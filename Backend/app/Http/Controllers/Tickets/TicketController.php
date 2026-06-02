<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use App\Services\FirebaseNotificationService;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * TicketController — handles the Backend admin ticket management UI.
 * Provides ticket inbox, detail view, message sending, image uploads,
 * and ticket lifecycle actions (close, reopen, update status).
 * Property-scoped: site users only see tickets for their property.
 */
class TicketController extends Controller
{
    protected TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Display ticket inbox with filterable list.
     * Property-scoped for site users, all tickets visible for HO/admin.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        /** Build base query with eager loading */
        $query = Ticket::with(['user', 'category', 'property', 'transaction.room'])
            ->orderBy('last_message_at', 'desc')
            ->orderBy('created_at', 'desc');

        /** Property scoping: site users only see their property's tickets */
        if (!$user->canViewAllProperties() && $user->property_id) {
            $query->where('property_id', $user->property_id);
        }

        /** HQ CS users also see HQ tickets */
        if ($user->user_type === 0 && $user->role && $user->role->name === 'Customer Service') {
            if ($user->canViewAllProperties()) {
                /** HQ CS with full access: see all tickets */
            } else {
                $query->where(function ($q) use ($user) {
                    $q->where('property_id', $user->property_id)
                      ->orWhere('recipient_type', 'hq');
                });
            }
        }

        /** Status filter */
        if ($request->filled('status')) {
            $query->where('ticket_status', $request->status);
        }

        /** Type filter */
        if ($request->filled('type')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('ticket_type', $request->type);
            });
        }

        /** Recipient type filter */
        if ($request->filled('recipient')) {
            $query->where('recipient_type', $request->recipient);
        }

        /** Search filter: ticket number, subject, user name/email */
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('order_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->paginate($request->input('per_page', 25));

        /** Add unread count per ticket for the current user */
        $tickets->each(function ($ticket) use ($user) {
            $ticket->unread_count = $ticket->getUnreadCountForUser($user->id);
        });

        /** Get categories for filter dropdown */
        $categories = TicketCategory::active()->get();

        $openTicketId = $request->input('open');

        return view('pages.tickets.index', compact('tickets', 'categories', 'openTicketId'));
    }

    /**
     * AJAX filter endpoint — returns partial HTML for ticket list + pagination.
     */
    public function filter(Request $request)
    {
        $user = Auth::user();

        $query = Ticket::with(['user', 'category', 'property', 'transaction.room'])
            ->orderBy('last_message_at', 'desc')
            ->orderBy('created_at', 'desc');

        if (!$user->canViewAllProperties() && $user->property_id) {
            $query->where('property_id', $user->property_id);
        }

        if ($request->filled('status')) {
            $query->where('ticket_status', $request->status);
        }
        if ($request->filled('type')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('ticket_type', $request->type);
            });
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('order_id', 'like', "%{$search}%");
            });
        }

        $tickets = $query->paginate(25);
        $tickets->each(function ($ticket) use ($user) {
            $ticket->unread_count = $ticket->getUnreadCountForUser($user->id);
        });

        return response()->json([
            'html' => view('pages.tickets.partials.ticket-list', compact('tickets'))->render(),
            'pagination' => $tickets->links()->toHtml(),
            'total' => $tickets->total(),
        ]);
    }

    /**
     * Show ticket detail with messages — returns JSON for AJAX requests.
     */
    public function show(Request $request, $id)
    {
        $user = Auth::user();
        $ticket = Ticket::with(['user', 'category', 'property', 'transaction.room'])->findOrFail($id);

        /** Property access check */
        if (!$user->canViewAllProperties() && $ticket->property_id && $ticket->property_id != $user->property_id) {
            if ($ticket->recipient_type !== 'hq') {
                abort(403, 'You do not have access to this ticket.');
            }
        }

        /** Get messages with sender and attachments */
        $messages = $ticket->messages()
            ->with(['sender', 'attachments'])
            ->orderBy('created_at', 'asc')
            ->get();

        /** Mark ticket as read by current user */
        $ticket->markAsReadByUser($user->id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'ticket' => $ticket,
                'messages' => $messages,
            ]);
        }

        return view('pages.tickets.index', [
            'tickets' => Ticket::with(['user', 'category', 'property'])->orderBy('last_message_at', 'desc')->paginate(25),
            'categories' => TicketCategory::active()->get(),
            'openTicketId' => $id,
        ]);
    }

    /**
     * Send a text message to a ticket.
     * Auto-transitions ticket to 'in_progress' if staff responds for the first time.
     * Sends FCM push notification to the ticket creator (customer).
     */
    public function sendMessage(Request $request, $ticketId)
    {
        $request->validate([
            'message_text' => 'required|string|max:5000',
        ]);

        $user = Auth::user();
        $ticket = Ticket::findOrFail($ticketId);

        /** Property access check */
        if (!$user->canViewAllProperties() && $ticket->property_id && $ticket->property_id != $user->property_id) {
            abort(403);
        }

        /** Create the message */
        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => $user->id,
            'message_text' => $request->message_text,
            'message_type' => 'text',
            'created_by' => (string) $user->id,
            'updated_by' => (string) $user->id,
        ]);

        /** Auto-transition to in_progress when staff first responds */
        if (in_array($ticket->ticket_status, ['open', 'reopened'])) {
            $this->ticketService->markInProgress($ticket->id, $user->id);
        }

        /** Send FCM push notification to the ticket creator */
        try {
            $fcmService = new FirebaseNotificationService();
            $senderName = $user->first_name ?? $user->name ?? 'Staff';
            $fcmService->sendToUser(
                $ticket->user,
                $senderName . ' - ' . $ticket->ticket_number,
                mb_strlen($request->message_text) > 200
                    ? mb_substr($request->message_text, 0, 200) . '...'
                    : $request->message_text,
                [
                    'type' => 'ticket_message',
                    'ticket_id' => (string) $ticket->id,
                    'conversation_id' => (string) $ticket->id,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Ticket FCM notification failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => $message->load('sender', 'attachments'),
        ]);
    }

    /**
     * Upload an image to a ticket.
     * Converts HEIC/HEIF to JPEG if Imagick is available.
     * Creates a message with the image attachment.
     */
    public function uploadImage(Request $request, $ticketId)
    {
        $request->validate([
            'image' => 'required|file|max:10240',
            'caption' => 'nullable|string|max:5000',
        ]);

        $user = Auth::user();
        $ticket = Ticket::findOrFail($ticketId);

        if (!$user->canViewAllProperties() && $ticket->property_id && $ticket->property_id != $user->property_id) {
            abort(403);
        }

        $file = $request->file('image');
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();
        $originalName = $file->getClientOriginalName();

        /** Convert HEIC/HEIF to JPEG if possible */
        if (in_array($extension, ['heic', 'heif']) || in_array($mimeType, ['image/heic', 'image/heif'])) {
            if (extension_loaded('imagick')) {
                try {
                    $imagick = new \Imagick();
                    $imagick->readImage($file->getPathname());
                    $imagick->setImageFormat('jpeg');
                    $imagick->setImageCompressionQuality(85);
                    $convertedPath = tempnam(sys_get_temp_dir(), 'heic_') . '.jpg';
                    $imagick->writeImage($convertedPath);
                    $imagick->destroy();

                    $extension = 'jpg';
                    $mimeType = 'image/jpeg';
                    $originalName = pathinfo($originalName, PATHINFO_FILENAME) . '.jpg';
                    /** Use the converted file for storage */
                    $storagePath = 'ticket_attachments/' . $ticket->id;
                    $fileName = time() . '_' . $originalName;
                    Storage::disk('public')->putFileAs($storagePath, new \Illuminate\Http\File($convertedPath), $fileName);
                    unlink($convertedPath);
                } catch (\Exception $e) {
                    Log::error('HEIC conversion failed: ' . $e->getMessage());
                    return response()->json(['success' => false, 'message' => 'HEIC conversion failed. Please upload JPG or PNG.'], 422);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'HEIC format not supported. Please upload JPG or PNG.'], 422);
            }
        } else {
            /** Store non-HEIC images directly */
            $storagePath = 'ticket_attachments/' . $ticket->id;
            $fileName = time() . '_' . $originalName;
            $file->storeAs('public/' . $storagePath, $fileName);
        }

        /** Create thumbnail for images */
        $thumbnailPath = null;
        $fullPath = storage_path('app/public/' . $storagePath . '/' . $fileName);
        if (str_starts_with($mimeType, 'image/') && file_exists($fullPath)) {
            try {
                $thumbDir = 'ticket_attachments/' . $ticket->id . '/thumbnails';
                Storage::disk('public')->makeDirectory($thumbDir);
                $thumbName = 'thumb_' . $fileName;

                if (extension_loaded('gd')) {
                    $source = imagecreatefromstring(file_get_contents($fullPath));
                    if ($source) {
                        $width = imagesx($source);
                        $height = imagesy($source);
                        $ratio = min(200 / $width, 200 / $height);
                        $newWidth = (int) ($width * $ratio);
                        $newHeight = (int) ($height * $ratio);
                        $thumb = imagecreatetruecolor($newWidth, $newHeight);
                        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        $thumbFullPath = storage_path('app/public/' . $thumbDir . '/' . $thumbName);
                        imagejpeg($thumb, $thumbFullPath, 80);
                        imagedestroy($source);
                        imagedestroy($thumb);
                        $thumbnailPath = $thumbDir . '/' . $thumbName;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Thumbnail generation failed: ' . $e->getMessage());
            }
        }

        /** Create message + attachment records */
        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => $user->id,
            'message_text' => $request->caption,
            'message_type' => 'image',
            'created_by' => (string) $user->id,
            'updated_by' => (string) $user->id,
        ]);

        TicketAttachment::create([
            'message_id' => $message->id,
            'file_name' => $originalName,
            'file_path' => $storagePath . '/' . $fileName,
            'file_type' => $mimeType,
            'file_size' => filesize($fullPath),
            'thumbnail_path' => $thumbnailPath,
            'created_by' => (string) $user->id,
            'updated_by' => (string) $user->id,
        ]);

        /** Auto-transition to in_progress when staff first responds */
        if (in_array($ticket->ticket_status, ['open', 'reopened'])) {
            $this->ticketService->markInProgress($ticket->id, $user->id);
        }

        /** Send FCM push notification to ticket creator */
        try {
            $fcmService = new FirebaseNotificationService();
            $senderName = $user->first_name ?? $user->name ?? 'Staff';
            $fcmService->sendToUser(
                $ticket->user,
                $senderName . ' - ' . $ticket->ticket_number,
                '📷 ' . ($request->caption ?? 'Sent an image'),
                [
                    'type' => 'ticket_message',
                    'ticket_id' => (string) $ticket->id,
                    'conversation_id' => (string) $ticket->id,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Ticket FCM notification failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => $message->load('sender', 'attachments'),
        ]);
    }

    /**
     * Close a ticket — creates system message and notifies customer.
     */
    public function closeTicket(Request $request, $ticketId)
    {
        $user = Auth::user();
        $ticket = $this->ticketService->closeTicket($ticketId, $user->id);

        /** Notify customer that ticket was closed */
        try {
            $fcmService = new FirebaseNotificationService();
            $fcmService->sendToUser(
                $ticket->user,
                __('ui.ticket_closed_title'),
                __('ui.ticket_closed_body', ['number' => $ticket->ticket_number]),
                [
                    'type' => 'ticket_closed',
                    'ticket_id' => (string) $ticket->id,
                    'conversation_id' => (string) $ticket->id,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Ticket close notification failed: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'ticket' => $ticket]);
    }

    /**
     * Reopen a closed ticket — within 7-day reopen window.
     */
    public function reopenTicket(Request $request, $ticketId)
    {
        $user = Auth::user();

        try {
            $ticket = $this->ticketService->reopenTicket($ticketId, $user->id);
            return response()->json(['success' => true, 'ticket' => $ticket]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Update ticket status to in_progress.
     */
    public function updateStatus(Request $request, $ticketId)
    {
        $user = Auth::user();
        $ticket = $this->ticketService->markInProgress($ticketId, $user->id);
        return response()->json(['success' => true, 'ticket' => $ticket]);
    }

    /**
     * Get total unread ticket count for the current user (for sidebar badge).
     */
    public function getUnreadCount(Request $request)
    {
        $user = Auth::user();

        $query = Ticket::open();

        /** Property scoping */
        if (!$user->canViewAllProperties() && $user->property_id) {
            $query->where('property_id', $user->property_id);
        }

        $tickets = $query->get();
        $totalUnread = $tickets->sum(function ($ticket) use ($user) {
            return $ticket->getUnreadCountForUser($user->id);
        });

        return response()->json(['unread_count' => $totalUnread]);
    }

    /**
     * GET /tickets/eligible-bookings — returns eligible bookings for admin ticket creation.
     * HQ admin sees all properties, site admin sees only their property.
     */
    public function getEligibleBookings(Request $request)
    {
        $user = Auth::user();
        $propertyId = $user->canViewAllProperties() ? null : $user->property_id;

        $bookings = $this->ticketService->getEligibleBookingsForAdmin($propertyId);

        /** Apply search filter if provided */
        $search = $request->input('search');
        if ($search) {
            $search = strtolower($search);
            $bookings = $bookings->filter(function ($t) use ($search) {
                return str_contains(strtolower($t->user_name ?? ''), $search)
                    || str_contains(strtolower($t->order_id ?? ''), $search)
                    || str_contains(strtolower($t->property_name ?? ''), $search)
                    || str_contains(strtolower($t->room_name ?? ''), $search);
            });
        }

        /** Sort by property name, then room name ascending */
        $sorted = $bookings->sortBy([
            ['property_name', 'asc'],
            ['room_name', 'asc'],
        ])->values();

        /** Determine booking status: checked-in (actual), checked-out (actual), active (scheduled), or upcoming */
        $now = \Carbon\Carbon::now();
        $formatted = $sorted->map(function ($t) use ($now) {
            $scheduledIn = $t->check_in ? \Carbon\Carbon::parse($t->check_in) : null;
            $scheduledOut = $t->check_out ? \Carbon\Carbon::parse($t->check_out) : null;

            // Use actual check-in/out from t_booking for status
            if ($t->check_out_at) {
                $status = 'checked_out';
            } elseif ($t->check_in_at) {
                $status = 'checked_in';
            } elseif ($scheduledIn && $now->gte($scheduledIn)) {
                $status = 'active';
            } else {
                $status = 'upcoming';
            }

            return [
                'order_id' => $t->order_id,
                'user_name' => $t->user_name,
                'property_name' => $t->property_name,
                'room_no' => $t->room_no ?? '-',
                'room_name' => $t->room_name,
                'check_in' => $scheduledIn ? $scheduledIn->format('d M Y') : '-',
                'check_out' => $scheduledOut ? $scheduledOut->format('d M Y') : '-',
                'status' => $status,
            ];
        });

        return response()->json(['success' => true, 'data' => $formatted]);
    }

    /**
     * POST /tickets/store — admin creates a new ticket (Notice) for a booking customer.
     */
    public function storeAdminTicket(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'category_id' => 'required|integer|exists:t_ticket_categories,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        try {
            $user = Auth::user();

            $ticket = $this->ticketService->createAdminTicket(
                adminId: $user->id,
                categoryId: $request->category_id,
                orderId: $request->order_id,
                subject: $request->subject,
                initialMessage: $request->message,
            );

            /** Send FCM push notification to the customer */
            try {
                $fcm = app(FirebaseNotificationService::class);
                $customer = \App\Models\User::find($ticket->user_id);
                $fcm->sendToUser($customer, $request->subject, $request->message, [
                    'type' => 'ticket_created',
                    'ticket_id' => (string) $ticket->id,
                ]);
            } catch (\Exception $e) {
                Log::warning('FCM notification failed for admin ticket: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully.',
                'ticket_id' => $ticket->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Admin ticket creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket: ' . $e->getMessage(),
            ], 500);
        }
    }
}
