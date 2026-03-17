<?php

namespace App\Http\Controllers\Bookings\NewReservation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NewReservController extends Controller
{
    public function index()
    {
        $perPage = request('per_page', 8);

        // Use the filterBookings method to get the base query
        $query = $this->filterBookings();

        // Get paginated results
        $checkIns = $query->paginate($perPage);
        $showActions = true;

        return view('pages.bookings.newreservations.index', compact('checkIns', 'showActions'));
    }

    protected function filterBookings()
    {
        $query = Booking::with(['user', 'room', 'property', 'transaction'])
            ->where('t_booking.status', 1) // Only show active bookings (filter out room-changed old records)
            ->whereHas('transaction', function ($q) {
                $q->where('transaction_status', 'paid'); // Only paid transactions
            })
            ->whereNull('check_out_at') // Only bookings that haven't checked out
            ->join('t_transactions', 't_booking.order_id', '=', 't_transactions.order_id')
            ->orderByRaw('ISNULL(check_in_at) DESC')
            ->orderBy('t_transactions.check_in', 'desc');

        // Filter by property_id for site users
        $user = Auth::user();
        if ($user && $user->isSiteRole() && $user->property_id) {
            $query->where('t_booking.property_id', $user->property_id);
        }

        // Apply date filter only if user provides dates
        if (request()->filled('start_date') && request()->filled('end_date')) {
            $startDate = request('start_date');
            $endDate   = request('end_date');

            $query->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                if ($startDate === $endDate) {
                    $q->whereDate('check_in', $startDate);
                } else {
                    $q->whereBetween('check_in', [
                        $startDate . ' 00:00:00',
                        $endDate . ' 23:59:59'
                    ]);
                }
            });
        }

        // Search by order_id or user_name
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('t_booking.order_id', 'like', "%{$search}%")
                    ->orWhere('t_booking.user_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('username', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->select('t_booking.*');
    }

    public function filter(Request $request)
    {
        $query = $this->filterBookings();

        $checkIns = $query->paginate($request->input('per_page', 8));

        return response()->json([
            'table' => view('pages.bookings.newreservations.partials.newreserve_table', [
                'checkIns' => $checkIns,
                'per_page' => $request->input('per_page', 8),
            ])->render(),
            'pagination' => $checkIns->appends($request->input())->links()->toHtml(),
        ]);
    }

    public function checkIn(Request $request, $order_id)
    {
        try {
            $booking = Booking::where('order_id', $order_id)
                ->where('status', 1) // Get active booking only
                ->firstOrFail();

            if ($booking->check_in_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'This booking has already been checked in'
                ], 400);
            }

            // Conditional validation: only require doc_image if doc_path is null
            $rules = [
                'doc_type' => 'required|string|in:ktp,passport,sim,other',
                'has_profile_photo' => 'sometimes|boolean',
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|email|max:255',
                'guest_phone' => 'required|string|max:50',
                'guest_nik' => 'required|string|max:20',
            ];

            // Only require doc_image if booking doesn't have doc_path
            if (is_null($booking->doc_path)) {
                $rules['doc_image'] = 'required|file|mimes:jpg,jpeg,png|max:5120'; // 5MB
            } else {
                $rules['doc_image'] = 'sometimes|file|mimes:jpg,jpeg,png|max:5120'; // Optional
            }

            $validated = $request->validate($rules);

            $filePath = $booking->doc_path; // Keep existing doc_path if no new file uploaded

            // Only update doc_path if a new file is uploaded
            if ($request->hasFile('doc_image')) {
                $file = $request->file('doc_image');
                $fileName = 'doc_' . $booking->order_id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('documents', $fileName, 'public');
            }

            $updated = $booking->update([
                'check_in_at' => now(),
                'doc_type' => $validated['doc_type'],
                'doc_path' => $filePath,
                'updated_by' => Auth::id(),
                'verified_with_profile' => !empty($validated['has_profile_photo']),
                'user_name' => $validated['guest_name'],
                'user_email' => $validated['guest_email'],
                'user_phone_number' => $validated['guest_phone'],
            ]);

            // Update NIK pada user
            if ($booking->user) {
                $booking->user->update(['nik' => $validated['guest_nik']]);
            }

            if ($updated) {
                // Tentukan apakah perlu redirect ke print agreement
                // Hanya redirect jika ini adalah check-in pertama kali (doc_path baru di-upload dan is_printed = 0)
                $needPrintAgreement = $request->hasFile('doc_image') && $booking->is_printed == 0;

                return response()->json([
                    'success' => true,
                    'message' => 'Check-in successful',
                    'data' => $booking,
                    'need_print_agreement' => $needPrintAgreement,
                    'print_url' => $needPrintAgreement ? route('newReserv.checkin.regist', $order_id) : null,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Check-in update failed'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error during check-in: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getBookingDetails($orderId)
    {
        $booking = Booking::with(['transaction', 'property', 'room', 'transaction.user', 'user'])
            ->where('order_id', $orderId)
            ->where('status', 1) // Get active booking only
            ->firstOrFail();

        $response = $booking->toArray();

        // Tambahkan URL foto profil jika tersedia menggunakan APP_URL_IMAGES dari .env
        if ($booking->transaction->user && $booking->transaction->user->profile_photo_path) {
            $photoPath = $booking->transaction->user->profile_photo_path;
            $baseUrl = env('APP_URL_IMAGES', config('app.url'));
            $response['user_profile_photo_url'] = $baseUrl . '/storage/' . $photoPath;
        } else {
            $response['user_profile_photo_url'] = null;
        }

        return response()->json($response);
    }

    //Formulir Registrasi & Invoice
    public function getRegist($order_id)
    {
        try {
            // Ambil data transaksi dengan relasi
            $transaction = Transaction::where('order_id', $order_id)
                ->with(['user', 'property', 'room', 'booking'])
                ->firstOrFail();

            // Increment print counter berdasarkan order_id
            Booking::where('order_id', $order_id)->increment('is_printed');

            // Ambil ulang booking setelah update (optional)
            $booking = $transaction->booking;

            // Format data untuk view
            $bookingDetails = [
                'order_id' => $transaction->order_id,
                'check_in_date' => $transaction->check_in ? date('F d, Y', strtotime($transaction->check_in)) : 'N/A',
                'check_in_time' => $transaction->check_in_time ?? ($transaction->check_in ? date('H:i', strtotime($transaction->check_in)) : 'N/A'),
                'check_out_date' => $transaction->check_out ? date('F d, Y', strtotime($transaction->check_out)) : 'N/A',
                'check_out_time' => $transaction->check_out_time ?? ($transaction->check_out ? date('H:i', strtotime($transaction->check_out)) : 'N/A'),
                'guest_name' => $transaction->user_name ?? $transaction->user->first_name ?? 'N/A',
                'guest_email' => $transaction->user_email ?? $transaction->user->email ?? 'N/A',
                'guest_phone' => $transaction->user_phone_number ?? 'N/A',
                'property_name' => $transaction->property_name ?? $transaction->property->name ?? 'N/A',
                'room_name' => $transaction->room_name ?? $transaction->room->name ?? 'N/A',
                'room_number' => $transaction->room->no ?? 'N/A',
                'total_payment' => $transaction->grandtotal_price ? $this->formatRupiah($transaction->grandtotal_price) : 'N/A',
                'transaction_type' => $transaction->transaction_type ?? 'N/A',
                'duration' => $this->calculateDuration($transaction->check_in, $transaction->check_out),
                'guest_count' => $transaction->booking_days ?? 1,
                'advance_payment' => $transaction->grandtotal_price ? $this->formatRupiah($transaction->grandtotal_price) : 'N/A',
                'company_name' => '-',
                'daily_price' => $transaction->daily_price,
                'monthly_price' => $transaction->monthly_price,
            ];

            $guestContact = [
                'name' => $transaction->user_name ?? $transaction->user->first_name ?? 'N/A',
                'email' => $transaction->user_email ?? $transaction->user->email ?? 'N/A',
                'phone' => $transaction->user_phone_number ?? 'N/A',
                'address' => $transaction->user->address ?? '-',
            ];

            $currentDate = date('F d, Y');
            $logoPath = url('/images/frist_icon.png');

            // Create full doc URL - PERBAIKAN DI SINI
            $documentImage = null;
            if ($booking && $booking->doc_path) {
                // Gunakan Storage facade untuk generate URL yang benar
                $documentImage = Storage::url($booking->doc_path);

                // Pastikan URL lengkap (untuk kasus dimana Storage::url() hanya return relative path)
                if (!str_starts_with($documentImage, 'http')) {
                    $documentImage = url($documentImage);
                }
            }

            // Tentukan view berdasarkan harga yang ada
            $viewName = 'pages.bookings.components.regist_form_monthly'; // default

            if (!empty($transaction->daily_price) && empty($transaction->monthly_price)) {
                $viewName = 'pages.bookings.components.regist_form_daily';
            } elseif (!empty($transaction->monthly_price) && empty($transaction->daily_price)) {
                $viewName = 'pages.bookings.components.regist_form_monthly';
            }

            return view($viewName, compact(
                'bookingDetails',
                'guestContact',
                'currentDate',
                'logoPath',
                'documentImage'
            ));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load registration form: ' . $e->getMessage()
            ], 500);
        }
    }



    // Tambahkan method helper jika belum ada
    private function formatRupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    private function calculateDuration($check_in, $check_out)
    {
        if (!$check_in || !$check_out) {
            return 'N/A';
        }

        $start = new \DateTime($check_in);
        $end = new \DateTime($check_out);
        $interval = $start->diff($end);

        return $interval->days . ' Hari';
    }

    public function getInvoice($orderId)
    {
        $booking = Booking::with([
            'transaction',
            'property',
            'room',
            'transaction.user',
            'user',
            'payment'
        ])->where('order_id', $orderId)->firstOrFail();

        if (!$booking->transaction) {
            abort(404, 'Transaction data not found');
        }

        // Generate nomor invoice sesuai format: No. (id)/KGA-INV/(bulan)/(tahun)
        $transactionDate = $booking->transaction->transaction_date ?? now();
        $currentYear = $transactionDate->format('Y');
        $currentMonth = $transactionDate->format('m');

        // Ambil ID transaksi
        $transactionId = $booking->transaction->idrec;

        $invoiceNumberFormatted = "No.{$transactionId}/KGA-INV/{$currentMonth}/{$currentYear}";

        // Return view dengan data invoice number
        return view('pages.bookings.components.invoice', compact('booking', 'invoiceNumberFormatted'));
    }
}
