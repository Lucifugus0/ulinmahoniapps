<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\TicketBroadcast;
use App\Services\FirebaseNotificationService;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * BroadcastController — handles one-way announcement creation and listing
 * for the Backend admin dashboard. Broadcasts are sent to targeted audiences
 * via FCM push notifications and appear as read-only threads in user ticket lists.
 */
class BroadcastController extends Controller
{
    protected TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * List all broadcasts — property-scoped for site users.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = TicketBroadcast::with(['sender', 'property'])
            ->orderBy('sent_at', 'desc');

        /** Property scoping: site users see only their property's broadcasts */
        if (!$user->canViewAllProperties() && $user->property_id) {
            $query->where(function ($q) use ($user) {
                $q->where('property_id', $user->property_id)
                  ->orWhere('sender_type', 'hq');
            });
        }

        $broadcasts = $query->paginate(25);

        return view('pages.tickets.broadcasts.index', compact('broadcasts'));
    }

    /**
     * Show broadcast creation form.
     */
    public function create()
    {
        $user = Auth::user();

        /** Get properties for dropdown (site users see only their property) */
        $properties = $user->canViewAllProperties()
            ? Property::orderBy('name')->get()
            : Property::where('idrec', $user->property_id)->get();

        return view('pages.tickets.broadcasts.create', compact('properties'));
    }

    /**
     * Store and send a new broadcast.
     * Creates the broadcast record, queries the target audience,
     * and sends FCM push notifications to all recipients.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message_text' => 'required|string|max:5000',
            'sender_type' => 'required|in:front_desk,hq',
            'audience' => 'required|in:all_users,active_bookings,active_and_future_bookings',
            'property_id' => 'nullable|integer',
        ]);

        $user = Auth::user();

        /** Determine property_id based on sender type */
        $propertyId = null;
        if ($request->sender_type === 'front_desk') {
            $propertyId = $request->property_id ?? $user->property_id;
            if (!$propertyId) {
                return response()->json(['success' => false, 'message' => 'Property is required for Front Desk broadcasts.'], 422);
            }
        }

        /** Generate broadcast number */
        $broadcastNumber = $this->ticketService->generateBroadcastNumber($propertyId, $request->sender_type);

        /** Get target audience user IDs */
        $audienceIds = $this->ticketService->getBroadcastAudience(
            $request->sender_type,
            $propertyId,
            $request->audience
        );

        /** Create the broadcast record */
        $broadcast = TicketBroadcast::create([
            'broadcast_number' => $broadcastNumber,
            'sender_type' => $request->sender_type,
            'property_id' => $propertyId,
            'sender_id' => $user->id,
            'title' => $request->title,
            'message_text' => $request->message_text,
            'audience' => $request->audience,
            'recipient_count' => count($audienceIds),
            'sent_at' => now(),
            'created_by' => (string) $user->id,
            'updated_by' => (string) $user->id,
        ]);

        /** Send FCM push notifications to all audience members */
        try {
            $fcmService = new FirebaseNotificationService();
            $fcmService->sendToMultipleUsers(
                $audienceIds,
                $request->title,
                mb_strlen($request->message_text) > 200
                    ? mb_substr($request->message_text, 0, 200) . '...'
                    : $request->message_text,
                [
                    'type' => 'broadcast',
                    'broadcast_id' => (string) $broadcast->id,
                ]
            );
            Log::info("Broadcast {$broadcastNumber} sent to " . count($audienceIds) . " users");
        } catch (\Exception $e) {
            Log::error('Broadcast FCM failed: ' . $e->getMessage());
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'broadcast' => $broadcast,
                'recipient_count' => count($audienceIds),
            ]);
        }

        return redirect()->route('broadcasts.index')
            ->with('success', "Broadcast {$broadcastNumber} sent to " . count($audienceIds) . " users.");
    }

    /**
     * Show broadcast detail.
     */
    public function show($id)
    {
        $broadcast = TicketBroadcast::with(['sender', 'property'])->findOrFail($id);

        return view('pages.tickets.broadcasts.show', compact('broadcast'));
    }
}
