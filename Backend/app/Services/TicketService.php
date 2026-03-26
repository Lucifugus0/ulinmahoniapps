<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Property;
use App\Models\Role;
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
 * Handles ticket creation, lifecycle management, number generation,
 * eligibility checks, and audience queries for broadcasts.
 */
class TicketService
{
    /**
     * Generate a unique ticket number using atomic increment.
     * Format: {PROPERTY_INITIAL}-TKT-{XXXX} or HQ-TKT-{XXXX}
     * Uses lockForUpdate() to prevent race conditions on concurrent ticket creation.
     */
    public function generateTicketNumber(?int $propertyId, string $recipientType): string
    {
        return DB::transaction(function () use ($propertyId, $recipientType) {
            /** Determine scope: property initial or 'HQ' */
            $scope = 'HQ';
            if ($recipientType === 'front_desk' && $propertyId) {
                $property = Property::where('idrec', $propertyId)->first();
                $scope = $property?->initial ?? 'PROP';
            }

            /** Atomically increment the sequence counter */
            $sequence = TicketSequence::where('scope', $scope)
                ->where('sequence_type', 'ticket')
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                /** Auto-create sequence if not seeded yet */
                $sequence = TicketSequence::create([
                    'scope' => $scope,
                    'sequence_type' => 'ticket',
                    'last_number' => 0,
                ]);
            }

            $sequence->increment('last_number');
            $number = $sequence->last_number;

            return $scope . '-TKT-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Generate a unique broadcast number using atomic increment.
     * Format: {PROPERTY_INITIAL}-BCT-{XXXX} or HQ-BCT-{XXXX}
     */
    public function generateBroadcastNumber(?int $propertyId, string $senderType): string
    {
        return DB::transaction(function () use ($propertyId, $senderType) {
            $scope = 'HQ';
            if ($senderType === 'front_desk' && $propertyId) {
                $property = Property::where('idrec', $propertyId)->first();
                $scope = $property?->initial ?? 'PROP';
            }

            $sequence = TicketSequence::where('scope', $scope)
                ->where('sequence_type', 'broadcast')
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                $sequence = TicketSequence::create([
                    'scope' => $scope,
                    'sequence_type' => 'broadcast',
                    'last_number' => 0,
                ]);
            }

            $sequence->increment('last_number');
            $number = $sequence->last_number;

            return $scope . '-BCT-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Check if a user is eligible to create a ticket for a given booking.
     * Eligible if: transaction_status = 'paid' AND within stay period or 7-day grace after checkout.
     * Returns ['eligible' => bool, 'reason' => string].
     */
    public function checkTicketEligibility(int $userId, string $orderId): array
    {
        /** Find the transaction belonging to this user */
        $transaction = Transaction::where('order_id', $orderId)
            ->where('user_id', $userId)
            ->first();

        if (!$transaction) {
            return ['eligible' => false, 'reason' => 'Booking not found'];
        }

        /** Only paid bookings are eligible */
        if ($transaction->transaction_status !== 'paid') {
            return ['eligible' => false, 'reason' => 'Booking is not paid'];
        }

        /** Check if within stay period or 7-day grace after checkout */
        $now = Carbon::now();
        $checkOut = Carbon::parse($transaction->check_out);
        $graceDeadline = $checkOut->copy()->addDays(7);

        if ($now->gt($graceDeadline)) {
            return ['eligible' => false, 'reason' => 'Booking grace period has expired (7 days after checkout)'];
        }

        return ['eligible' => true, 'reason' => 'OK'];
    }

    /**
     * Get all bookings eligible for ticket creation by a user.
     * Returns paid bookings that are within stay period or 7-day grace.
     */
    public function getEligibleBookings(int $userId): \Illuminate\Support\Collection
    {
        $now = Carbon::now();
        $graceLimit = $now->copy()->subDays(7);

        return Transaction::where('user_id', $userId)
            ->where('transaction_status', 'paid')
            ->where('check_out', '>=', $graceLimit)
            ->orderBy('check_in', 'desc')
            ->get();
    }

    /**
     * Create a new ticket with an initial message.
     * Validates eligibility, generates ticket number, creates ticket and first message.
     */
    public function createTicket(
        int $userId,
        int $categoryId,
        ?string $orderId,
        string $subject,
        ?string $initialMessage = null
    ): Ticket {
        return DB::transaction(function () use ($userId, $categoryId, $orderId, $subject, $initialMessage) {
            /** Look up the category for routing info */
            $category = TicketCategory::findOrFail($categoryId);

            /** Determine property_id from the booking (if applicable) */
            $propertyId = null;
            if ($category->requires_booking && $orderId) {
                $eligibility = $this->checkTicketEligibility($userId, $orderId);
                if (!$eligibility['eligible']) {
                    throw new \Exception($eligibility['reason']);
                }

                $transaction = Transaction::where('order_id', $orderId)->first();
                $propertyId = $transaction?->property_id;
            }

            /** Generate unique ticket number */
            $ticketNumber = $this->generateTicketNumber($propertyId, $category->recipient_type);

            /** Create the ticket */
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

            /** Create the initial message if provided */
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
     * Close a ticket — sets status to 'closed', records who closed it,
     * and sets the reopen deadline to 7 days from now.
     * Creates a system message recording the action.
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

            /** Create system message for the status change */
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
     * Reopen a closed ticket — checks the 7-day reopen window.
     * Creates a system message recording the action.
     */
    public function reopenTicket(int $ticketId, int $userId): Ticket
    {
        return DB::transaction(function () use ($ticketId, $userId) {
            $ticket = Ticket::findOrFail($ticketId);

            if (!$ticket->canBeReopened()) {
                throw new \Exception('Ticket cannot be reopened (past deadline or not closed)');
            }

            $ticket->update([
                'ticket_status' => 'reopened',
                'reopened_at' => now(),
                'updated_by' => (string) $userId,
            ]);

            /** Create system message for the reopen */
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
     * Update ticket status to in_progress (when staff first responds).
     * Creates a system message recording the action.
     */
    public function markInProgress(int $ticketId, int $userId): Ticket
    {
        $ticket = Ticket::findOrFail($ticketId);

        if ($ticket->ticket_status !== 'open' && $ticket->ticket_status !== 'reopened') {
            return $ticket;
        }

        $ticket->update([
            'ticket_status' => 'in_progress',
            'updated_by' => (string) $userId,
        ]);

        return $ticket->fresh();
    }

    /**
     * Get all Front Desk staff for a specific property.
     * Front Desk = user_type=1 (Site) AND property_id matches.
     */
    public function getStaffForProperty(int $propertyId): \Illuminate\Database\Eloquent\Collection
    {
        return User::where('user_type', 1)
            ->where('property_id', $propertyId)
            ->where('status', 1)
            ->get();
    }

    /**
     * Get all HQ Customer Service users.
     * HQ CS = user_type=0 (HO) AND role.name = 'Customer Service'.
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

    /**
     * Get the target audience user IDs for a broadcast.
     * Used when sending FCM notifications to the right set of users.
     */
    public function getBroadcastAudience(string $senderType, ?int $propertyId, string $audience): array
    {
        $now = Carbon::now();

        switch ($audience) {
            case 'all_users':
                /** All registered non-admin users */
                return User::where('is_admin', 0)
                    ->where('status', 1)
                    ->pluck('id')
                    ->toArray();

            case 'active_bookings':
                /** Users with currently active bookings (paid, check_in <= now <= check_out) */
                $query = Transaction::where('transaction_status', 'paid')
                    ->where('check_in', '<=', $now)
                    ->where('check_out', '>=', $now);

                if ($senderType === 'front_desk' && $propertyId) {
                    $query->where('property_id', $propertyId);
                }

                return $query->pluck('user_id')->unique()->toArray();

            case 'active_and_future_bookings':
                /** Users with active or future bookings (paid, check_out >= now) */
                $query = Transaction::where('transaction_status', 'paid')
                    ->where('check_out', '>=', $now);

                if ($senderType === 'front_desk' && $propertyId) {
                    $query->where('property_id', $propertyId);
                }

                return $query->pluck('user_id')->unique()->toArray();

            default:
                return [];
        }
    }
}
