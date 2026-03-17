<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Booking;
use App\Models\ChatConversation;
use App\Models\ChatParticipant;
use Carbon\Carbon;

class ChatService
{
    /**
     * Check if user has valid booking for order_id
     * Valid booking = checked in AND not checked out yet
     */
    public function hasValidBooking($userId, $orderId)
    {
        $transaction = Transaction::where('order_id', $orderId)
            ->where('user_id', $userId)
            ->where('status', '1') // Active
            ->whereIn('transaction_status', ['confirmed', 'completed', 'paid'])
            ->first();

        if (!$transaction) {
            return false;
        }

        // Check if booking exists and user has checked in
        $booking = Booking::where('order_id', $orderId)
            ->whereNotNull('check_in_at') // Must have checked in
            ->first();

        if (!$booking) {
            return false;
        }

        // Check if not checked out yet
        $checkOutDate = Carbon::parse($transaction->check_out);
        $now = Carbon::now();

        return $now->lte($checkOutDate);
    }

    /**
     * Get transaction and booking details
     */
    public function getBookingDetails($orderId)
    {
        $transaction = Transaction::where('order_id', $orderId)->first();

        if (!$transaction) {
            return null;
        }

        $booking = Booking::where('order_id', $orderId)->first();

        return [
            'transaction' => $transaction,
            'booking' => $booking,
            'property_id' => $transaction->property_id,
            'user_id' => $transaction->user_id
        ];
    }

    /**
     * Create conversation with automatic customer participant
     */
    public function createConversation($orderId, $userId, $title = null)
    {
        $details = $this->getBookingDetails($orderId);

        if (!$details) {
            throw new \Exception('Booking details not found');
        }

        // Generate default title if not provided
        if (!$title) {
            $title = "Chat for Order {$orderId}";
        }

        $conversation = ChatConversation::create([
            'order_id' => $orderId,
            'property_id' => $details['property_id'],
            'title' => $title,
            'status' => 'active',
            'created_by' => $userId
        ]);

        // Auto-add user as customer participant
        ChatParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => $userId,
            'role' => 'customer',
            'joined_at' => now(),
            'created_by' => $userId
        ]);

        return $conversation;
    }

    /**
     * Check if user can access conversation
     */
    public function canAccessConversation($conversationId, $userId, $isAdmin = false)
    {
        $conversation = ChatConversation::find($conversationId);

        if (!$conversation) {
            return false;
        }

        // Admins can access all conversations
        if ($isAdmin) {
            return true;
        }

        // Check if user is a participant
        return $conversation->isParticipant($userId);
    }
}
