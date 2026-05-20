<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>

    <style>
        /* <!-- A4 portrait page sizing for print + on-screen preview --> */
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        html, body {
            background: #f3f4f6;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
            /* On screen we render a paper-like A4 sheet centered with internal padding;
               in print the @page margin owns the page padding (see @media print below). */
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 15mm;
            background: #fff;
            box-sizing: border-box;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        /* <!-- ===== Header: logo left, INVOICE title block right ===== --> */
        .header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .logo img {
            width: 110px;
        }

        .title {
            text-align: right;
        }

        .title h1 {
            margin: 0 0 12px 0;
            font-size: 30px;
            letter-spacing: 1px;
        }

        .title .meta {
            font-size: 13px;
            color: #444;
            line-height: 1.6;
        }

        /* <!-- ===== Two-column block: customer data vs issuing company ===== --> */
        .content-section {
            width: 100%;
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .col-left {
            width: 52%;
        }

        .col-right {
            width: 44%;
        }

        /* <!-- Section heading: bold + underline, matching the template ===== --> */
        .section-title {
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            border-bottom: 1.5px solid #000;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .info-table .label {
            width: 70px;
            padding: 2px 0;
            vertical-align: top;
        }

        .info-table .sep {
            width: 12px;
            padding: 2px 0;
            vertical-align: top;
        }

        .info-table .value {
            padding: 2px 0;
            vertical-align: top;
        }

        /* <!-- Italic note lines under the customer data (property + check-in) --> */
        .note-line {
            font-size: 13px;
            font-style: italic;
            margin-top: 10px;
        }

        .company-name {
            font-weight: 700;
            font-size: 13px;
        }

        .company-detail {
            font-size: 13px;
            line-height: 1.6;
            margin-top: 2px;
        }

        /* <!-- ===== Purchase details table ===== --> */
        .purchase-title {
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            margin-top: 32px;
            margin-bottom: 6px;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }

        .detail-table th {
            background-color: #7a9c54;
            color: #fff;
            text-align: center;
            font-size: 12px;
            padding: 8px 6px;
            border: 1px solid #cdd6c0;
        }

        .detail-table td {
            border: 1px solid #cdd6c0;
            padding: 8px 6px;
            font-size: 12px;
        }

        .detail-table .data-row td {
            height: 28px;
        }

        /* <!-- Totals rows hang under the last two columns of the table --> */
        .totals-blank {
            border: none !important;
        }

        .totals-label {
            background-color: #f1f1f1;
            font-weight: 600;
            text-align: center;
        }

        .totals-label.grand {
            background-color: #c7d8ac;
        }

        .totals-value.grand {
            background-color: #dde7cd;
            font-weight: 700;
        }

        .tax-note {
            font-size: 11px;
            font-style: italic;
            margin-top: 14px;
        }

        /* <!-- ===== Transaction details + footer disclaimer ===== --> */
        .transaction-details {
            margin-top: 28px;
            font-size: 12px;
            line-height: 1.7;
        }

        .transaction-details .heading {
            font-weight: 700;
        }

        .disclaimer {
            margin-top: 70px;
            font-size: 11px;
            color: #444;
            line-height: 1.7;
            text-align: justify;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* <!-- Keep colours when printing --> */
        @media print {
            html, body {
                background: #fff;
            }

            body {
                width: auto;
                min-height: 0;
                margin: 0;
                padding: 0;
                box-shadow: none;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }

            .detail-table th,
            .totals-label,
            .totals-label.grand,
            .totals-value.grand {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

    {{-- ========== HEADER ========== --}}
    <div class="header">
        <div class="logo">
            <img src="/images/UlinMahoni-logo.png" alt="Ulin Mahoni">
        </div>

        <div class="title">
            <h1>{{ __('ui.invoice_title') }}</h1>
            <div class="meta">
                {{-- Invoice number: persisted t_transactions.invoice_number, or '-' when not yet assigned --}}
                <div>No. {{ $invoiceNumberFormatted }}</div>
                <div>
                    {{ __('ui.invoice_date_label') }}
                    {{ $booking->transaction->transaction_date
                        ? $booking->transaction->transaction_date->translatedFormat('d F Y, H:i')
                        : now()->translatedFormat('d F Y, H:i') }}
                </div>
            </div>
        </div>
    </div>

    {{-- ========== DATA PEMESAN & DITERBITKAN ATAS NAMA ========== --}}
    <div class="content-section">

        {{-- DATA PEMESAN (customer) --}}
        <div class="col-left">
            <div class="section-title">{{ __('ui.invoice_customer_data') }}</div>

            <table class="info-table">
                <tr>
                    <td class="label">{{ __('ui.invoice_name_label') }}</td>
                    <td class="sep">:</td>
                    <td class="value">{{ $booking->transaction->user_name ?? ($booking->user_name ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td class="label">{{ __('ui.invoice_phone_label') }}</td>
                    <td class="sep">:</td>
                    <td class="value">{{ $booking->transaction->user_phone_number ?? ($booking->user_phone_number ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td class="sep">:</td>
                    <td class="value">{{ $booking->transaction->user_email ?? ($booking->user_email ?? 'N/A') }}</td>
                </tr>
            </table>

            {{-- Property/room chosen — the "(keterangan ... yg dipilih)" template line --}}
            <div class="note-line">
                {{ $booking->transaction->property_name ?? ($booking->property->name ?? 'N/A') }}
                &mdash;
                {{ $booking->transaction->room_name ?? ($booking->room->name ?? 'N/A') }}
            </div>

            {{-- Check-in date — the "(tgl check in)" template line --}}
            <div class="note-line">
                {{ __('ui.invoice_checkin_date') }}:
                {{ $booking->transaction->check_in
                    ? $booking->transaction->check_in->translatedFormat('d F Y')
                    : ($booking->check_in_at
                        ? $booking->check_in_at->translatedFormat('d F Y')
                        : 'N/A') }}
            </div>
        </div>

        {{-- DITERBITKAN ATAS NAMA (issuing company) --}}
        <div class="col-right">
            <div class="section-title">{{ __('ui.invoice_issued_in_name_of') }}</div>
            <div class="company-name">PT. Karya Graha Ayoda</div>
            <div class="company-detail">
                APL Tower &mdash; Central Park Lantai 39,<br>
                Jl. Letjen S. Parman Kav. 28, Kel. Tanjung Duren Selatan,<br>
                Kec. Grogol Petamburan, Kota Jakarta Barat<br>
                Kode Pos: 11470, NPWP: 024127090436000
            </div>
        </div>
    </div>

    {{-- ========== RINCIAN PEMBELIAN ========== --}}
    <div class="purchase-title">{{ __('ui.invoice_purchase_details') }}</div>

    @php
        // <!-- Resolve booking type and duration once for the table row -->
        $bookingType = $booking->transaction->booking_type ?? 'daily';
        $isMonthly = $bookingType === 'monthly';
        $duration = $isMonthly
            ? ($booking->transaction->booking_months ?? 0) . ' ' . __('ui.invoice_months')
            : ($booking->transaction->booking_days ?? 0) . ' ' . __('ui.invoice_days');
        $unitPrice = $isMonthly
            ? ($booking->transaction->monthly_price ?? 0)
            : ($booking->transaction->daily_price ?? 0);

        // <!-- Money values for the totals block -->
        $roomPrice  = $booking->transaction->room_price ?? 0;
        $discount   = $booking->transaction->discount_amount ?? 0;
        // <!-- Sub Total = room price minus any voucher/promo discount -->
        $subTotal   = $roomPrice - $discount;

        // <!-- Parking: only show a separate row when parking_fee > 0 -->
        $parkingFee      = $booking->transaction->parking_fee ?? 0;
        $parkingType     = $booking->transaction->parking_type ?? null;
        $parkingDuration = $booking->transaction->parking_duration ?? null;

        // <!-- Total Payment = Sub Total + Parking (excludes service fee & deposit) -->
        $totalPayment = $subTotal + $parkingFee;
    @endphp

    <table class="detail-table">
        <thead>
            <tr>
                <th style="width: 16%;">{{ __('ui.invoice_col_property') }}</th>
                <th style="width: 10%;">{{ __('ui.invoice_col_type') }}</th>
                <th style="width: 20%;">{{ __('ui.invoice_col_description') }}</th>
                <th style="width: 10%;">{{ __('ui.invoice_col_quantity') }}</th>
                <th style="width: 14%;">{{ __('ui.invoice_col_booking_type') }}</th>
                <th style="width: 15%;">{{ __('ui.invoice_col_unit_price') }}</th>
                <th style="width: 15%;">{{ __('ui.invoice_col_total') }}</th>
            </tr>
        </thead>
        <tbody>
            {{-- Room rental line --}}
            <tr class="data-row">
                <td>{{ $booking->transaction->property_name ?? ($booking->property->name ?? 'N/A') }}</td>
                <td>{{ $booking->transaction->property_type ?? ($booking->property->type ?? 'N/A') }}</td>
                <td>{{ $booking->transaction->room_name ?? ($booking->room->name ?? 'N/A') }}</td>
                <td class="text-center">{{ $duration }}</td>
                <td class="text-center">{{ ucfirst($bookingType) }}</td>
                <td class="text-right">Rp {{ number_format($unitPrice, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($roomPrice, 0, ',', '.') }}</td>
            </tr>

            {{-- Discount row: immediately after the room row, before parking --}}
            <tr>
                <td class="totals-blank" colspan="5"></td>
                <td class="totals-label">{{ __('ui.invoice_discount') }}</td>
                {{-- Discount is applied to room price only --}}
                <td class="text-right">Rp {{ number_format($discount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="totals-blank" colspan="5"></td>
                <td class="totals-label">{{ __('ui.invoice_subtotal') }}</td>
                {{-- Sub Total = room_price - discount_amount (before adding parking) --}}
                <td class="text-right">Rp {{ number_format($subTotal, 0, ',', '.') }}</td>
            </tr>

            {{-- Parking line: only rendered when a parking fee was charged on this transaction --}}
            @if ($parkingFee > 0)
            <tr class="data-row">
                <td>{{ $booking->transaction->property_name ?? ($booking->property->name ?? 'N/A') }}</td>
                <td>{{ $booking->transaction->property_type ?? ($booking->property->type ?? 'N/A') }}</td>
                {{-- Description: "Parkir {type}" e.g. "Parkir Motor" --}}
                <td>{{ __('ui.invoice_parking') }}{{ $parkingType ? ' ' . ucfirst($parkingType) : '' }}</td>
                <td class="text-center">{{ $parkingDuration ?? '-' }}</td>
                <td class="text-center">{{ __('ui.invoice_parking_addon') }}</td>
                <td class="text-right">Rp {{ number_format($parkingFee, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($parkingFee, 0, ',', '.') }}</td>
            </tr>
            @endif

            {{-- Total Payment = Sub Total + Parking (excludes service_fees & deposit_fee) --}}
            <tr>
                <td class="totals-blank" colspan="5"></td>
                <td class="totals-label grand">{{ __('ui.invoice_total_payment') }}</td>
                <td class="totals-value grand text-right">Rp {{ number_format($totalPayment, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="tax-note">{{ __('ui.invoice_price_includes_tax') }}</div>

    {{-- ========== DETAIL TRANSAKSI ========== --}}
    <div class="transaction-details">
        <span class="heading">{{ __('ui.invoice_transaction_details') }}</span><br>
        Order ID: {{ $booking->order_id }}<br>
        {{ __('ui.invoice_transaction_code') }} {{ $booking->transaction->transaction_code ?? 'N/A' }}<br>
        Status: {{ ucfirst($booking->transaction->transaction_status ?? 'pending') }}<br>
        @if ($booking->transaction->paid_at)
            {{ __('ui.invoice_payment_date') }} {{ $booking->transaction->paid_at->format('Y-m-d H:i:s') }}<br>
        @endif
        {{ __('ui.invoice_payment_via') }} {{ $booking->transaction->transaction_type ?? 'N/A' }}
    </div>

    {{-- ========== FOOTER DISCLAIMER ========== --}}
    <div class="disclaimer">{{ __('ui.invoice_disclaimer') }}</div>

</body>

</html>
