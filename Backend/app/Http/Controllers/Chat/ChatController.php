<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ChatAttachment;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * Display chat inbox with conversation list
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get conversations with property scoping
        $conversations = $this->filterConversations($request)->paginate(
            $request->input('per_page', 20)
        );

        // Add unread count to each conversation
        $conversations->each(function($conversation) use ($user) {
            $conversation->unread_count = $conversation->getUnreadCountForUser($user->id);
        });

        $openConversationId = $request->input('open');

        return view('pages.chat.index', compact('conversations', 'openConversationId'));
    }

    /**
     * Filter conversations via AJAX
     */
    public function filter(Request $request)
    {
        $user = Auth::user();
        $conversations = $this->filterConversations($request)->paginate(20);

        // Add unread count to each conversation
        $conversations->each(function($conversation) use ($user) {
            $conversation->unread_count = $conversation->getUnreadCountForUser($user->id);
        });

        return response()->json([
            'table' => view('pages.chat.partials.conversation-list', compact('conversations'))->render(),
            'pagination' => $conversations->links()->toHtml()
        ]);
    }

    /**
     * Show single conversation chat window
     */
    public function show(Request $request, $id)
    {
        $user = Auth::user();

        $conversation = ChatConversation::with(['transaction', 'property', 'participants.user'])
            ->findOrFail($id);

        // Check property access
        if (!$user->canViewAllProperties() && $conversation->property_id != $user->property_id) {
            abort(403, 'Anda tidak memiliki akses ke conversation ini.');
        }

        // Get messages
        $messages = $conversation->messages()
            ->with(['sender', 'attachments'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark as read
        $conversation->markAsReadByUser($user->id);

        // Get booking data for user info
        $booking = Booking::with(['room', 'transaction'])
            ->where('order_id', $conversation->order_id)
            ->first();

        // Return JSON for AJAX requests (widget)
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'conversation' => $conversation,
                'messages' => $messages,
                'booking' => $booking
            ]);
        }

        // For direct browser navigation, redirect to index with ?open=id
        return redirect()->route('chat.index', ['open' => $id]);
    }

    /**
     * Get conversations as JSON for widget
     */
    public function getConversationsJson(Request $request)
    {
        $user = Auth::user();

        $conversations = $this->filterConversations($request)
            ->with(['messages' => function($q) {
                $q->latest()->limit(1);
            }, 'messages.sender'])
            ->limit(50)
            ->get();

        // Add unread count to each conversation
        $conversations->each(function($conversation) use ($user) {
            $conversation->unread_count = $conversation->getUnreadCountForUser($user->id);
        });

        $totalUnread = $conversations->sum('unread_count');

        return response()->json([
            'success' => true,
            'data' => $conversations,
            'total_unread' => $totalUnread
        ]);
    }

    /**
     * Create new conversation
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:t_transactions,order_id',
            'initial_message' => 'nullable|string',
        ]);

        $user = Auth::user();
        $transaction = Transaction::where('order_id', $request->order_id)->first();

        // Check property access
        if (!$user->canViewAllProperties() && $transaction->property_id != $user->property_id) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk membuat conversation ini.'
                ], 403);
            }
            abort(403, 'Anda tidak memiliki akses untuk membuat conversation ini.');
        }

        // Check if conversation already exists
        $existingConversation = ChatConversation::where('order_id', $request->order_id)->first();

        if ($existingConversation) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'conversation_id' => $existingConversation->id,
                    'message' => 'Conversation untuk booking ini sudah ada.',
                    'already_exists' => true
                ]);
            }
            return redirect()->route('chat.index', ['open' => $existingConversation->id])
                ->with('info', 'Conversation untuk booking ini sudah ada.');
        }

        // Create conversation
        $conversation = ChatConversation::create([
            'order_id' => $request->order_id,
            'property_id' => $transaction->property_id,
            'title' => 'Chat untuk Booking ' . $request->order_id,
            'status' => 'active',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // Add current user as participant
        $conversation->addParticipant($user->id, 'staff');

        // Add transaction user as participant if exists
        if ($transaction->user_id) {
            $conversation->addParticipant($transaction->user_id, 'customer');
        }

        // Send initial message if provided
        if ($request->initial_message) {
            ChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'message_text' => $request->initial_message,
                'message_type' => 'text',
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            $conversation->update(['last_message_at' => now()]);
        }

        // Return JSON for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'message' => 'Conversation berhasil dibuat.'
            ]);
        }

        return redirect()->route('chat.index', ['open' => $conversation->id])
            ->with('success', 'Conversation berhasil dibuat.');
    }

    /**
     * Send message to conversation
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'message_text' => 'required|string',
        ]);

        $user = Auth::user();
        $conversation = ChatConversation::findOrFail($conversationId);

        // Check property access
        if (!$user->canViewAllProperties() && $conversation->property_id != $user->property_id) {
            abort(403, 'Anda tidak memiliki akses ke conversation ini.');
        }

        // Create message
        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message_text' => $request->message_text,
            'message_type' => 'text',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // Update conversation last_message_at
        $conversation->update(['last_message_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => $message->load('sender', 'attachments')
        ]);
    }

    /**
     * Build conversations query with filters
     */
    protected function filterConversations(Request $request)
    {
        $user = Auth::user();

        $query = ChatConversation::with(['transaction.user', 'property', 'participants', 'booking.room'])
            ->withCount(['messages'])
            ->orderBy('last_message_at', 'desc')
            ->orderBy('created_at', 'desc');

        // Property scoping
        if (!$user->canViewAllProperties() && $user->property_id) {
            $query->where('property_id', $user->property_id);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('booking', function ($bq) use ($search) {
                      $bq->where('user_name', 'like', "%{$search}%")
                         ->orWhere('user_email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('transaction.user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $query;
    }

    /**
     * Get list of checked-in users for creating conversations
     * Includes: currently checked-in users AND departed users within 7 days after checkout
     */
    public function getCheckedInUsers(Request $request)
    {
        $user = Auth::user();

        // Query bookings with property scoping
        // Include: checked-in users OR departed users within 7 days after checkout
        $query = Booking::with(['transaction', 'room', 'property'])
            ->whereHas('transaction', function($q) {
                $q->where('transaction_status', '=', 'paid');
            })
            ->whereNotNull('t_booking.check_in_at')
            ->where(function($q) {
                // Currently checked-in (check_out_at is null)
                $q->whereNull('t_booking.check_out_at')
                  // OR departed within 7 days after checkout
                  ->orWhere(function($subQ) {
                      $subQ->whereNotNull('t_booking.check_out_at')
                           ->where('t_booking.check_out_at', '>=', now()->subDays(7));
                  });
            });

        // Property scoping
        if (!$user->canViewAllProperties() && $user->property_id) {
            $query->where('t_booking.property_id', $user->property_id);
        }

        $bookings = $query->orderBy('t_booking.check_in_at', 'desc')->get();

        // Format data for frontend
        $data = $bookings->map(function($booking) {
            // Check if conversation already exists
            $hasConversation = ChatConversation::where('order_id', $booking->order_id)->exists();

            // Safely format check_in_at
            $checkInFormatted = 'N/A';
            if ($booking->check_in_at) {
                try {
                    $checkInFormatted = $booking->check_in_at->format('Y-m-d H:i');
                } catch (\Exception $e) {
                    $checkInFormatted = $booking->check_in_at;
                }
            }

            // Safely format check_out_at
            $checkOutFormatted = null;
            if ($booking->check_out_at) {
                try {
                    $checkOutFormatted = $booking->check_out_at->format('Y-m-d H:i');
                } catch (\Exception $e) {
                    $checkOutFormatted = $booking->check_out_at;
                }
            }

            // Determine status
            $status = 'checked_in';
            if ($booking->check_out_at) {
                $status = 'departed';
            }

            return [
                'order_id' => $booking->order_id,
                'user_name' => $booking->user_name ?? 'N/A',
                'user_email' => $booking->user_email ?? 'N/A',
                'room_name' => $booking->room->name ?? 'N/A',
                'property_name' => $booking->property->name ?? 'N/A',
                'check_in_at' => $checkInFormatted,
                'check_out_at' => $checkOutFormatted,
                'status' => $status,
                'has_conversation' => $hasConversation,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Upload image attachment to conversation
     */
    public function uploadImage(Request $request, $conversationId)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB
            'message_text' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $conversation = ChatConversation::findOrFail($conversationId);

        // Check property access
        if (!$user->canViewAllProperties() && $conversation->property_id != $user->property_id) {
            abort(403, 'Anda tidak memiliki akses ke conversation ini.');
        }

        // Create message first
        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message_text' => $request->message_text ?? '',
            'message_type' => 'image',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // Handle file upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'chat_img_' . $message->id . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Store in public/chat-attachments/{conversation_id}/
            $path = $file->storeAs(
                "chat-attachments/{$conversation->id}",
                $filename,
                'public'
            );

            // Create attachment record
            ChatAttachment::create([
                'message_id' => $message->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'attachment_type' => 'other',
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
        }

        // Update conversation last_message_at
        $conversation->update(['last_message_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => $message->load('sender', 'attachments')
        ]);
    }

    /**
     * Edit a message
     */
    public function editMessage(Request $request, $id)
    {
        $request->validate([
            'message_text' => 'required|string|max:5000',
        ]);

        $user = Auth::user();
        $message = ChatMessage::with('conversation')->findOrFail($id);

        // Check if user is the sender
        if ($message->sender_id != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only edit your own messages.'
            ], 403);
        }

        // Check property access
        if (!$user->canViewAllProperties() && $message->conversation->property_id != $user->property_id) {
            abort(403, 'Anda tidak memiliki akses ke conversation ini.');
        }

        // Update message
        $message->update([
            'message_text' => $request->message_text,
            'is_edited' => true,
            'edited_at' => now(),
            'updated_by' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => $message->load('sender', 'attachments')
        ]);
    }

    /**
     * Get total unread count for current user
     */
    public function getUnreadCount(Request $request)
    {
        $user = Auth::user();

        // Get all conversations for the user
        $query = ChatConversation::query();

        // Property scoping
        if (!$user->canViewAllProperties() && $user->property_id) {
            $query->where('property_id', $user->property_id);
        }

        $conversations = $query->get();

        // Calculate total unread
        $totalUnread = $conversations->sum(function($conversation) use ($user) {
            return $conversation->getUnreadCountForUser($user->id);
        });

        return response()->json([
            'success' => true,
            'unread_count' => $totalUnread
        ]);
    }

    /**
     * Find conversation by order_id
     */
    public function findByOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);

        $user = Auth::user();

        // Find conversation by order_id
        $conversation = ChatConversation::where('order_id', $request->order_id)->first();

        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation tidak ditemukan.'
            ], 404);
        }

        // Check property access
        if (!$user->canViewAllProperties() && $conversation->property_id != $user->property_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke conversation ini.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id
        ]);
    }
}
