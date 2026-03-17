<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomDoorLock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DoorLockController extends Controller
{
    private string $apiBase = 'https://hms.seyven.cloud/api/seyven-lock';

    private function apiKey(): string
    {
        return config('services.seyven.api_key', 'deff4be0b430945d4c544b0ba588ecad');
    }

    public function index(Request $request)
    {
        $query = RoomDoorLock::with('room')
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('lock_alias', 'like', "%{$request->search}%")
                        ->orWhere('lock_id', 'like', "%{$request->search}%")
                        ->orWhereHas('room', function ($r) use ($request) {
                            $r->where('name', 'like', "%{$request->search}%")
                              ->orWhere('no', 'like', "%{$request->search}%");
                        });
                });
            })
            ->orderBy('created_at', 'desc');

        $perPage = $request->input('per_page', 8);
        $doorLocks = $query->paginate($perPage);

        if ($request->ajax()) {
            return view('pages.Properties.Door_lock.index', compact('doorLocks'));
        }

        $rooms = Room::select('idrec', 'name', 'no', 'property_name')
            ->orderBy('property_name')
            ->orderBy('no')
            ->get();

        return view('pages.Properties.Door_lock.index', compact('doorLocks', 'rooms'));
    }

    /**
     * Hit Seyven API to get lock details by lockId.
     */
    public function getLockDetails(Request $request)
    {
        $request->validate([
            'lock_id' => 'required|integer',
        ]);

        $response = Http::post("{$this->apiBase}/get-lock-details", [
            'apiKey' => $this->apiKey(),
            'lockId' => (int) $request->lock_id,
        ]);

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dari API. Periksa Lock ID.',
            ], 422);
        }

        $body = $response->json();

        if (isset($body['errcode']) && $body['errcode'] !== 0) {
            return response()->json([
                'success' => false,
                'message' => $body['errmsg'] ?? 'Lock ID tidak ditemukan.',
            ], 422);
        }

        $lock = $body['data'] ?? $body;

        return response()->json([
            'success' => true,
            'data' => [
                'lock_id'           => $lock['lockId'] ?? $request->lock_id,
                'lock_alias'        => $lock['lockAlias'] ?? null,
                'lock_mac'          => $lock['lockMac'] ?? null,
                'model_num'         => $lock['modelNum'] ?? null,
                'firmware_revision' => $lock['firmwareRevision'] ?? null,
                'battery_level'     => $lock['electricQuantity'] ?? null,
                'has_gateway'       => $lock['hasGateway'] ?? false,
                'lock_sound'        => $lock['lockSound'] ?? null,
                'privacy_lock'      => $lock['privacyLock'] ?? null,
                'is_frozen'         => $lock['isFrozen'] ?? null,
                'passage_mode'      => $lock['passageMode'] ?? null,
                'last_sync_at'      => $lock['date'] ?? null,
            ],
        ]);
    }

    /**
     * Store door lock after fetching details.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_idrec'        => 'required|integer|exists:m_rooms,idrec',
            'lock_id'           => 'required|integer|unique:m_rooms_door_lock,lock_id',
            'lock_alias'        => 'nullable|string|max:100',
            'lock_mac'          => 'nullable|string|max:50',
            'model_num'         => 'nullable|string|max:50',
            'firmware_revision' => 'nullable|string|max:50',
            'battery_level'     => 'nullable|integer',
            'has_gateway'       => 'nullable|boolean',
            'lock_sound'        => 'nullable|integer',
            'privacy_lock'      => 'nullable|integer',
            'is_frozen'         => 'nullable|integer',
            'passage_mode'      => 'nullable|integer',
            'last_sync_at'      => 'nullable|integer',
        ]);

        $doorLock = RoomDoorLock::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Door lock berhasil ditambahkan.',
            'data'    => $doorLock->load('room'),
        ], 201);
    }

    /**
     * Add random passcode via Seyven API then save to DB.
     */
    public function addPasscode(Request $request, $id)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'start_periode' => 'required|integer',
            'end_periode'   => 'required|integer',
        ]);

        $doorLock = RoomDoorLock::findOrFail($id);

        $response = Http::post("{$this->apiBase}/get-random-passcode", [
            'apiKey'          => $this->apiKey(),
            'lockId'          => $doorLock->lock_id,
            'name'            => $request->name,
            'keyboardPwdType' => 3,
            'start_periode'   => (int) $request->start_periode,
            'end_periode'     => (int) $request->end_periode,
        ]);

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat passcode dari API.',
            ], 422);
        }

        $body = $response->json();

        if (isset($body['errcode']) && $body['errcode'] !== 0) {
            return response()->json([
                'success' => false,
                'message' => $body['errmsg'] ?? 'Gagal membuat passcode.',
            ], 422);
        }

        $passcode = $body['data']['keyboardPwd'] ?? $body['keyboardPwd'] ?? null;

        if (!$passcode) {
            return response()->json([
                'success' => false,
                'message' => 'Passcode tidak ditemukan dalam respons API.',
            ], 422);
        }

        $doorLock->update([
            'passcode'       => $passcode,
            'passcode_name'  => $request->name,
            'passcode_start' => (int) $request->start_periode,
            'passcode_end'   => (int) $request->end_periode,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Passcode berhasil dibuat.',
            'passcode' => $passcode,
            'data'     => $doorLock->fresh()->load('room'),
        ]);
    }

    /**
     * Delete door lock.
     */
    public function destroy($id)
    {
        $doorLock = RoomDoorLock::findOrFail($id);
        $doorLock->delete();

        return response()->json([
            'success' => true,
            'message' => 'Door lock berhasil dihapus.',
        ]);
    }
}
