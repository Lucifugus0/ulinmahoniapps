<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketBroadcast;
use App\Models\TicketBroadcastRead;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * TicketController — API endpoints for the customer service ticketing system.
 * Used by both the mobile app and the web portal.
 * Follows the existing ChatController pattern: user_id from request params,
 * standard {status, message, data} response format.
 */
class TicketController extends ApiController
{
    protected TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        parent::__construct();
        $this->ticketService = $ticketService;
    }

    /**
     * GET /api/v1/tickets/categories
     * List all active ticket categories grouped by type.
     */
    public function getCategories(Request $request)
    {
        try {
            $categories = TicketCategory::active()->get();

            /** Group by ticket_type for easier UI rendering */
            $grouped = $categories->groupBy('ticket_type')->map(function ($items, $type) {
                return $items->map(function ($cat) {
                    return [
                        'id' => $cat->id,
                        'ticket_type' => $cat->ticket_type,
                        'category' => $cat->category,
                        'label_en' => $cat->label_en,
                        'label_id' => $cat->label_id,
                        'label_zh' => $cat->label_zh,
                        'recipient_type' => $cat->recipient_type,
                        'requires_booking' => $cat->requires_booking,
                        'sort_order' => $cat->sort_order,
                    ];
                });
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Categories retrieved successfully',
                'data' => $grouped,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve categories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/v1/tickets/eligibility?user_id=&order_id=
     * Check if a user can create a ticket for a specific booking.
     */
    public function checkEligibility(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'order_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->ticketService->checkTicketEligibility(
            $request->input('user_id'),
            $request->input('order_id')
        );

        return response()->json([
            'status' => 'success',
            'message' => $result['reason'],
            'data' => $result,
        ]);
    }

    /**
     * GET /api/v1/tickets/eligible-bookings?user_id=
     * List bookings eligible for ticket creation.
     */
    public function getEligibleBookings(Request $request)
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'user_id is required'], 400);
        }

        try {
            $bookings = $this->ticketService->getEligibleBookings($userId);

            return response()->json([
                'status' => 'success',
                'message' => 'Eligible bookings retrieved',
                'data' => $bookings->map(function ($t) {
                    return [
                        'order_id' => $t->order_id,
                        'property_id' => $t->property_id,
                        'property_name' => $t->property_name,
                        'room_name' => $t->room_name,
                        'check_in' => $t->check_in?->toDateString(),
                        'check_out' => $t->check_out?->toDateString(),
                        'transaction_status' => $t->transaction_status,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/v1/tickets?user_id=&status=
     * List user's tickets with pagination and unread counts.
     */
    public function listTickets(Request $request)
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'user_id is required'], 400);
        }

        try {
            $query = Ticket::where('user_id', $userId)
                ->with(['category', 'property:idrec,name', 'transaction:idrec,order_id,room_name,property_name'])
                ->orderBy('last_message_at', 'desc');

            /** Filter by status */
            if ($request->filled('status')) {
                $query->where('ticket_status', $request->status);
            }

            $perPage = $request->input('per_page', 20);
            $tickets = $query->paginate($perPage);

            /** Add unread count per ticket */
            $ticketsData = $tickets->items();
            foreach ($ticketsData as $ticket) {
                $ticket->unread_count = $ticket->getUnreadCountForUser($userId);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Tickets retrieved successfully',
                'data' => $ticketsData,
                'meta' => [
                    'current_page' => $tickets->currentPage(),
                    'last_page' => $tickets->lastPage(),
                    'per_page' => $tickets->perPage(),
                    'total' => $tickets->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /api/v1/tickets
     * Create a new ticket with an initial message.
     */
    public function createTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'category_id' => 'required|integer|exists:t_ticket_categories,id',
            'order_id' => 'nullable|string',
            'subject' => 'required|string|max:255',
            'initial_message' => 'nullable|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $ticket = $this->ticketService->createTicket(
                $request->input('user_id'),
                $request->input('category_id'),
                $request->input('order_id'),
                $request->input('subject'),
                $request->input('initial_message')
            );

            $ticket->load(['category', 'property:idrec,name']);

            /** Send FCM notification to relevant staff */
            try {
                $fcmService = new FirebaseNotificationService();
                $user = User::find($request->input('user_id'));
                $userName = $user?->first_name ?? $user?->name ?? 'Customer';

                if ($ticket->recipient_type === 'front_desk' && $ticket->property_id) {
                    $staff = $this->ticketService->getStaffForProperty($ticket->property_id);
                } else {
                    $staff = $this->ticketService->getHQCSUsers();
                }

                foreach ($staff as $staffUser) {
                    $fcmService->sendToUser(
                        $staffUser,
                        'New Ticket: ' . $ticket->ticket_number,
                        $userName . ': ' . $ticket->subject,
                        [
                            'type' => 'ticket_created',
                            'ticket_id' => (string) $ticket->id,
                            'conversation_id' => (string) $ticket->id,
                        ]
                    );
                }
            } catch (\Exception $e) {
                Log::error('Ticket creation FCM failed: ' . $e->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Ticket created successfully',
                'data' => $ticket,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * GET /api/v1/tickets/{id}?user_id=
     * Get ticket detail with paginated messages.
     */
    public function getTicket(Request $request, $id)
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'user_id is required'], 400);
        }

        try {
            $ticket = Ticket::with(['category', 'property:idrec,name', 'transaction:idrec,order_id,room_name,property_name'])
                ->findOrFail($id);

            /** Access check: only ticket creator or admin can view */
            $user = User::find($userId);
            if ($ticket->user_id != $userId && (!$user || $user->is_admin != 1)) {
                return response()->json(['status' => 'error', 'message' => 'Access denied'], 403);
            }

            /** Get paginated messages */
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 50);
            $messages = $ticket->messages()
                ->with(['sender:id,first_name,last_name,email,name', 'attachments'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            /** Mark as read */
            $ticket->markAsReadByUser($userId);

            /** Reverse messages so oldest first (like chat) */
            $messagesData = array_reverse($messages->items());

            return response()->json([
                'status' => 'success',
                'message' => 'Ticket retrieved successfully',
                'data' => [
                    'ticket' => $ticket,
                    'messages' => $messagesData,
                    'can_reopen' => $ticket->canBeReopened(),
                ],
                'meta' => [
                    'current_page' => $messages->currentPage(),
                    'last_page' => $messages->lastPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /api/v1/tickets/{id}/messages
     * Send a text or image message to a ticket.
     */
    public function sendMessage(Request $request, $id)
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'user_id is required'], 400);
        }

        $ticket = Ticket::findOrFail($id);

        /** Access check */
        $user = User::find($userId);
        if ($ticket->user_id != $userId && (!$user || $user->is_admin != 1)) {
            return response()->json(['status' => 'error', 'message' => 'Access denied'], 403);
        }

        /** Closed ticket check */
        if ($ticket->ticket_status === 'closed') {
            return response()->json(['status' => 'error', 'message' => 'Cannot send messages to a closed ticket'], 422);
        }

        try {
            /** Handle image upload */
            if ($request->hasFile('image')) {
                $validator = Validator::make($request->all(), [
                    'image' => 'required|file|max:10240',
                    'caption' => 'nullable|string|max:5000',
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
                }

                $file = $request->file('image');
                $extension = strtolower($file->getClientOriginalExtension());
                $mimeType = $file->getMimeType();
                $originalName = $file->getClientOriginalName();

                /** Convert HEIC/HEIF to JPEG if Imagick available */
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
                            $storagePath = 'ticket_attachments/' . $ticket->id;
                            $fileName = time() . '_' . $originalName;
                            Storage::disk('public')->putFileAs($storagePath, new \Illuminate\Http\File($convertedPath), $fileName);
                            unlink($convertedPath);
                        } catch (\Exception $e) {
                            return response()->json(['status' => 'error', 'message' => 'HEIC conversion failed. Please upload JPG or PNG.'], 422);
                        }
                    } else {
                        return response()->json(['status' => 'error', 'message' => 'HEIC not supported. Please upload JPG or PNG.'], 422);
                    }
                } else {
                    $storagePath = 'ticket_attachments/' . $ticket->id;
                    $fileName = time() . '_' . $originalName;
                    $file->storeAs('public/' . $storagePath, $fileName);
                }

                /** Generate thumbnail */
                $thumbnailPath = null;
                $fullPath = storage_path('app/public/' . $storagePath . '/' . $fileName);
                if (str_starts_with($mimeType, 'image/') && file_exists($fullPath) && extension_loaded('gd')) {
                    try {
                        $thumbDir = 'ticket_attachments/' . $ticket->id . '/thumbnails';
                        Storage::disk('public')->makeDirectory($thumbDir);
                        $thumbName = 'thumb_' . $fileName;
                        $source = imagecreatefromstring(file_get_contents($fullPath));
                        if ($source) {
                            $w = imagesx($source);
                            $h = imagesy($source);
                            $ratio = min(200 / $w, 200 / $h);
                            $thumb = imagecreatetruecolor((int)($w * $ratio), (int)($h * $ratio));
                            imagecopyresampled($thumb, $source, 0, 0, 0, 0, (int)($w * $ratio), (int)($h * $ratio), $w, $h);
                            imagejpeg($thumb, storage_path('app/public/' . $thumbDir . '/' . $thumbName), 80);
                            imagedestroy($source);
                            imagedestroy($thumb);
                            $thumbnailPath = $thumbDir . '/' . $thumbName;
                        }
                    } catch (\Exception $e) {
                        Log::warning('Thumbnail failed: ' . $e->getMessage());
                    }
                }

                /** Create image message + attachment */
                $message = TicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'sender_id' => $userId,
                    'message_text' => $request->input('caption'),
                    'message_type' => 'image',
                    'created_by' => (string) $userId,
                    'updated_by' => (string) $userId,
                ]);

                TicketAttachment::create([
                    'message_id' => $message->id,
                    'file_name' => $originalName,
                    'file_path' => $storagePath . '/' . $fileName,
                    'file_type' => $mimeType,
                    'file_size' => filesize($fullPath),
                    'thumbnail_path' => $thumbnailPath,
                    'created_by' => (string) $userId,
                ]);
            } else {
                /** Text message */
                $validator = Validator::make($request->all(), [
                    'message_text' => 'required|string|max:5000',
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
                }

                $message = TicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'sender_id' => $userId,
                    'message_text' => $request->input('message_text'),
                    'message_type' => 'text',
                    'created_by' => (string) $userId,
                    'updated_by' => (string) $userId,
                ]);
            }

            $message->load(['sender:id,first_name,last_name,email,name', 'attachments']);

            /** Send FCM to the other side */
            try {
                $fcmService = new FirebaseNotificationService();
                $senderName = $user?->first_name ?? $user?->name ?? 'User';
                $msgPreview = $message->message_type === 'image'
                    ? '📷 ' . ($message->message_text ?? 'Sent an image')
                    : (mb_strlen($message->message_text) > 200 ? mb_substr($message->message_text, 0, 200) . '...' : $message->message_text);

                if ($ticket->user_id == $userId) {
                    /** Customer sent → notify staff */
                    if ($ticket->recipient_type === 'front_desk' && $ticket->property_id) {
                        $staff = $this->ticketService->getStaffForProperty($ticket->property_id);
                    } else {
                        $staff = $this->ticketService->getHQCSUsers();
                    }
                    foreach ($staff as $s) {
                        $fcmService->sendToUser($s, $senderName . ' - ' . $ticket->ticket_number, $msgPreview, [
                            'type' => 'ticket_message', 'ticket_id' => (string) $ticket->id, 'conversation_id' => (string) $ticket->id,
                        ]);
                    }
                } else {
                    /** Staff sent → notify customer */
                    $fcmService->sendToUser($ticket->user, $senderName . ' - ' . $ticket->ticket_number, $msgPreview, [
                        'type' => 'ticket_message', 'ticket_id' => (string) $ticket->id, 'conversation_id' => (string) $ticket->id,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Ticket message FCM failed: ' . $e->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Message sent',
                'data' => $message,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /api/v1/tickets/{id}/read
     * Mark a ticket as read by the current user.
     */
    public function markAsRead(Request $request, $id)
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'user_id is required'], 400);
        }

        $ticket = Ticket::findOrFail($id);
        $ticket->markAsReadByUser($userId);

        return response()->json(['status' => 'success', 'message' => 'Marked as read']);
    }

    /**
     * POST /api/v1/tickets/{id}/close
     * Close a ticket (user or admin).
     */
    public function closeTicket(Request $request, $id)
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'user_id is required'], 400);
        }

        try {
            $ticket = $this->ticketService->closeTicket($id, $userId);

            /** Notify the other side about closure */
            try {
                $fcmService = new FirebaseNotificationService();
                if ($ticket->user_id == $userId) {
                    /** Customer closed → notify staff */
                    if ($ticket->recipient_type === 'front_desk' && $ticket->property_id) {
                        $staff = $this->ticketService->getStaffForProperty($ticket->property_id);
                        foreach ($staff as $s) {
                            $fcmService->sendToUser($s, 'Ticket Closed', $ticket->ticket_number . ' closed by customer', [
                                'type' => 'ticket_closed', 'ticket_id' => (string) $ticket->id,
                            ]);
                        }
                    }
                } else {
                    /** Staff closed → notify customer */
                    $fcmService->sendToUser($ticket->user, 'Ticket Closed', $ticket->ticket_number . ' has been closed', [
                        'type' => 'ticket_closed', 'ticket_id' => (string) $ticket->id,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Ticket close FCM failed: ' . $e->getMessage());
            }

            return response()->json(['status' => 'success', 'message' => 'Ticket closed', 'data' => $ticket]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * POST /api/v1/tickets/{id}/reopen
     * Reopen a closed ticket within the 7-day window.
     */
    public function reopenTicket(Request $request, $id)
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'user_id is required'], 400);
        }

        try {
            $ticket = $this->ticketService->reopenTicket($id, $userId);
            return response()->json(['status' => 'success', 'message' => 'Ticket reopened', 'data' => $ticket]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * GET /api/v1/broadcasts?user_id=
     * List broadcasts visible to the user.
     */
    public function listBroadcasts(Request $request)
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'user_id is required'], 400);
        }

        try {
            /** Get broadcasts: HQ broadcasts (visible to all) + property broadcasts for user's active bookings */
            $user = User::find($userId);
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
            }

            $broadcasts = TicketBroadcast::where(function ($q) use ($userId) {
                    /** HQ broadcasts (audience-dependent, but for simplicity show all HQ) */
                    $q->where('sender_type', 'hq');
                })
                ->orWhere(function ($q) use ($userId) {
                    /** Property broadcasts where user has/had bookings at that property */
                    $q->where('sender_type', 'front_desk')
                      ->whereIn('property_id', function ($sub) use ($userId) {
                          $sub->select('property_id')
                              ->from('t_transactions')
                              ->where('user_id', $userId)
                              ->whereIn('transaction_status', ['paid', 'completed']);
                      });
                })
                ->orderBy('sent_at', 'desc')
                ->paginate(20);

            /** Add read status per broadcast */
            $broadcastsData = $broadcasts->items();
            foreach ($broadcastsData as $broadcast) {
                $broadcast->is_read = $broadcast->isReadByUser($userId);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Broadcasts retrieved',
                'data' => $broadcastsData,
                'meta' => [
                    'current_page' => $broadcasts->currentPage(),
                    'last_page' => $broadcasts->lastPage(),
                    'per_page' => $broadcasts->perPage(),
                    'total' => $broadcasts->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/v1/broadcasts/{id}?user_id=
     * Get broadcast detail and mark as read.
     */
    public function getBroadcast(Request $request, $id)
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'user_id is required'], 400);
        }

        try {
            $broadcast = TicketBroadcast::with(['sender:id,first_name,last_name,name', 'property:idrec,name'])
                ->findOrFail($id);

            /** Mark as read */
            $broadcast->markAsReadByUser($userId);

            return response()->json([
                'status' => 'success',
                'message' => 'Broadcast retrieved',
                'data' => $broadcast,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
