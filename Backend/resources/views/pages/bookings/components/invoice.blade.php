<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            padding: 30px;
            color: #333;
        }

        .header {
            width: 100%;
            display: flex;
            justify-content: space-between;
        }

        .logo img {
            width: 120px;
        }

        .title {
            text-align: right;
        }

        .title h2 {
            margin: 0;
            font-size: 28px;
        }

        .line {
            margin-top: 10px;
            border-bottom: 2px solid #000;
        }

        .content-section {
            width: 100%;
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .data-pemesan {
            width: 48%;
        }

        .diterbitkan-oleh {
            width: 48%;
        }

        table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        table td {
            padding: 4px 0;
            font-size: 15px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 5px;
            style="border-bottom: 2px solid #000;"
        }

        .table-header {
            background-color: #538135;
            color: #fff;
            text-align: center;
            font-size: 14px;
        }

        .table-header th {
            padding: 6px;
            border: 1px solid #ddd;
        }

        .table-data td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 14px;
        }

        /* Perbaikan: Lebarkan kolom total */
        .total-table {
            width: 50%;
            /* Diperlebar dari 40% */
            margin-left: auto;
            border: 1px solid #ddd;
        }

        .total-table td {
            padding: 8px;
            /* Ditambah padding untuk ruang lebih */
            border: 1px solid #ddd;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .info-table .label {
            width: 140px;
            padding: 4px 0;
            font-weight: 600;
        }

        .info-table .value {
            padding: 4px 0;
        }

        .section-title {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .line {
            width: 100%;
            height: 1px;
            background: #ccc;
            margin: 8px 0;
        }

        /* Perbaikan: Pastikan warna tetap saat dicetak */
        @media print {
            .table-header {
                background-color: #538135 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }

            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        .bold {
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            font-size: 13px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>

    {{-- ========== HEADER ========== --}}
    <div class="header">
        <div class="logo">
            <img src="/images/UlinMahoni-logo.png" alt="Logo">
        </div>

        <div class="title">
            <h2 style="margin-bottom: 10px;">INVOICE</h2>

            <div>{{ $invoiceNumberFormatted }}</div>
            <div>
                {{ __('ui.invoice_date_label') }}
                {{ $booking->transaction->transaction_date
                    ? $booking->transaction->transaction_date->format('Y-m-d H:i:s')
                    : now()->format('Y-m-d H:i:s') }}
            </div>

        </div>

    </div>

    {{-- ========== DATA PEMESAN & DITERBITKAN OLEH ========== --}}
    <div class="content-section">
        {{-- DATA PEMESAN --}}
        <div class="data-pemesan">
            <div class="section-title">{{ __('ui.invoice_customer_data') }}</div>
            <div class="line"></div>

            {{-- Table Data Pemesan --}}
            <table class="info-table">
                <tr>
                    <td class="label">{{ __('ui.invoice_name_label') }}</td>
                    <td class="value">: {{ $booking->transaction->user_name ?? ($booking->user_name ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td class="label">{{ __('ui.invoice_phone_label') }}</td>
                    <td class="value">:
                        {{ $booking->transaction->user_phone_number ?? ($booking->user_phone_number ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td class="value">: {{ $booking->transaction->user_email ?? ($booking->user_email ?? 'N/A') }}</td>
                </tr>
            </table>

            <div class="line"></div>

            {{-- Table Detail Property --}}
            <table class="info-table">
                <tr>
                    <td class="label">Property</td>
                    <td class="value">:
                        {{ $booking->transaction->property_name ?? ($booking->property->name ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td class="label">{{ __('ui.invoice_room_label') }}</td>
                    <td class="value">: {{ $booking->transaction->room_name ?? ($booking->room->name ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td class="label">{{ __('ui.invoice_checkin_date') }}</td>
                    <td class="value">
                        :
                        {{ $booking->transaction->check_in
                            ? $booking->transaction->check_in->format('Y-m-d')
                            : ($booking->check_in_at
                                ? $booking->check_in_at->format('Y-m-d')
                                : 'N/A') }}
                    </td>
                </tr>
                <tr>
                    <td class="label">{{ __('ui.invoice_checkout_date') }}</td>
                    <td class="value">
                        :
                        {{ $booking->transaction->check_out
                            ? $booking->transaction->check_out->format('Y-m-d')
                            : ($booking->check_out_at
                                ? $booking->check_out_at->format('Y-m-d')
                                : 'N/A') }}
                    </td>
                </tr>
            </table>
        </div>


        {{-- DITERBITKAN OLEH --}}
        <div class="diterbitkan-oleh">
            <div class="section-title">{{ __('ui.invoice_issued_by') }}</div>
            <div class="line"></div>
            <table>
                <tr>
                    <td><strong>PT. Karya Graha Ayoda</strong></td>
                </tr>
                <tr>
                    <td>APL Tower - Central Park Lantai 39</td>
                </tr>
                <tr>
                    <td>Jl. Letjen S. Parman Kav. 28, Kel. Tanjung Duren Selatan,</td>
                </tr>
                <tr>
                    <td>Kec. Grogol Petamburan, Kota Jakarta Barat</td>
                </tr>
                <tr>
                    <td>Kode Pos: 11470</td>
                </tr>
                <tr>
                    <td>NPWP: 024127090436000</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ========== RINCIAN PEMBELIAN ========== --}}
    <div class="section-title">{{ __('ui.invoice_purchase_details') }}</div>

    <table>
        <thead class="table-header">
            <tr>
                <th>{{ __('ui.invoice_col_invoice_no') }}</th>
                <th>{{ __('ui.invoice_col_type') }}</th>
                <th>{{ __('ui.invoice_col_description') }}</th>
                <th>{{ __('ui.invoice_col_duration') }}</th>
                <th>{{ __('ui.invoice_col_booking_type') }}</th>
                <th>{{ __('ui.invoice_col_unit_price') }}</th>
                <th>{{ __('ui.invoice_col_total') }}</th>
            </tr>
        </thead>

        <tbody>
            <tr class="table-data">
                <td class="text-center">
                    {{ $invoiceNumberFormatted ?? ($booking->transaction->transaction_code ?? 'N/A') }}</td>
                <td>{{ $booking->transaction->property_type ?? ($booking->property->type ?? 'N/A') }}</td>
                <td>{{ $booking->transaction->room_name ?? ($booking->room->name ?? 'N/A') }}</td>
                <td>
                    @if (($booking->transaction->booking_type ?? 'daily') === 'monthly')
                        {{ $booking->transaction->booking_months ?? 0 }} Bulan
                    @else
                        {{ $booking->transaction->booking_days ?? 0 }} Hari
                    @endif
                </td>
                <td>{{ $booking->transaction->booking_type ?? 'daily' }}</td>
                <td>
                    Rp
                    @if (($booking->transaction->booking_type ?? 'daily') === 'monthly')
                        {{ number_format($booking->transaction->monthly_price ?? 0, 0, ',', '.') }}
                    @else
                        {{ number_format($booking->transaction->daily_price ?? 0, 0, ',', '.') }}
                    @endif
                </td>

                <td>Rp {{ number_format($booking->transaction->room_price ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ========== TOTAL ========== --}}
    <table class="total-table">
        <tr>
            <td>{{ __('ui.invoice_subtotal') }}</td>
            <td class="text-right">Rp {{ number_format($booking->transaction->room_price ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>{{ __('ui.invoice_tax_service') }}</td>
            <td class="text-right">Rp {{ number_format($booking->transaction->admin_fees ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>DEPOSIT</td>
            <td class="text-right">Rp {{ number_format($booking->transaction->deposit ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr class="bold">
            <td>{{ __('ui.invoice_total_payment') }}</td>
            <td class="text-right">Rp {{ number_format($booking->transaction->grandtotal_price ?? 0, 0, ',', '.') }}
            </td>
        </tr>
    </table>

    {{-- ========== DETAIL TRANSAKSI ========== --}}
    <div class="footer">
        <strong>{{ __('ui.invoice_transaction_details') }}</strong> <br>
        Order ID: {{ $booking->order_id }} <br>
        {{ __('ui.invoice_transaction_code') }} {{ $booking->transaction->transaction_code ?? 'N/A' }} <br>
        Status: {{ $booking->transaction->transaction_status ?? 'pending' }} <br>

        @if ($booking->transaction->paid_at)
            {{ __('ui.invoice_payment_date') }} {{ $booking->transaction->paid_at->format('Y-m-d H:i:s') }} <br>
        @endif

        {{ __('ui.invoice_payment_via') }} {{ $booking->transaction->transaction_type ?? 'N/A' }}
    </div>


</body>

</html>
