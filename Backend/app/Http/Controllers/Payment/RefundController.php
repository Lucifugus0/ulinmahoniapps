<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Refund;
use App\Models\Transaction;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class RefundController extends Controller
{
    /**
     * Tampilkan semua data refund.
     */
    public function index(Request $request)
    {
        $query = Refund::with('transaction')->where('status', 'pending');

        // Filter berdasarkan pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_booking', 'like', "%{$search}%")
                    ->orWhereHas('transaction', function ($q2) use ($search) {
                        $q2->where('user_name', 'like', "%{$search}%")
                            ->orWhere('transaction_code', 'like', "%{$search}%");
                    });
            });
        }

        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('refund_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('refund_date', '<=', $request->end_date);
        }

        $perPage = $request->get('per_page', 8);
        $refunds = $query->orderBy('refund_date', 'desc')->paginate($perPage);

        return view('pages.payment.refund.index', compact(
            'refunds',
            'perPage'
        ));
    }

    /**
     * Confirm a pending refund by uploading proof image.
     * Updates existing pending refund record (from cancel flow) instead of creating a duplicate.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:t_transactions,order_id',
            'refund_image' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Maks 5MB
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($request) {

            // Find existing pending refund (created during cancel flow)
            $existingRefund = Refund::where('id_booking', $request->order_id)->first();

            // If refund already marked as 'refunded', reject duplicate
            if ($existingRefund && $existingRefund->status === 'refunded') {
                return response()->json([
                    'message' => 'Refund untuk order ini sudah dilakukan.'
                ], 422);
            }

            // Handle file upload
            if ($request->hasFile('refund_image')) {
                $image = $request->file('refund_image');

                if (!$image->isValid()) {
                    return response()->json([
                        'message' => 'File yang diupload tidak valid.'
                    ], 422);
                }

                $imageCaption = $image->getClientOriginalName();
                $imagePath = $image->store('refund_images', 'public');
            } else {
                return response()->json([
                    'message' => 'Bukti refund harus diupload.'
                ], 422);
            }

            // Update existing pending refund or create new one
            if ($existingRefund) {
                $existingRefund->update([
                    'status' => 'refunded',
                    'img' => $imageCaption,
                    'image_caption' => $imageCaption,
                    'image_path' => $imagePath,
                    'refund_date' => now()->format('Y-m-d H:i:s'),
                    'admin_notes' => $request->input('admin_notes'),
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                ]);
                $refund = $existingRefund;
            } else {
                // Fallback: create new refund if no pending record exists
                $refund = Refund::create([
                    'id_booking' => $request->order_id,
                    'status' => 'refunded',
                    'refund_type' => 'admin',
                    'img' => $imageCaption,
                    'image_caption' => $imageCaption,
                    'image_path' => $imagePath,
                    'refund_date' => now()->format('Y-m-d H:i:s'),
                    'admin_notes' => $request->input('admin_notes'),
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                ]);
            }

            // Update booking to inactive after refund
            $booking = Booking::where('order_id', $request->order_id)
                ->where('status', 1)
                ->first();
            if ($booking) {
                $booking->update([
                    'status' => 0,
                    'reason' => 'refunded'
                ]);

                /* rental_status untouched — refund follows cancellation, and
                   cancellation is disallowed after check-in. Only check-out
                   flips the physical-occupancy flag. */
            }

            // Update status transaksi
            $transaction = Transaction::where('order_id', $request->order_id)->first();
            if ($transaction) {
                $transaction->update([
                    'transaction_status' => 'refunded'
                ]);
            }

            return response()->json([
                'message' => 'Refund berhasil disimpan dan status transaksi diupdate.',
                'data' => $refund,
                'booking_updated' => $booking ? true : false,
                'transaction_updated' => $transaction ? true : false
            ], 201);
        });
    }
}
