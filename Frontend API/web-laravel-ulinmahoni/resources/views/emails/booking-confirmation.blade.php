<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - Ulinmahoni</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .header p {
            font-size: 14px;
            opacity: 0.95;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #1f2937;
        }
        .message {
            font-size: 15px;
            color: #4b5563;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .booking-details {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 30px;
        }
        .booking-details h2 {
            font-size: 18px;
            color: #1f2937;
            margin-bottom: 16px;
            font-weight: 600;
        }
        .detail-row {
            display: table;
            width: 100%;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            display: table-cell;
            width: 55%;
            font-weight: 500;
            color: #6b7280;
            font-size: 14px;
            padding-right: 16px;
            vertical-align: middle;
        }
        .detail-value {
            display: table-cell;
            width: 45%;
            font-weight: 600;
            color: #1f2937;
            text-align: right;
            font-size: 14px;
            vertical-align: middle;
        }
        .total-row {
            background-color: #ecfdf5;
            margin: -24px -24px 0;
            padding: 16px 24px;
            border-top: 2px solid #0d9488;
        }
        .total-row .detail-label {
            color: #047857;
            font-size: 16px;
        }
        .total-row .detail-value {
            color: #047857;
            font-size: 18px;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s;
        }
        .button:hover {
            transform: translateY(-2px);
        }
        .info-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 16px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box p {
            color: #92400e;
            font-size: 14px;
            margin: 0;
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 8px;
        }
        .footer a {
            color: #0d9488;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        @media only screen and (max-width: 600px) {
            .content {
                padding: 30px 20px;
            }
            .booking-details {
                padding: 20px;
            }
            .detail-row {
                display: block;
            }
            .detail-label,
            .detail-value {
                display: block;
                width: 100%;
                text-align: left;
                padding-right: 0;
            }
            .detail-value {
                margin-top: 2px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🎉 Booking Confirmed! / Pemesanan Dikonfirmasi!</h1>
            <p>Your reservation has been successfully created / Reservasi Anda telah berhasil dibuat</p>
        </div>

        <div class="content">
            <div class="greeting">
                Dear / Kepada Yth {{ $user->first_name }} {{ $user->last_name }},
            </div>

            <div class="message">
                Thank you for choosing Ulinmahoni! We're excited to confirm your booking. Below are the details of your reservation.<br>
                <em>Terima kasih telah memilih Ulinmahoni! Kami dengan senang hati mengkonfirmasi pemesanan Anda. Berikut adalah detail reservasi Anda.</em>
            </div>

            <div class="booking-details">
                <h2>Booking Details / Detail Pemesanan</h2>

                <div class="detail-row">
                    <span class="detail-label">Order ID / ID Pesanan</span>
                    <span class="detail-value">{{ $transaction['order_id'] }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Property / Properti</span>
                    <span class="detail-value">{{ $transaction['property_name'] }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Room / Kamar</span>
                    <span class="detail-value">{{ $transaction['room_name'] }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Check-in</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($transaction['check_in'])->format('d M Y') }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Check-out</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($transaction['check_out'])->format('d M Y') }}</span>
                </div>

                @if(isset($transaction['booking_days']) && $transaction['booking_days'])
                <div class="detail-row">
                    <span class="detail-label">Duration / Durasi</span>
                    <span class="detail-value">{{ $transaction['booking_days'] }} {{ $transaction['booking_days'] > 1 ? 'nights / malam' : 'night / malam' }}</span>
                </div>
                @elseif(isset($transaction['booking_months']) && $transaction['booking_months'])
                <div class="detail-row">
                    <span class="detail-label">Duration / Durasi</span>
                    <span class="detail-value">{{ $transaction['booking_months'] }} {{ $transaction['booking_months'] > 1 ? 'months / bulan' : 'month / bulan' }}</span>
                </div>
                @endif

                <div class="detail-row">
                    <span class="detail-label">Room Price / Harga Kamar</span>
                    <span class="detail-value">Rp {{ number_format($transaction['room_price'], 0, ',', '.') }}</span>
                </div>

                @if(isset($transaction['service_fees']) && $transaction['service_fees'] > 0)
                <div class="detail-row">
                    <span class="detail-label">Service Fee / Biaya Layanan</span>
                    <span class="detail-value">Rp {{ number_format($transaction['service_fees'], 0, ',', '.') }}</span>
                </div>
                @endif

                @if(isset($transaction['admin_fees']) && $transaction['admin_fees'] > 0)
                <div class="detail-row">
                    <span class="detail-label">Admin Fee / Biaya Admin</span>
                    <span class="detail-value">Rp {{ number_format($transaction['admin_fees'], 0, ',', '.') }}</span>
                </div>
                @endif

                <div class="total-row">
                    <div class="detail-row" style="border: none; padding: 0;">
                        <span class="detail-label">Grand Total / Total Keseluruhan</span>
                        <span class="detail-value">Rp {{ number_format($transaction['grandtotal_price'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            @if($paymentUrl)
            <div class="info-box">
                <p><strong>⏰ Payment Required / Pembayaran Diperlukan:</strong> Please complete your payment within the specified time to confirm your booking. / Harap selesaikan pembayaran Anda dalam waktu yang ditentukan untuk mengkonfirmasi pemesanan Anda.</p>
            </div>

            <div class="button-container">
                <a href="{{ $paymentUrl }}" class="button">Complete Payment Now / Bayar Sekarang</a>
            </div>
            @endif

            <div class="message" style="margin-top: 30px;">
                If you have any questions or need assistance, please don't hesitate to contact our support team.<br>
                <em>Jika Anda memiliki pertanyaan atau memerlukan bantuan, jangan ragu untuk menghubungi tim dukungan kami.</em>
            </div>

            <div class="message" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <strong>Important / Penting:</strong> Please keep this email for your records. You will need your Order ID for check-in. / Harap simpan email ini untuk catatan Anda. Anda akan memerlukan ID Pesanan untuk check-in.
            </div>
        </div>

        <div class="footer">
            <p><strong>Ulinmahoni</strong></p>
            <p>Your trusted accommodation partner / Mitra akomodasi terpercaya Anda</p>
            <p style="margin-top: 16px;">
                <a href="{{ config('app.url') }}">Visit our website / Kunjungi website kami</a> |
                <a href="{{ config('app.url') }}/contact">Contact Support / Hubungi Dukungan</a>
            </p>
            <p style="margin-top: 16px; font-size: 12px;">
                This is an automated email. Please do not reply to this message.<br>
                <em>Ini adalah email otomatis. Harap jangan membalas pesan ini.</em>
            </p>
        </div>
    </div>
</body>
</html>
