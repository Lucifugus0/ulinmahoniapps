<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Booking;
use App\Models\Refund;
use App\Models\Payment;
use App\Models\Room;
use App\Models\ParkingFeeTransaction;
use App\Models\ParkingFee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\RefundCalculationService;
use App\Services\InvoiceNumberService;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', 8);

        // Server-side sorting: supported columns map to actual DB columns/joins
        $sortBy = $request->input('sort_by', 'tanggal');
        $sortDir = $request->input('sort_dir', 'desc') === 'asc' ? 'asc' : 'desc';

        $query = Payment::with([
            'booking',
            'user',
            'verifiedBy',
            'transaction' => function ($query) {
                $query->with(['property', 'room', 'user']);
            }
        ])->whereHas('transaction', function ($q) use ($user) {
            if ($user->isSite() && $user->property_id) {
                $q->where('property_id', $user->property_id);
            }
            $q->where('transaction_status', '!=', 'expired');
        });

        // Apply sort — join t_transactions for sortable columns
        if (in_array($sortBy, ['tanggal', 'orderid', 'pelanggan', 'property'])) {
            $query->join('t_transactions', 't_payment.order_id', '=', 't_transactions.order_id');
            if ($sortBy === 'tanggal') {
                $query->orderBy('t_transactions.created_at', $sortDir);
            } elseif ($sortBy === 'orderid') {
                $query->orderBy('t_transactions.order_id', $sortDir);
            } elseif ($sortBy === 'pelanggan') {
                $query->orderBy('t_transactions.user_name', $sortDir);
            } elseif ($sortBy === 'property') {
                $query->orderBy('t_transactions.property_name', $sortDir);
            }
            $query->select('t_payment.*'); // avoid ambiguous columns
        } else {
            $query->orderBy('idrec', 'desc');
        }

        $payments = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->withQueryString();

        return view('pages.payment.pay.index', [
            'payments' => $payments,
            'per_page' => $perPage,
        ]);
    }

    public function filter(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', 8);
        $search = $request->input('search');
        $status = $request->input('status', 'all');

        // Server-side sorting
        $sortBy = $request->input('sort_by', 'tanggal');
        $sortDir = $request->input('sort_dir', 'desc') === 'asc' ? 'asc' : 'desc';

        $query = Payment::with([
            'booking',
            'user',
            'verifiedBy',
            'transaction' => function ($query) {
                $query->with(['property', 'room', 'user']);
            }
        ])->whereHas('transaction', function ($q) use ($user) {
            if ($user->isSite() && $user->property_id) {
                $q->where('property_id', $user->property_id);
            }
            $q->where('transaction_status', '!=', 'expired');
        });

        // Apply sort
        if (in_array($sortBy, ['tanggal', 'orderid', 'pelanggan', 'property'])) {
            $query->join('t_transactions', 't_payment.order_id', '=', 't_transactions.order_id');
            if ($sortBy === 'tanggal') {
                $query->orderBy('t_transactions.created_at', $sortDir);
            } elseif ($sortBy === 'orderid') {
                $query->orderBy('t_transactions.order_id', $sortDir);
            } elseif ($sortBy === 'pelanggan') {
                $query->orderBy('t_transactions.user_name', $sortDir);
            } elseif ($sortBy === 'property') {
                $query->orderBy('t_transactions.property_name', $sortDir);
            }
            $query->select('t_payment.*');
        } else {
            $query->orderBy('t_payment.idrec', 'desc');
        }

        // Filter based on search
        if ($search) {
            $query->where(function ($q) use ($search) {
                /* Prefix with t_payment. to avoid ambiguous column when sort joins t_transactions */
                $q->where('t_payment.order_id', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('username', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('transaction', function ($transactionQuery) use ($search) {
                        $transactionQuery->where('order_id', 'like', '%' . $search . '%');
                    });
            });
        }

        // Filter based on status
        if ($status !== 'all') {
            $query->whereHas('transaction', function ($q) use ($status) {
                $q->where('transaction_status', $status);
            });
        }

        // Date range filter if needed
        if ($request->has('start_date') && $request->has('end_date')) {
            /* Prefix with t_payment. to avoid ambiguous column when sort joins t_transactions */
            $query->whereBetween('t_payment.created_at', [
                $request->input('start_date') . ' 00:00:00',
                $request->input('end_date') . ' 23:59:59'
            ]);
        }

        $payments = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('pages.payment.pay.partials.pay_table', ['payments' => $payments])->render(),
            ]);
        }

        return view('pages.payment.pay.index', [
            'payments' => $payments,
            'per_page' => $perPage,
        ]);
    }

    public function approve(Request $request, $id)
    {
        try {
            // First try to find the payment record
            $payment = Payment::where('idrec', $id)->first();

            if (!$payment) {
                // Fallback to transaction if payment not found
                $transaction = Transaction::where('order_id', $id)->orWhere('id', $id)->firstOrFail();

                $payment = Payment::updateOrCreate(
                    ['order_id' => $transaction->order_id],
                    [
                        'property_id' => $transaction->property_id,
                        'room_id' => $transaction->room_id,
                        'user_id' => $transaction->user_id,
                        'grandtotal_price' => $transaction->grandtotal_price,
                        'verified_by' => Auth::id(),
                        'verified_at' => now(),
                        'payment_status' => 'paid',
                    ]
                );

                $transaction->update([
                    'transaction_status' => 'paid',
                    'paid_at' => now()
                ]);

                // Assign persisted invoice number now that this transaction is paid
                InvoiceNumberService::assign($transaction->fresh());
            } else {
                // Update existing payment
                $payment->update([
                    'verified_by' => Auth::id(),
                    'verified_at' => now(),
                    'payment_status' => 'paid',
                ]);

                // Update related transaction
                if ($payment->transaction) {
                    $payment->transaction->update([
                        'transaction_status' => 'paid',
                        'paid_at' => now()
                    ]);

                    // Assign persisted invoice number now that this transaction is paid
                    InvoiceNumberService::assign($payment->transaction->fresh());
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran berhasil disetujui'
                ]);
            }

            return redirect()->back()->with('success', 'Pembayaran berhasil disetujui');
        } catch (\Exception $e) {
            Log::error('Payment approval failed: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyetujui pembayaran. Error: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal menyetujui pembayaran. Error: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            // Find payment by idrec (primary key)
            $payment = Payment::findOrFail($id);

            // Update payment record
            $payment->update([
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'payment_status' => 'rejected',
                'notes' => $request->input('rejectNote'),
                'updated_at' => now()
            ]);

            // Update related transaction if exists
            if ($payment->transaction) {
                $payment->transaction->update([
                    'transaction_status' => 'rejected',
                    'paid_at' => now()
                ]);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran berhasil ditolak'
                ]);
            }

            return redirect()->back()->with('success', 'Pembayaran berhasil ditolak');
        } catch (\Exception $e) {
            Log::error('Payment rejection failed: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menolak pembayaran. Error: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal menolak pembayaran. Error: ' . $e->getMessage());
        }
    }

    public function cancel(Request $request, $id)
    {
        try {
            $payment = Payment::findOrFail($id);

            // Validasi status transaksi
            if (!in_array($payment->transaction->transaction_status, ['paid', 'completed'])) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Hanya booking dengan status terverifikasi yang dapat dibatalkan.'
                    ], 400);
                }
                return redirect()->back()->with('error', 'Hanya booking dengan status terverifikasi yang dapat dibatalkan.');
            }

            // Tentukan alasan pembatalan
            $cancelReason = $request->cancelReason === 'other'
                ? $request->customCancelReason
                : $request->cancelReason;

            // Calculate refund using RefundCalculationService for breakdown
            $refundService = new RefundCalculationService();
            $refundCalc = $refundService->calculate($payment->transaction);

            /* Honor the refund option. Three modes:
                 - no_refund   → zero everything
                 - full_refund → room + parking + deposit (everything except service/admin fees) — backend-only override
                 - refund      → tier-based amount from RefundCalculationService
               The "Bukti pembayaran tidak sesuai" reason is forced to no_refund server-side
               regardless of the submitted option, as a safety net if the JS lock is bypassed. */
            $refundOption = $request->input('refundOption', 'refund');
            if ($request->cancelReason === 'bukti_pembayaran_tidak_sesuai') {
                $refundOption = 'no_refund';
            }

            if ($refundOption === 'no_refund') {
                $refundAmount = 0;
                $refundCalc['room_refund']    = 0;
                $refundCalc['deposit_refund'] = 0;
                $refundCalc['other_refund']   = 0;
                $refundCalc['total_refund']   = 0;
            } elseif ($refundOption === 'full_refund') {
                /* Full refund: 100% of room + parking + deposit. Service and admin fees still excluded. */
                $refundCalc['room_refund']    = (float) ($payment->transaction->room_price ?? 0);
                $refundCalc['deposit_refund'] = (float) ($payment->transaction->deposit_fee ?? 0);
                $refundCalc['other_refund']   = (float) ($payment->transaction->parking_fee ?? 0);
                $refundCalc['refund_percentage'] = 100;
                $refundCalc['total_refund']   = $refundCalc['room_refund'] + $refundCalc['deposit_refund'] + $refundCalc['other_refund'];
                $refundAmount = (int) round($refundCalc['total_refund']);
            } else {
                /* Use the server-calculated tier total — modal field is read-only so it should match.
                   Fall back to the calc total if missing. */
                $refundAmount = $request->refundAmount
                    ? (int) str_replace(['Rp', '.', ' '], '', $request->refundAmount)
                    : $refundCalc['total_refund'];
            }

            /* Wrap the entire cancel flow (refund row, status update, renewal rollback,
               booking status, room release, payment status) in a single DB transaction so
               every side-effect either commits together or rolls back together. Guarantees
               t_transactions.transaction_status = 'cancelled' AND parent renewal rollback
               are both applied — or neither is. */
            DB::transaction(function () use ($payment, $cancelReason, $refundAmount, $refundCalc) {
                // Simpan data refund ke tabel t_refund with breakdown
                Refund::create([
                    'id_booking'    => $payment->order_id,
                    'status'        => 'pending',
                    'reason'        => $cancelReason,
                    'amount'        => $refundAmount,
                    'refund_type'   => 'admin',
                    'room_refund'   => $refundCalc['room_refund'],
                    'deposit_refund' => $refundCalc['deposit_refund'],
                    'other_refund'  => $refundCalc['other_refund'],
                    'img'           => null,
                    'image_caption' => null,
                    'image_path'    => null,
                    'refund_date'   => Carbon::now(),
                ]);

                // Update status transaksi (Eloquent fires model events)
                $payment->transaction->update([
                    'transaction_status' => 'cancelled',
                    'cancel_at' => Carbon::now(),
                ]);

                /* Defensive double-check via raw query — even if Eloquent's update()
                   silently no-ops (e.g. due to model events), this guarantees the row is updated. */
                DB::table('t_transactions')
                    ->where('idrec', $payment->transaction->idrec)
                    ->update([
                        'transaction_status' => 'cancelled',
                        'cancel_at'          => Carbon::now(),
                    ]);

                /* Renewal rollback — if this cancelled booking is a renewal,
                   reset the parent booking's renewal_status and check_out_at
                   so the parent booking is no longer marked as "already renewed".
                   Inside the same DB transaction so it's atomic with the cancel. */
                if ($payment->transaction->is_renewal == 1) {
                    $txRoomId    = (int) $payment->transaction->room_id;
                    $txUserId    = (int) $payment->transaction->user_id;
                    $txIdrec     = (int) $payment->transaction->idrec;
                    $txCreatedAt = (string) $payment->transaction->created_at;

                    /* Find the most recent previous PAID/CONFIRMED transaction
                       for the same room + user (the parent booking that was renewed). */
                    $previousTransaction = DB::table('t_transactions')
                        ->where('room_id', $txRoomId)
                        ->where('user_id', $txUserId)
                        ->where('idrec', '!=', $txIdrec)
                        ->whereRaw('UPPER(transaction_status) IN (?, ?)', ['PAID', 'CONFIRMED'])
                        ->where('created_at', '<', $txCreatedAt)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($previousTransaction) {
                        $prevIdrec   = (int) $previousTransaction->idrec;
                        $prevOrderId = (string) $previousTransaction->order_id;

                        // Check if parent booking had a check_out_at before clearing it
                        $previousBooking = DB::table('t_booking')->where('order_id', $prevOrderId)->first();
                        $hadCheckOut = $previousBooking && $previousBooking->check_out_at !== null;

                        // Reset parent transaction's renewal_status back to 0
                        DB::table('t_transactions')
                            ->where('idrec', $prevIdrec)
                            ->update(['renewal_status' => 0]);

                        /* Clear parent booking's check_out_at (undo the checkout
                           that was set when this renewal was created) */
                        DB::table('t_booking')
                            ->where('order_id', $prevOrderId)
                            ->update(['check_out_at' => null]);

                        /* Restore rental_status for monthly-only rooms — if the parent
                           had a check_out_at, the room was occupied, so set back to 1 */
                        $cancelledRoom = DB::table('m_rooms')->where('idrec', $txRoomId)->first();
                        if ($cancelledRoom && !$cancelledRoom->periode_daily) {
                            DB::table('m_rooms')
                                ->where('idrec', $txRoomId)
                                ->update(['rental_status' => $hadCheckOut ? 1 : 0]);
                        }

                        Log::info('Renewal rollback on cancellation', [
                            'cancelled_transaction_id' => $txIdrec,
                            'previous_transaction_id'  => $prevIdrec,
                            'previous_order_id'        => $prevOrderId,
                        ]);
                    } else {
                        Log::warning('Renewal cancel: parent transaction not found for rollback', [
                            'cancelled_transaction_id' => $txIdrec,
                            'room_id'                  => $txRoomId,
                            'user_id'                  => $txUserId,
                        ]);
                    }
                }

                // Update related booking status to 0 (cancelled)
                $booking = $payment->booking ?? Booking::where('order_id', $payment->order_id)->where('status', 1)->first();

                if ($booking) {
                    $booking->update([
                        'status' => 0,
                        'reason' => $cancelReason,
                    ]);

                    // Cancel booking selalu membebaskan kamar (rental_status = 0)
                    if ($booking->room_id) {
                        Room::where('idrec', $booking->room_id)
                            ->update(['rental_status' => 0]);
                    }
                }

                // Release parking quota for this cancelled booking
                $this->releaseParkingQuota($payment->order_id);

                $payment->update([
                    'payment_status' => 'refunded',
                ]);
            });

            // Kirim notifikasi (opsional)
            if ($request->has('sendNotification')) {
                // logika kirim notifikasi ke pelanggan (jika diperlukan)
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking berhasil dibatalkan dan data refund telah disimpan.'
                ]);
            }

            return redirect()->back()->with('success', 'Booking berhasil dibatalkan dan data refund telah disimpan.');
        } catch (\Exception $e) {
            Log::error('Booking cancellation failed: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membatalkan booking. Error: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal membatalkan booking. Error: ' . $e->getMessage());
        }
    }


    public function getBookingAttachment($id)
    {
        try {
            $payment = Payment::with(['transaction'])->findOrFail($id);

            if (!$payment->transaction || !$payment->transaction->attachment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bukti pembayaran tidak tersedia'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'attachment' => $payment->transaction->attachment,
                'order_id' => $payment->order_id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil bukti pembayaran'
            ], 500);
        }
    }

    public function viewProof($id)
    {
        $transaction = Transaction::findOrFail($id);

        if (!$transaction->attachment) {
            abort(404);
        }

        // Decode base64 dan tampilkan sebagai gambar
        $imageData = base64_decode($transaction->attachment);

        return response($imageData)
            ->header('Content-Type', $this->getImageMimeType($transaction->attachment));
    }

    private function getImageMimeType($base64)
    {
        // Deteksi tipe MIME dari data base64
        $signature = substr($base64, 0, 20);

        if (strpos($signature, 'data:image/jpeg') === 0) {
            return 'image/jpeg';
        } elseif (strpos($signature, 'data:image/png') === 0) {
            return 'image/png';
        } elseif (strpos($signature, 'data:image/gif') === 0) {
            return 'image/gif';
        }

        // Default ke JPEG jika tidak bisa dideteksi
        return 'image/jpeg';
    }

    public function updatePaymentDate(Request $request, $id)
    {
        try {
            $payment = Payment::findOrFail($id);

            // Get check-in date
            $checkInDate = $payment->transaction?->check_in;

            if (!$checkInDate) {
                return redirect()->back()->with('error', 'Tanggal check-in tidak ditemukan');
            }

            $request->validate([
                'payment_date' => [
                    'required',
                    'date',
                    'before_or_equal:' . $checkInDate->format('Y-m-d H:i:s'),
                ],
            ], [
                'payment_date.required' => 'Tanggal pembayaran wajib diisi',
                'payment_date.date' => 'Format tanggal tidak valid',
                'payment_date.before_or_equal' => 'Tanggal pembayaran harus sebelum atau sama dengan tanggal check-in (' . $checkInDate->format('d M Y H:i') . ')',
            ]);

            $newPaymentDate = Carbon::parse($request->payment_date);

            // Update payment date
            if ($payment->transaction && $payment->transaction->paid_at) {
                $payment->transaction->update([
                    'paid_at' => $newPaymentDate
                ]);
            } elseif ($payment->verified_at) {
                $payment->update([
                    'verified_at' => $newPaymentDate
                ]);
            }

            return redirect()->back()->with('success', 'Tanggal pembayaran berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Update payment date failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui tanggal pembayaran. Error: ' . $e->getMessage());
        }
    }

    public function updateCheckInOut(Request $request, $id)
    {
        try {
            $payment = Payment::findOrFail($id);

            // Get current check-in and check-out dates
            $currentCheckIn = $payment->transaction?->check_in;
            $currentCheckOut = $payment->transaction?->check_out;

            if (!$currentCheckIn) {
                return redirect()->back()->with('error', 'Tanggal check-in tidak ditemukan');
            }

            $validationRules = [
                'check_in' => [
                    'required',
                    'date',
                    // 'before_or_equal:' . $currentCheckIn->format('Y-m-d H:i:s'),
                ],
            ];

            $validationMessages = [
                'check_in.required' => 'Tanggal check-in wajib diisi',
                'check_in.date' => 'Format tanggal check-in tidak valid',
                // 'check_in.before_or_equal' => 'Tanggal check-in harus sebelum atau sama dengan tanggal check-in saat ini (' . $currentCheckIn->format('d M Y H:i') . ')',
                'check_out.date' => 'Format tanggal check-out tidak valid',
                'check_out.after' => 'Tanggal check-out harus setelah tanggal check-in',
            ];

            // Add check-out validation - allow free date selection for check-out
            $validationRules['check_out'] = [
                'nullable',
                'date',
                'after:check_in',
            ];

            $request->validate($validationRules, $validationMessages);

            $newCheckIn = Carbon::parse($request->check_in);
            $newCheckOut = $request->check_out ? Carbon::parse($request->check_out) : null;

            // Update check-in and check-out dates
            if ($payment->transaction) {
                $updateData = ['check_in' => $newCheckIn];

                if ($newCheckOut) {
                    $updateData['check_out'] = $newCheckOut;
                }

                $payment->transaction->update($updateData);
            }

            return redirect()->back()->with('success', 'Tanggal check-in/check-out berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Update check-in/out failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui tanggal check-in/check-out. Error: ' . $e->getMessage());
        }
    }

    public function updateNotes(Request $request, $id)
    {
        try {
            $payment = Payment::findOrFail($id);

            $request->validate([
                'notes' => 'nullable|string|max:1000'
            ]);

            $payment->update([
                'notes' => $request->input('notes'),
                'updated_at' => now(),
                'updated_by' => Auth::id()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notes berhasil diperbarui',
                    'notes' => $payment->notes
                ]);
            }

            return redirect()->back()->with('success', 'Notes berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Update notes failed: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui notes. Error: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal memperbarui notes. Error: ' . $e->getMessage());
        }
    }

    /**
     * Release parking quota for the given order_id
     */
    private function releaseParkingQuota($order_id)
    {
        try {
            // Find all parking transactions for this order_id with 'paid' status
            $parkingTransactions = ParkingFeeTransaction::where('order_id', $order_id)
                ->where('transaction_status', 'paid')
                ->where('status', 1)
                ->get();

            foreach ($parkingTransactions as $transaction) {
                // Update parking transaction status to 'cancelled'
                $transaction->update([
                    'transaction_status' => 'cancelled'
                ]);

                // Decrement quota if parking fee has capacity limit (capacity > 0)
                if ($transaction->parking_fee_id) {
                    $parkingFee = ParkingFee::find($transaction->parking_fee_id);

                    if ($parkingFee && $parkingFee->capacity > 0) {
                        $parkingFee->decrementQuota(1);
                        Log::info("Parking quota released for cancelled order {$order_id}, parking type: {$parkingFee->parking_type}");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to release parking quota for order {$order_id}: " . $e->getMessage());
            // Don't throw exception, just log it - parking quota release shouldn't block cancellation
        }
    }
}
