<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatAttachment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ChatApiController extends Controller
{
    /**
     * Get user's conversations list
     * GET /api/chat/conversations
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = ChatConversation::with(['transaction', 'property', 'participants.user'])
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->withCount(['messages'])
            ->orderBy('last_message_at', 'desc')
            ->orderBy('created_at', 'desc');

        // Property scoping
        $this->applyPropertyScoping($query, $user);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $conversations = $query->paginate($request->input('per_page', 20));

        // Add unread count for each conversation
        $conversations->getCollection()->transform(function ($conversation) use ($user) {
            $conversation->unread_count = $conversation->getUnreadCountForUser($user->id);
            return $conversation;
        });

        return response()->json([
            'success' => true,
            'data' => $conversations->items(),
            'meta' => [
                'current_page' => $conversations->currentPage(),
                'last_page' => $conversations->lastPage(),
                'per_page' => $conversations->perPage(),
                'total' => $conversations->total(),
            ]
        ]);
    }

    /**
     * Create new conversation
     * POST /api/chat/conversations
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:t_transactions,order_id',
            'initial_message' => 'nullable|string',
        ]);

        $user = Auth::user();
        $transaction = Transaction::where('order_id', $request->order_id)->firstOrFail();

        // Check property access
        if (!$user->canViewAllProperties() && $transaction->property_id != $user->property_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk membuat conversation ini.'
            ], 403);
        }

        // Check if conversation already exists
        $existingConversation = ChatConversation::where('order_id', $request->order_id)->first();

        if ($existingConversation) {
            return response()->json([
                'success' => true,
                'message' => 'Conversation sudah ada',
                'conversation' => $existingConversation->load(['transaction', 'property', 'participants.user']),
            ]);
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

        // Add participants
        $conversation->addParticipant($user->id, $user->isSiteRole() ? 'staff' : 'customer');

        if ($transaction->user_id && $transaction->user_id != $user->id) {
            $conversation->addParticipant($transaction->user_id, 'customer');
        }

        // Send initial message if provided
        $message = null;
        if ($request->filled('initial_message')) {
            $message = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'message_text' => $request->initial_message,
                'message_type' => 'text',
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            $conversation->update(['last_message_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Conversation berhasil dibuat',
            'conversation' => $conversation->load(['transaction', 'property', 'participants.user']),
            'initial_message' => $message,
        ], 201);
    }

    /**
     * Get conversation details
     * GET /api/chat/conversations/{id}
     */
    public function show($id)
    {
        $user = Auth::user();

        $conversation = ChatConversation::with(['transaction', 'property', 'participants.user'])
            ->findOrFail($id);

        // Check if user is participant
        if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda bukan participant dari conversation ini.'
            ], 403);
        }

        // Check property access
        $this->checkPropertyAccess($conversation, $user);

        return response()->json([
            'success' => true,
            'conversation' => $conversation,
            'participants' => $conversation->participants()->with('user')->get(),
        ]);
    }

    /**
     * Update conversation
     * PUT /api/chat/conversations/{id}
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,archived,closed',
        ]);

        $user = Auth::user();
        $conversation = ChatConversation::findOrFail($id);

        // Check access
        $this->checkPropertyAccess($conversation, $user);

        $conversation->update(array_merge(
            $request->only(['title', 'status']),
            ['updated_by' => $user->id]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Conversation berhasil diupdate',
            'conversation' => $conversation->fresh(),
        ]);
    }

    /**
     * Archive conversation
     * DELETE /api/chat/conversations/{id}
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $conversation = ChatConversation::findOrFail($id);

        // Check access
        $this->checkPropertyAccess($conversation, $user);

        $conversation->update([
            'status' => 'archived',
            'updated_by' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Conversation berhasil diarchive',
        ]);
    }

    /**
     * Get conversation messages
     * GET /api/chat/conversations/{id}/messages
     */
    public function getMessages(Request $request, $conversationId)
    {
        $user = Auth::user();
        $conversation = ChatConversation::findOrFail($conversationId);

        // Check if user is participant
        if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda bukan participant dari conversation ini.'
            ], 403);
        }

        // Check property access
        $this->checkPropertyAccess($conversation, $user);

        $messages = $conversation->messages()
            ->with(['sender', 'attachments'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 50));

        return response()->json([
            'success' => true,
            'data' => array_reverse($messages->items()), // Reverse to show oldest first
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ]
        ]);
    }

    /**
     * Send message
     * POST /api/chat/conversations/{id}/messages
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'message_text' => 'required|string',
            'message_type' => 'nullable|in:text,file,image,system',
        ]);

        $user = Auth::user();
        $conversation = ChatConversation::findOrFail($conversationId);

        // Check if user is participant
        if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda bukan participant dari conversation ini.'
            ], 403);
        }

        // Check property access
        $this->checkPropertyAccess($conversation, $user);

        // Create message
        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message_text' => $request->message_text,
            'message_type' => $request->input('message_type', 'text'),
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // Update conversation last_message_at
        $conversation->update(['last_message_at' => now()]);

        // TODO: Broadcast MessageSent event

        return response()->json([
            'success' => true,
            'message' => $message->load(['sender', 'attachments']),
        ], 201);
    }

    /**
     * Mark message as read
     * POST /api/chat/messages/{id}/read
     */
    public function markAsRead($messageId)
    {
        $user = Auth::user();
        $message = ChatMessage::findOrFail($messageId);

        $message->markAsReadBy($user->id);

        // TODO: Broadcast MessageRead event

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read',
        ]);
    }

    /**
     * Mark all messages in conversation as read
     * POST /api/chat/conversations/{id}/read-all
     */
    public function markAllAsRead($conversationId)
    {
        $user = Auth::user();
        $conversation = ChatConversation::findOrFail($conversationId);

        // Check if user is participant
        if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda bukan participant dari conversation ini.'
            ], 403);
        }

        $conversation->markAsReadByUser($user->id);

        return response()->json([
            'success' => true,
            'message' => 'All messages marked as read',
        ]);
    }

    /**
     * Upload attachment
     * POST /api/chat/messages/{id}/attachments
     */
    public function uploadAttachment(Request $request, $messageId)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB
            'attachment_type' => 'required|in:ktp,payment_proof,room_photo,document,other',
        ]);

        $user = Auth::user();
        $message = ChatMessage::findOrFail($messageId);
        $conversation = $message->conversation;

        // Check property access
        $this->checkPropertyAccess($conversation, $user);

        $file = $request->file('file');
        $type = $request->attachment_type;

        // Store file
        $path = $file->store("public/chat-attachments/{$conversation->id}/{$type}");

        // Generate thumbnail if image
        $thumbnailPath = null;
        if (str_starts_with($file->getMimeType(), 'image/')) {
            $thumbnailPath = $this->generateThumbnail($path);
        }

        // Create attachment record
        $attachment = ChatAttachment::create([
            'message_id' => $messageId,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'attachment_type' => $type,
            'thumbnail_path' => $thumbnailPath,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // TODO: Broadcast MessageSent event

        return response()->json([
            'success' => true,
            'attachment' => $attachment,
        ], 201);
    }

    /**
     * Set typing status
     * POST /api/chat/conversations/{id}/typing
     */
    public function setTypingStatus(Request $request, $conversationId)
    {
        $request->validate([
            'is_typing' => 'required|boolean',
        ]);

        $user = Auth::user();
        $conversation = ChatConversation::findOrFail($conversationId);

        // Check if user is participant
        if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda bukan participant dari conversation ini.'
            ], 403);
        }

        $key = "chat:typing:{$conversationId}:{$user->id}";

        if ($request->is_typing) {
            Redis::setex($key, 5, json_encode([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'timestamp' => now()->toISOString(),
            ]));
        } else {
            Redis::del($key);
        }

        // TODO: Broadcast UserTyping event

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Search messages
     * GET /api/chat/search
     */
    public function searchMessages(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
            'conversation_id' => 'nullable|exists:t_chat_conversations,id',
        ]);

        $user = Auth::user();

        $query = ChatMessage::with(['sender', 'conversation', 'attachments'])
            ->whereHas('conversation', function ($q) use ($user) {
                $q->whereHas('participants', function ($subQ) use ($user) {
                    $subQ->where('user_id', $user->id);
                });

                // Property scoping
                if (!$user->canViewAllProperties() && $user->property_id) {
                    $q->where('property_id', $user->property_id);
                }
            })
            ->searchText($request->q);

        // Filter by conversation if provided
        if ($request->filled('conversation_id')) {
            $query->where('conversation_id', $request->conversation_id);
        }

        $messages = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $messages->items(),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ]
        ]);
    }

    /**
     * Apply property scoping to query
     */
    protected function applyPropertyScoping($query, $user)
    {
        if (!$user->canViewAllProperties() && $user->property_id) {
            $query->where('property_id', $user->property_id);
        }
    }

    /**
     * Check property access
     */
    protected function checkPropertyAccess($conversation, $user)
    {
        if (!$user->canViewAllProperties() && $conversation->property_id != $user->property_id) {
            abort(403, 'Anda tidak memiliki akses ke conversation ini.');
        }
    }

    /**
     * Generate thumbnail for image
     */
    protected function generateThumbnail($originalPath)
    {
        try {
            $image = Image::make(storage_path('app/' . $originalPath));
            $image->fit(200, 200);

            $thumbnailPath = str_replace('chat-attachments/', 'chat-attachments/thumbnails/', $originalPath);
            $thumbnailDir = dirname(storage_path('app/' . $thumbnailPath));

            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            $image->save(storage_path('app/' . $thumbnailPath));

            return $thumbnailPath;
        } catch (\Exception $e) {
            \Log::error('Failed to generate thumbnail: ' . $e->getMessage());
            return null;
        }
    }
}
