<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatParticipant;
use App\Models\ChatAttachment;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChatController extends ApiController
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        parent::__construct();
        $this->chatService = $chatService;
    }

    /**
     * LIST USER'S CONVERSATIONS
     * GET /api/v1/chat/conversations
     */
    public function listConversations(Request $request)
    {
        try {
            // Get user_id from request parameter
            $userId = $request->input('user_id');

            if (!$userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'user_id parameter is required'
                ], 400);
            }

            $query = ChatConversation::query()
                ->forUser($userId)
                ->with([
                    'messages.sender',
                    'messages.attachments',
                    'participants.user',
                    'property:idrec,name',
                    'transaction:idrec,order_id,room_id,room_name',
                    'transaction.room:idrec,no'
                ])
                ->orderBy('last_message_at', 'desc');

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Pagination
            if ($request->has('limit')) {
                $conversations = $query->paginate($request->limit);

                // Add unread count for each conversation
                $conversationsData = $conversations->items();
                foreach ($conversationsData as $conversation) {
                    $conversation->unread_count = $conversation->getUnreadCount($userId);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Conversations retrieved successfully',
                    'data' => $conversationsData,
                    'meta' => [
                        'current_page' => $conversations->currentPage(),
                        'last_page' => $conversations->lastPage(),
                        'per_page' => $conversations->perPage(),
                        'total' => $conversations->total()
                    ]
                ]);
            }

            $conversations = $query->get();

            // Add unread count
            foreach ($conversations as $conversation) {
                $conversation->unread_count = $conversation->getUnreadCount($userId);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Conversations retrieved successfully',
                'data' => $conversations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching conversations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET CONVERSATION DETAILS WITH MESSAGES
     * GET /api/v1/chat/conversations/{id}
     */
    public function getConversation(Request $request, $id)
    {
        try {
            // Get user_id from request parameter
            $userId = $request->input('user_id');

            if (!$userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'user_id parameter is required'
                ], 400);
            }

            // Get user model to check admin status
            $user = User::find($userId);
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $conversation = ChatConversation::with([
                'participants.user',
                'property:idrec,name',
                'transaction:idrec,order_id,room_id,room_name',
                'transaction.room:idrec,no'
            ])->find($id);

            if (!$conversation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Conversation not found'
                ], 404);
            }

            // Check access permission
            $isAdmin = $user->is_admin == 1;
            if (!$this->chatService->canAccessConversation($id, $userId, $isAdmin)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to access this conversation'
                ], 403);
            }

            // Get messages with pagination
            $limit = $request->get('limit', 50);
            $messages = ChatMessage::byConversation($id)
                ->with(['sender:id,first_name,last_name,email', 'attachments'])
                ->orderBy('created_at', 'desc')
                ->paginate($limit);

            // Mark messages as read
            $participant = ChatParticipant::where('conversation_id', $id)
                ->where('user_id', $userId)
                ->first();

            if ($participant) {
                $participant->updateLastRead();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Conversation retrieved successfully',
                'data' => [
                    'conversation' => $conversation,
                    'messages' => $messages->items(),
                    'meta' => [
                        'current_page' => $messages->currentPage(),
                        'last_page' => $messages->lastPage(),
                        'per_page' => $messages->perPage(),
                        'total' => $messages->total()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching conversation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * CREATE NEW CONVERSATION
     * POST /api/v1/chat/conversations
     */
    public function createConversation(Request $request)
    {
        try {
            // Get user_id from request parameter
            $userId = $request->input('user_id');

            if (!$userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'user_id parameter is required'
                ], 400);
            }

            // Validation
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|string|max:100|exists:t_transactions,order_id',
                'title' => 'nullable|string|max:255',
                'initial_message' => 'nullable|string|max:5000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $orderId = $request->order_id;

            // Check if conversation already exists
            $existingConversation = ChatConversation::where('order_id', $orderId)->first();
            if ($existingConversation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Conversation for this order already exists',
                    'data' => ['conversation_id' => $existingConversation->id]
                ], 409);
            }

            // Validate user has valid booking
            if (!$this->chatService->hasValidBooking($userId, $orderId)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have a valid active booking for this order. You must check in and stay within the booking period.'
                ], 403);
            }

            DB::beginTransaction();

            // Create conversation
            $conversation = $this->chatService->createConversation(
                $orderId,
                $userId,
                $request->title
            );

            // Send initial message if provided
            if ($request->has('initial_message') && !empty($request->initial_message)) {
                ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $userId,
                    'message_text' => $request->initial_message,
                    'message_type' => 'text',
                    'created_by' => $userId
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Conversation created successfully',
                'data' => $conversation->load(['participants.user', 'property'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating conversation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * SEND MESSAGE
     * POST /api/v1/chat/conversations/{id}/messages
     */
    public function sendMessage(Request $request, $id)
    {
        try {
            // Get user_id from request parameter
            $userId = $request->input('user_id');

            if (!$userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'user_id parameter is required'
                ], 400);
            }

            // Get user model to check admin status
            $user = User::find($userId);
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            // Validation
            $validator = Validator::make($request->all(), [
                'message_text' => 'required_without:image|nullable|string|max:5000',
                'image' => 'required_without:message_text|nullable|image|mimes:jpeg,jpg,png,gif|max:10240'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $conversation = ChatConversation::find($id);

            if (!$conversation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Conversation not found'
                ], 404);
            }

            // Check access permission
            $isAdmin = $user->is_admin == 1;
            if (!$this->chatService->canAccessConversation($id, $userId, $isAdmin)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to send messages in this conversation'
                ], 403);
            }

            DB::beginTransaction();

            // Determine message type
            $messageType = $request->hasFile('image') ? 'image' : 'text';

            // Create message
            $message = ChatMessage::create([
                'conversation_id' => $id,
                'sender_id' => $userId,
                'message_text' => $request->message_text,
                'message_type' => $messageType,
                'created_by' => $userId
            ]);

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $fileName = Str::random(40) . '.' . $image->getClientOriginalExtension();
                $filePath = "chat_attachments/{$id}/" . $fileName;

                // Store original
                $path = $image->storeAs('chat_attachments/' . $id, $fileName, 'public');

                // Create thumbnail (optional)
                $thumbnailPath = null;
                try {
                    if (class_exists('Intervention\Image\Facades\Image')) {
                        $thumbnailName = 'thumb_' . $fileName;
                        $thumbnailFullPath = storage_path('app/public/chat_attachments/' . $id . '/' . $thumbnailName);

                        // Ensure directory exists
                        $dir = dirname($thumbnailFullPath);
                        if (!file_exists($dir)) {
                            mkdir($dir, 0755, true);
                        }

                        \Intervention\Image\Facades\Image::make($image)->fit(200, 200)->save($thumbnailFullPath);
                        $thumbnailPath = "chat_attachments/{$id}/" . $thumbnailName;
                    }
                } catch (\Exception $e) {
                    // Log error but don't fail - thumbnail is optional
                    \Log::warning('Failed to create thumbnail: ' . $e->getMessage());
                }

                // Create attachment record
                ChatAttachment::create([
                    'message_id' => $message->id,
                    'file_name' => $image->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_type' => $image->getMimeType(),
                    'file_size' => $image->getSize(),
                    'attachment_type' => 'other',
                    'thumbnail_path' => $thumbnailPath,
                    'created_by' => $userId
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Message sent successfully',
                'data' => $message->load(['sender', 'attachments'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error sending message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * EDIT MESSAGE
     * PUT /api/v1/chat/messages/{id}
     */
    public function editMessage(Request $request, $id)
    {
        try {
            // Get user_id from request parameter
            $userId = $request->input('user_id');

            if (!$userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'user_id parameter is required'
                ], 400);
            }

            // Validation
            $validator = Validator::make($request->all(), [
                'message_text' => 'required|string|max:5000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $message = ChatMessage::find($id);

            if (!$message) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Message not found'
                ], 404);
            }

            // Check if user can edit this message
            if (!$message->canBeEditedBy($userId)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to edit this message'
                ], 403);
            }

            // Update message
            $message->update([
                'message_text' => $request->message_text,
                'is_edited' => true,
                'edited_at' => now(),
                'updated_by' => $userId
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Message updated successfully',
                'data' => $message->load(['sender', 'attachments'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * MARK MESSAGES AS READ
     * POST /api/v1/chat/conversations/{id}/read
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            // Get user_id from request parameter
            $userId = $request->input('user_id');

            if (!$userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'user_id parameter is required'
                ], 400);
            }

            $conversation = ChatConversation::find($id);

            if (!$conversation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Conversation not found'
                ], 404);
            }

            // Check if user is participant
            if (!$conversation->isParticipant($userId)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not a participant in this conversation'
                ], 403);
            }

            // Update last_read_at for participant
            $participant = ChatParticipant::where('conversation_id', $id)
                ->where('user_id', $userId)
                ->first();

            if ($participant) {
                $participant->updateLastRead();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Messages marked as read'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error marking messages as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ASSIGN PARTICIPANT (ADMIN ONLY)
     * POST /api/v1/chat/conversations/{id}/participants
     */
    public function assignParticipant(Request $request, $id)
    {
        try {
            // Get user_id from request parameter (admin user)
            $userId = $request->input('admin_user_id');

            if (!$userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'admin_user_id parameter is required'
                ], 400);
            }

            // Check if user is admin
            $user = User::find($userId);
            if (!$user || $user->is_admin != 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            // Validation
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'role' => 'required|in:staff,admin'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $conversation = ChatConversation::find($id);

            if (!$conversation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Conversation not found'
                ], 404);
            }

            // Check if user is already a participant
            $existingParticipant = ChatParticipant::where('conversation_id', $id)
                ->where('user_id', $request->user_id)
                ->first();

            if ($existingParticipant) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User is already a participant in this conversation'
                ], 409);
            }

            // Add participant
            $participant = ChatParticipant::create([
                'conversation_id' => $id,
                'user_id' => $request->user_id,
                'role' => $request->role,
                'joined_at' => now(),
                'created_by' => $userId
            ]);

            // Create system message
            $assignedUser = User::find($request->user_id);
            ChatMessage::create([
                'conversation_id' => $id,
                'sender_id' => $userId,
                'message_text' => "{$assignedUser->first_name} {$assignedUser->last_name} has been added as {$request->role}",
                'message_type' => 'system',
                'created_by' => $userId
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Participant assigned successfully',
                'data' => $participant->load('user')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error assigning participant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * LIST ALL CONVERSATIONS (ADMIN ONLY)
     * GET /api/v1/chat/admin/conversations
     */
    public function adminListConversations(Request $request)
    {
        try {
            // Get user_id from request parameter (admin user)
            $userId = $request->input('admin_user_id');

            if (!$userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'admin_user_id parameter is required'
                ], 400);
            }

            // Check if user is admin
            $user = User::find($userId);
            if (!$user || $user->is_admin != 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $query = ChatConversation::query()
                ->with([
                    'messages.sender',
                    'messages.attachments',
                    'participants.user',
                    'property:idrec,name',
                    'transaction:idrec,order_id,user_name,user_email,room_id,room_name',
                    'transaction.room:idrec,no'
                ]);

            // Filter by property
            if ($request->has('property_id')) {
                $query->where('property_id', $request->property_id);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by order_id
            if ($request->has('order_id')) {
                $query->where('order_id', $request->order_id);
            }

            // Order by
            $orderBy = $request->get('order_by', 'last_message_at');
            $orderDirection = $request->get('order_direction', 'desc');
            $query->orderBy($orderBy, $orderDirection);

            // Pagination
            if ($request->has('limit')) {
                $conversations = $query->paginate($request->limit);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Conversations retrieved successfully',
                    'data' => $conversations->items(),
                    'meta' => [
                        'current_page' => $conversations->currentPage(),
                        'last_page' => $conversations->lastPage(),
                        'per_page' => $conversations->perPage(),
                        'total' => $conversations->total()
                    ]
                ]);
            }

            $conversations = $query->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Conversations retrieved successfully',
                'data' => $conversations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching conversations',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
