<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DepositFeeTransaction;
use App\Models\DepositFeeTransactionImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DepositPaymentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 8);

        $query = DepositFeeTransaction::with(['depositFee', 'images', 'verifiedBy', 'createdBy', 'transaction.property', 'transaction.room'])
            ->orderBy('created_at', 'desc');

        $user = Auth::user();
        if ($user->isSite() && $user->property_id) {
            $query->whereHas('transaction', function($q) use ($user) {
                $q->where('property_id', $user->property_id);
            });
        }

        if ($request->has('search') && !empty($request->search)) {
            $query->where('order_id', 'like', "%{$request->search}%");
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('transaction_status', $request->status);
        }

        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $depositTransactions = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->withQueryString();

        if ($request->ajax() || $request->header('X-Requested-With') == 'XMLHttpRequest') {
            return view('pages.payment.deposit.partials.deposit-payment_table', compact('depositTransactions'));
        }

        return view('pages.payment.deposit.index', compact('depositTransactions'));
    }

    public function filter(Request $request)
    {
        $perPage = $request->input('per_page', 8);

        $query = DepositFeeTransaction::with(['depositFee', 'images', 'verifiedBy', 'createdBy', 'transaction.property', 'transaction.room'])
            ->orderBy('created_at', 'desc');

        $user = Auth::user();
        if ($user->isSite() && $user->property_id) {
            $query->whereHas('transaction', function($q) use ($user) {
                $q->where('property_id', $user->property_id);
            });
        }

        if (!empty($request->search)) {
            $query->where('order_id', 'like', "%{$request->search}%");
        }

        if (!empty($request->status)) {
            $query->where('transaction_status', $request->status);
        }

        if (!empty($request->date_from)) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if (!empty($request->date_to)) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $depositTransactions = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->appends($request->all());

        return response()->json([
            'html' => view('pages.payment.deposit.partials.deposit-payment_table', compact('depositTransactions'))->render(),
            'pagination' => $perPage !== 'all' && $depositTransactions instanceof \Illuminate\Pagination\LengthAwarePaginator
                ? $depositTransactions->links()->toHtml()
                : ''
        ]);
    }

    public function approve(Request $request, $id)
    {
        try {
            $transaction = DepositFeeTransaction::findOrFail($id);

            $transaction->update([
                'transaction_status' => 'paid',
                'paid_at' => now(),
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran deposit berhasil disetujui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|min:10',
        ]);

        try {
            $transaction = DepositFeeTransaction::findOrFail($id);

            $transaction->update([
                'transaction_status' => 'rejected',
                'notes' => $request->notes,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran deposit berhasil ditolak'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function viewProof($id)
    {
        $transaction = DepositFeeTransaction::with('images')->findOrFail($id);

        $images = $transaction->images->where('status', 1);

        if ($images->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada bukti pembayaran'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'images' => $images->map(function ($img) {
                return [
                    'id' => $img->idrec,
                    'image_url' => asset('storage/' . $img->image),
                    'image_type' => $img->image_type,
                    'description' => $img->description,
                ];
            })->values()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:t_transactions,order_id',
            'fee_amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'payment_proof' => 'required|image|mimes:jpeg,jpg|max:5120', // 5MB in kilobytes
            'notes' => 'nullable|string|max:1000',
        ], [
            'order_id.required' => 'Please select a booking order',
            'order_id.exists' => 'Selected booking order not found',
            'payment_proof.image' => 'Payment proof must be an image file',
            'payment_proof.mimes' => 'Payment proof must be a JPG/JPEG file',
            'payment_proof.max' => 'Payment proof size must not exceed 5MB',
        ]);

        try {
            DB::beginTransaction();

            // Get transaction details from order_id
            $bookingTransaction = \App\Models\Transaction::where('order_id', $request->order_id)->firstOrFail();

            // Get property details
            $property = \App\Models\Property::findOrFail($bookingTransaction->property_id);

            // Generate property initials (first letter of each word)
            $propertyWords = explode(' ', $property->name);
            $propertyInitials = '';
            foreach ($propertyWords as $word) {
                if (!empty($word)) {
                    $propertyInitials .= strtoupper(substr($word, 0, 1));
                }
            }
            if (strlen($propertyInitials) > 3) {
                $propertyInitials = substr($propertyInitials, 0, 3);
            }

            // Get current month and year
            $transactionDate = \Carbon\Carbon::parse($request->transaction_date);
            $month = $transactionDate->month;
            $year = $transactionDate->year;

            // Convert month to Roman numeral
            $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            $romanMonth = $romanMonths[$month - 1];

            // Get sequential number for this month/year/property
            $lastTransaction = DepositFeeTransaction::whereHas('transaction', function ($q) use ($bookingTransaction) {
                    $q->where('property_id', $bookingTransaction->property_id);
                })
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month)
                ->orderBy('idrec', 'desc')
                ->first();

            $sequentialNumber = 1;
            if ($lastTransaction && $lastTransaction->invoice_id) {
                // Extract number from last invoice_id (format: 0001/DEP-XX/KGA-INV/I/2026)
                preg_match('/^(\d+)\//', $lastTransaction->invoice_id, $matches);
                if (!empty($matches[1])) {
                    $sequentialNumber = intval($matches[1]) + 1;
                }
            }

            // Format sequential number with leading zeros
            $formattedNumber = str_pad($sequentialNumber, 4, '0', STR_PAD_LEFT);

            // Generate invoice ID: 0001/DEP-XX/KGA-INV/I/2026
            $invoiceId = sprintf(
                '%s/DEP-%s/KGA-INV/%s/%s',
                $formattedNumber,
                $propertyInitials,
                $romanMonth,
                $year
            );

            // Create deposit fee transaction with status 'paid' and already verified
            $transaction = DepositFeeTransaction::create([
                'invoice_id' => $invoiceId,
                'order_id' => $request->order_id,
                'fee_amount' => $request->fee_amount,
                'transaction_date' => $request->transaction_date,
                'transaction_status' => 'paid', // Already paid
                'paid_at' => now(),
                'verified_by' => Auth::id(), // Already verified
                'verified_at' => now(),
                'notes' => $request->notes,
                'status' => 1,
                'created_by' => Auth::id(),
            ]);

            // Handle image upload - Save as file
            if ($request->hasFile('payment_proof')) {
                $image = $request->file('payment_proof');

                // Create directory if not exists
                $uploadPath = storage_path('app/public/deposit_payments');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Generate unique filename (sanitize invoiceId by replacing / with -)
                $sanitizedInvoiceId = str_replace('/', '-', $invoiceId);
                $filename = $sanitizedInvoiceId . '_' . time() . '.' . $image->getClientOriginalExtension();

                // Store the file
                $image->move($uploadPath, $filename);

                // Save file path to database
                DepositFeeTransactionImage::create([
                    'deposit_transaction_id' => $transaction->idrec,
                    'image' => 'deposit_payments/' . $filename, // Store relative path
                    'image_type' => $image->getClientOriginalExtension(),
                    'description' => 'Payment proof uploaded by admin',
                    'status' => 1,
                    'created_by' => Auth::id(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Deposit payment added successfully',
                'data' => $transaction
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to add deposit payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCheckedInOrders(Request $request)
    {
        try {
            $propertyId = $request->get('property_id');

            // Query dari t_booking dengan join ke t_transactions
            $query = \App\Models\Booking::whereNotNull('check_in_at')
                ->whereNull('check_out_at') // Masih check-in, belum check-out
                ->where('status', 1) // Active booking
                ->with(['transaction' => function($q) {
                    $q->where('transaction_status', 'paid')
                        ->where('status', 1);
                }, 'property', 'room']);

            // Filter by property if user is site admin
            $user = Auth::user();
            if ($user->isSite() && $user->property_id) {
                $query->where('property_id', $user->property_id);
            } elseif ($propertyId) {
                $query->where('property_id', $propertyId);
            }

            $orders = $query->orderBy('check_in_at', 'desc')
                ->get()
                ->filter(function($booking) {
                    // Filter only bookings with paid transaction
                    return $booking->transaction && $booking->transaction->transaction_status === 'paid';
                })
                ->map(function($booking) {
                    return [
                        'order_id' => $booking->order_id,
                        'user_name' => $booking->transaction->user_name ?? $booking->user_name,
                        'user_phone' => $booking->transaction->user_phone_number ?? $booking->user_phone_number,
                        'room_name' => $booking->room->name ?? '-',
                        'property_name' => $booking->property->name ?? '-',
                        'check_in' => $booking->check_in_at ? $booking->check_in_at->format('d M Y') : '',
                        'check_out' => $booking->transaction && $booking->transaction->check_out
                            ? $booking->transaction->check_out->format('d M Y')
                            : '-',
                        'display_text' => $booking->order_id . ' - ' . ($booking->transaction->user_name ?? $booking->user_name) . ' (' . ($booking->room->name ?? '-') . ')',
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load checked-in orders: ' . $e->getMessage()
            ], 500);
        }
    }
}
