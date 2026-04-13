<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\TicketBroadcast;
use App\Models\TicketBroadcastRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Backend admin notification center — unified feed for chat unread, broadcasts,
 * and other push-message types delivered to the logged-in admin user.
 *
 * Used by the header notifications dropdown (replaces the legacy chat-only dropdown).
 */
class NotificationCenterController extends Controller
{
    /**
     * GET /notifications/feed
     *
     * Returns a unified notification feed sorted by timestamp desc:
     *   - Chat conversations with unread messages
     *   - Broadcasts that haven't been marked read by this user
     *
     * Each item includes: type, id, title, body, timestamp, unread_count, link.
     */
    public function feed(Request $request)
    {
        $user = Auth::user();
        $items = [];

        /* ---------------- Chat conversations with unread messages ---------------- */
        $chatQuery = ChatConversation::query();
        if (!$user->canViewAllProperties() && $user->property_id) {
            $chatQuery->where('property_id', $user->property_id);
        }
        $conversations = $chatQuery->orderByDesc('last_message_at')->limit(20)->get();

        $totalChatUnread = 0;
        foreach ($conversations as $conv) {
            $unread = $conv->getUnreadCountForUser($user->id);
            if ($unread > 0) {
                $items[] = [
                    'type'         => 'chat',
                    'id'           => 'chat_' . $conv->id,
                    'ref_id'       => $conv->id,
                    'title'        => $conv->title ?: ('Chat ' . ($conv->order_id ?? '')),
                    'body'         => $conv->order_id ?? '',
                    'timestamp'    => optional($conv->last_message_at)->toIso8601String(),
                    'unread_count' => $unread,
                    'link'         => route('chat.index') . '?open=' . $conv->id,
                ];
                $totalChatUnread += $unread;
            }
        }

        /* ---------------- Broadcasts the user has not read ---------------- */
        /* Find broadcast IDs the user has already read */
        $readBroadcastIds = TicketBroadcastRead::where('user_id', $user->id)
            ->pluck('broadcast_id')
            ->toArray();

        $broadcasts = TicketBroadcast::whereNotIn('id', $readBroadcastIds)
            ->orderByDesc('sent_at')
            ->limit(20)
            ->get();

        foreach ($broadcasts as $broadcast) {
            $items[] = [
                'type'         => 'broadcast',
                'id'           => 'broadcast_' . $broadcast->id,
                'ref_id'       => $broadcast->id,
                'title'        => $broadcast->title,
                'body'         => mb_strimwidth($broadcast->message_text ?? '', 0, 120, '…'),
                'timestamp'    => optional($broadcast->sent_at)->toIso8601String(),
                'unread_count' => 1,
                'link'         => route('broadcasts.show', $broadcast->id),
            ];
        }

        /* Sort combined feed by timestamp desc */
        usort($items, function ($a, $b) {
            return strcmp($b['timestamp'] ?? '', $a['timestamp'] ?? '');
        });

        $totalUnread = $totalChatUnread + $broadcasts->count();

        return response()->json([
            'success'      => true,
            'total_unread' => $totalUnread,
            'items'        => $items,
        ]);
    }

    /**
     * POST /notifications/mark-all-read
     *
     * Marks all currently-unread notifications as read for the logged-in admin:
     *   - Resets chat unread counters by writing chat_message_reads rows
     *   - Inserts t_ticket_broadcast_reads rows for any unread broadcasts
     */
    public function markAllRead(Request $request)
    {
        $user = Auth::user();

        /* ---------------- Mark all chat messages as read ---------------- */
        $chatQuery = ChatConversation::query();
        if (!$user->canViewAllProperties() && $user->property_id) {
            $chatQuery->where('property_id', $user->property_id);
        }

        $chatQuery->each(function ($conversation) use ($user) {
            /* ChatConversation::markAsReadByUser updates last_read_at on the participant row */
            if (method_exists($conversation, 'markAsReadByUser')) {
                $conversation->markAsReadByUser($user->id);
            }
        });

        /* ---------------- Mark all broadcasts as read ---------------- */
        $readBroadcastIds = TicketBroadcastRead::where('user_id', $user->id)
            ->pluck('broadcast_id')
            ->toArray();

        $unreadBroadcastIds = TicketBroadcast::whereNotIn('id', $readBroadcastIds)
            ->pluck('id')
            ->toArray();

        $now = now();
        foreach ($unreadBroadcastIds as $broadcastId) {
            TicketBroadcastRead::create([
                'broadcast_id' => $broadcastId,
                'user_id'      => $user->id,
                'read_at'      => $now,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }
}
