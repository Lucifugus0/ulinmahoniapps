<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Property;
use App\Models\Ticket;
use App\Models\TicketBroadcast;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use App\Models\TicketSequence;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * TicketService — core business logic for the customer service ticketing system.
 * Shared DB with Backend — same logic applies. Used by Frontend API controllers.
 */
class TicketService
{
    /**
     * Generate a unique ticket number using atomic increment.
     * Format: {PROPERTY_INITIAL}-TKT-{XXXX} or HQ-TKT-{XXXX}
     */
    public function generateTicketNumber(?int $propertyId, string $recipientType): string
    {
        return DB::transaction(function () use ($propertyId, $recipientType) {
            $scope = 'HQ';
            if ($recipientType === 'front_desk' && $propertyId) {
                $property = Property::where('idrec', $propertyId)->first();
                $scope = $property?->initial ?? 'PROP';
            }

            $sequence = TicketSequence::where('scope', $scope)
                ->where('sequence_type', 'ticket')
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                $sequence = TicketSequence::create([
                    'scope' => $scope,
                    'sequence_type' => 'ticket',
                    'last_number' => 0,
                ]);
            }

            $sequence->increment('last_number');
            return $scope . '-TKT-' . str_pad($sequence->last_number, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Check if a user is eligible to create a ticket for a given booking.
     * Eligible if: transaction_status = 'paid' AND within stay or 7-day grace after checkout.
     */
    public function checkTicketEligibility(int $userId, string $orderId): array
    {
        $transaction = Transaction::where('order_id', $orderId)
            ->where('user_id', $userId)
            ->first();

        if (!$transaction) {
            return ['eligible' => false, 'reason' => 'Booking not found'];
        }

        if ($transaction->transaction_status !== 'paid') {
            return ['eligible' => false, 'reason' => 'Booking is not paid'];
        }

        $now = Carbon::now();
        $checkOut = Carbon::parse($transaction->check_out);
        $graceDeadline = $checkOut->copy()->addDays(7);

        if ($now->gt($graceDeadline)) {
            return ['eligible' => false, 'reason' => 'Booking grace period has expired'];
        }

        return ['eligible' => true, 'reason' => 'OK'];
    }

    /**
     * Get all bookings eligible for ticket creation by a user.
     * Returns paid bookings within stay or 7-day grace period.
     */
    public function getEligibleBookings(int $userId): \Illuminate\Support\Collection
    {
        $graceLimit = Carbon::now()->subDays(7);

        return Transaction::where('user_id', $userId)
            ->where('transaction_status', 'paid')
            ->where('check_out', '>=', $graceLimit)
            ->with(['booking'])
            ->orderBy('check_in', 'desc')
            ->get();
    }

    /**
     * Create a new ticket with an initial message.
     */
    public function createTicket(
        int $userId,
        int $categoryId,
        ?string $orderId,
        string $subject,
        ?string $initialMessage = null
    ): Ticket {
        return DB::transaction(function () use ($userId, $categoryId, $orderId, $subject, $initialMessage) {
            $category = TicketCategory::findOrFail($categoryId);

            $propertyId = null;
            if ($category->requires_booking && $orderId) {
                $eligibility = $this->checkTicketEligibility($userId, $orderId);
                if (!$eligibility['eligible']) {
                    throw new \Exception($eligibility['reason']);
                }
                $transaction = Transaction::where('order_id', $orderId)->first();
                $propertyId = $transaction?->property_id;
            }

            $ticketNumber = $this->generateTicketNumber($propertyId, $category->recipient_type);

            $ticket = Ticket::create([
                'ticket_number' => $ticketNumber,
                'order_id' => $orderId,
                'property_id' => $propertyId,
                'user_id' => $userId,
                'category_id' => $categoryId,
                'recipient_type' => $category->recipient_type,
                'subject' => $subject,
                'ticket_status' => 'open',
                'priority' => 'normal',
                'last_message_at' => now(),
                'created_by' => (string) $userId,
                'updated_by' => (string) $userId,
            ]);

            if ($initialMessage) {
                TicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'sender_id' => $userId,
                    'message_text' => $initialMessage,
                    'message_type' => 'text',
                    'created_by' => (string) $userId,
                    'updated_by' => (string) $userId,
                ]);
            }

            return $ticket;
        });
    }

    /**
     * Close a ticket — sets status to closed with 7-day reopen window.
     */
    public function closeTicket(int $ticketId, int $userId): Ticket
    {
        return DB::transaction(function () use ($ticketId, $userId) {
            $ticket = Ticket::findOrFail($ticketId);

            if (!$ticket->isActive()) {
                throw new \Exception('Ticket is already closed');
            }

            $ticket->update([
                'ticket_status' => 'closed',
                'closed_at' => now(),
                'closed_by' => $userId,
                'reopen_deadline' => now()->addDays(7),
                'updated_by' => (string) $userId,
            ]);

            $user = User::find($userId);
            $userName = $user?->first_name ?? $user?->name ?? 'User';
            TicketMessage::create([
                'ticket_id' => $ticketId,
                'sender_id' => $userId,
                'message_text' => "Ticket closed by {$userName}",
                'message_type' => 'system',
                'created_by' => (string) $userId,
            ]);

            return $ticket->fresh();
        });
    }

    /**
     * Reopen a closed ticket within the 7-day window.
     */
    public function reopenTicket(int $ticketId, int $userId): Ticket
    {
        return DB::transaction(function () use ($ticketId, $userId) {
            $ticket = Ticket::findOrFail($ticketId);

            if (!$ticket->canBeReopened()) {
                throw new \Exception('Ticket cannot be reopened');
            }

            $ticket->update([
                'ticket_status' => 'reopened',
                'reopened_at' => now(),
                'updated_by' => (string) $userId,
            ]);

            $user = User::find($userId);
            $userName = $user?->first_name ?? $user?->name ?? 'User';
            TicketMessage::create([
                'ticket_id' => $ticketId,
                'sender_id' => $userId,
                'message_text' => "Ticket reopened by {$userName}",
                'message_type' => 'system',
                'created_by' => (string) $userId,
            ]);

            return $ticket->fresh();
        });
    }

    /**
     * Get Front Desk staff for a property (for FCM notifications).
     */
    public function getStaffForProperty(int $propertyId): \Illuminate\Database\Eloquent\Collection
    {
        return User::where('user_type', 1)
            ->where('property_id', $propertyId)
            ->where('status', 1)
            ->get();
    }

    /**
     * Get HQ Customer Service users (for FCM notifications).
     */
    public function getHQCSUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::where('user_type', 0)
            ->whereHas('role', function ($query) {
                $query->where('name', 'Customer Service');
            })
            ->where('status', 1)
            ->get();
    }
}
