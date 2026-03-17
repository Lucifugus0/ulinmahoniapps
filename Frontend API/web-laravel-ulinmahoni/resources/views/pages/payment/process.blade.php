@php
    // Expect variables: $booking, $payment_method
    // Fallbacks for demo/testing
    $payment_method = $payment_method ?? request('payment_method', 'bca');
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment Process - Ulin Mahoni</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @include('components.homepage.styles')
    <script>if (localStorage.getItem('dark-mode') === 'true') document.documentElement.classList.add('dark');</script>
</head>
<body>
    @include('components.homepage.header')
    <div class="header-spacer"></div>
    <main>
        <section class="py-12 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Payment Instructions</h2>
                    <p class="text-gray-600 mt-1">Follow the instructions below to complete your payment.</p>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Payment Instructions -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            @if ($payment_method === 'bca')
                                <div class="mb-6">
                                    <div class="flex items-center mb-2">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" alt="BCA" class="h-8 mr-2">
                                        <span class="text-lg font-bold">Bank Transfer - BCA</span>
                                    </div>
                                    <p class="text-gray-700 mb-2">Please transfer the exact amount to the following BCA account:</p>
                                    <div class="bg-gray-100 rounded p-4 mb-2">
                                        <p class="text-sm text-gray-700">Account Name: <span class="font-bold">PT. Ulin Mahoni</span></p>
                                        <p class="text-sm text-gray-700">Account Number: <span class="font-bold">1234567890</span></p>
                                        <p class="text-sm text-gray-700">Bank: <span class="font-bold">BCA</span></p>
                                    </div>
                                    <p class="text-sm text-gray-600">After payment, please confirm your transfer by contacting our admin at <a href="https://wa.me/6281234567890" target="_blank" class="text-teal-600 underline">WhatsApp</a>.</p>
                                </div>
                            @elseif ($payment_method === 'cash')
                                <div class="mb-6">
                                    <div class="flex items-center mb-2">
                                        <span class="text-lg font-bold"><i class="fas fa-money-bill-wave mr-2"></i>Cash Payment</span>
                                    </div>
                                    <p class="text-gray-700 mb-2">You have selected to pay by cash.</p>
                                    <p class="text-sm text-gray-600">Please prepare the exact amount and pay at the property reception upon check-in.</p>
                                </div>
                            @else
                                <div class="mb-6">
                                    <p class="text-red-600">Invalid payment method selected.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- Booking Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Booking Summary</h3>
                            <div class="space-y-4">
                                <div>
                                    <h4 class="font-medium">Transaction Details</h4>
                                    <p class="text-sm text-gray-600 font-bold mb-6">Order ID: {{ $booking->order_id }}</p>
                                    <p class="text-sm text-gray-600">Code: {{ $booking->transaction_code }}</p>
                                    <p class="text-sm text-gray-600">Date: {{ $booking->created_at->format('d M Y H:i') }}</p>
                                    <p class="text-sm text-gray-600">Status: {{ ucfirst($booking->transaction_status) }}</p>
                                </div>
                                <div>
                                    <h4 class="font-medium">Guest Details</h4>
                                    <h4 class="font-medium text-gray-600">Name</h4>
                                    <p class="text-sm text-gray-600 mb-2">{{ $booking->user_name }}</p>
                                    <h4 class="font-medium text-gray-600">Email</h4>
                                    <p class="text-sm text-gray-600 mb-2">{{ $booking->user_email }}</p>
                                    <h4 class="font-medium text-gray-600">Phone Number</h4>
                                    <p class="text-sm text-gray-600 mb-2">{{ $booking->user_phone_number }}</p>
                                </div>
                                <div>
                                    <h4 class="font-medium">Property Details</h4>
                                    <p class="text-sm text-gray-600">{{ $booking->property_name }} ({{ $booking->room_name }})</p>
                                    <p class="text-sm text-gray-600">Type: {{ ucfirst($booking->property_type) }}</p>
                                </div>
                                <div>
                                    <h4 class="font-medium">Stay Period</h4>
                                    <p class="text-sm text-gray-600">Check-in: {{ $booking->check_in->format('d M Y') }}</p>
                                    <p class="text-sm text-gray-600">Check-out: {{ $booking->check_out->format('d M Y') }}</p>
                                    <p class="text-sm text-gray-600">Duration: {{ $booking->booking_days }} days</p>
                                </div>
                                <div class="pt-4 border-t">
                                    <h4 class="font-medium">Pricing</h4>
                                    <p class="text-sm text-gray-600">Daily Price: {{ number_format($booking->daily_price, 0) }}</p>
                                    <p class="text-sm text-gray-600">Room Price: {{ number_format($booking->room_price, 0) }}</p>
                                    <p class="text-sm text-gray-600">Admin Fees: {{ number_format($booking->admin_fees, 0) }}</p>
                                    <p class="text-sm font-medium text-gray-900 mt-2">Total: {{ number_format($booking->grandtotal_price, 0) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    @include('components.homepage.footer')
</body>
</html>
