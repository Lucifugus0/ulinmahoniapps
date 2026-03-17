<!DOCTYPE html>
<html>

<head>
    <title>Registration Form - {{ $bookingDetails['order_id'] }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css');

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 8px;
            background: #fff;
            color: #000;
            font-size: 10px;
            line-height: 1.15;
        }

        .header {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #000;
            padding-bottom: 6px;
            margin-bottom: 6px;
            position: relative;
        }

        .logo-container {
            flex-shrink: 0;
            margin-right: 10px;
        }

        .header img {
            height: 32px;
            filter: grayscale(100%) contrast(120%);
        }

        .header-content {
            flex: 1;
            text-align: center;
        }

        .header h2 {
            font-size: 14px;
            font-weight: 700;
            margin: 2px 0 1px 0;
        }

        .header p {
            margin: 0;
            font-size: 11px;
            font-weight: 500;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin-bottom: 4px;
        }

        td {
            border: 1px solid #000;
            padding: 2px 3px;
            vertical-align: top;
        }

        .section-title {
            font-weight: 700;
            font-size: 10px;
            margin: 4px 0 2px 0;
            text-decoration: underline;
        }

        .notes,
        .disclaimer {
            font-size: 8px;
            margin-top: 4px;
            line-height: 1.15;
        }

        .bottom-section {
            margin-top: 4px;
            border-top: 1px solid #000;
            padding-top: 4px;
            font-size: 8px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            font-size: 8px;
            height: 100px;
        }

        .info-table td {
            border: 1px solid #000;
            padding: 3px;
            vertical-align: top;
        }

        .info-table .left-col {
            width: 65%;
        }

        .info-table .right-col {
            width: 35%;
            text-align: center;
        }

        .signature-container {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .document-section {
            flex: 1;
            margin-bottom: 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100px; /* Diperbesar dari 70px */
            padding: 4px; /* Diperbesar dari 2px */
        }

        .document-image {
            max-width: 100%;
            max-height: 90px; /* Diperbesar dari 60px */
            object-fit: contain;
        }

        .document-label {
            font-size: 7px;
            margin-top: 2px;
            font-weight: bold;
        }

        .signature-section {
            padding: 4px;
            text-align: center;
        }

        .signature-title {
            margin-bottom: 6px;
        }

        .signature-spacing {
            height: 30px;
            width: 100%;
            margin-bottom: 4px;
        }

        .right-signature-box {
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 3px;
        }

        .right-icons {
            font-size: 10px;
        }

        .right-icons i {
            margin: 0 2px;
        }

        .icon-ban {
            position: relative;
            display: inline-block;
            width: 15px;
            height: 15px;
        }

        .main-icon {
            width: 100%;
            height: 100%;
            display: block;
        }

        .ban-custom {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: block;
        }

        .compact-text {
            font-size: 8px;
            line-height: 1.15;
            margin: 1px 0;
        }

        .penalty-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            font-size: 8px;
        }

        .penalty-table td {
            border: 1px solid #000;
            padding: 3px;
            vertical-align: top;
        }

        .penalty-title {
            font-weight: 700;
        }

        .id-text {
            font-style: normal;
            font-weight: normal;
        }

        .en-text {
            font-style: italic;
            font-weight: normal;
        }

        .penalty-content {
            padding: 4px;
            line-height: 1.2;
        }

        .penalty-content ol {
            margin: 3px 0 3px 12px;
            padding-left: 4px;
        }

        .penalty-content li {
            margin-bottom: 1px;
        }

        /* New styles for vehicle form */
        .vehicle-title {
            font-weight: 700;
            font-size: 12px;
            margin: 8px 0 4px 0;
            text-align: center;
            text-decoration: underline;
        }

        .no-border {
            border: none !important;
        }

        .checkbox-box {
            display: inline-block;
            width: 8px;
            height: 8px;
            border: 1px solid #000;
            margin-right: 4px;
        }

        .signature-box {
            height: 25px;
            border: 1px solid #000;
            margin-top: 4px;
        }

        .vehicle-section {
            margin-top: 6px;
        }

        .compact-list {
            font-size: 8px;
            line-height: 1.15;
            margin: 3px 0;
        }

        /* New styles for two-column layout */
        .two-column-container {
            display: flex;
            gap: 6px;
            margin-top: 6px;
        }

        .penalty-column {
            flex: 1.5;
        }

        .vehicle-column {
            flex: 1;
        }

        .vehicle-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            margin-bottom: 4px;
        }

        .vehicle-table td {
            border: 1px solid #000;
            padding: 2px 3px;
            vertical-align: top;
        }

        /* New styles for compact layout */
        .compact-table {
            margin-bottom: 2px;
        }
        
        .compact-table td {
            padding: 1px 2px;
        }
        
        .compact-section {
            margin-top: 2px;
        }

        @media print {
            body {
                margin: 0;
                padding: 6px;
                font-size: 9px;
                line-height: 1.1;
            }

            .header {
                margin-bottom: 4px;
                padding-bottom: 4px;
            }

            .header img {
                height: 30px;
            }

            table {
                font-size: 8px;
                margin-bottom: 3px;
            }

            td {
                padding: 1px 2px;
            }

            .info-table {
                height: 90px;
            }

            .compact-text {
                font-size: 7px;
                line-height: 1.1;
            }

            .document-section {
                min-height: 95px; /* Diperbesar dari 65px */
            }

            .compact-list {
                font-size: 7px;
            }

            .two-column-container {
                gap: 4px;
            }
            
            .signature-spacing {
                height: 25px;
            }
        }

        @page {
            size: A4;
            margin: 7mm;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo-container">
            <img src="{{ $logoPath }}" alt="Logo Ulin Mahoni" onerror="this.style.display='none'">
        </div>
        <div class="header-content">
            <h2>ULIN MAHONI</h2>
            <p><span class="en-text">REGISTRATION FORM</span> / FORMULIR PENDAFTARAN</p>
        </div>
    </div>

    <table class="compact-table">
        <tr>
            <td><span class="en-text"><strong>Folio Number</span> / Nomor
                Folio</strong><br>{{ $bookingDetails['order_id'] }}</td>
            <td><span class="en-text"><strong>Arrival Date</span> / Tanggal
                Kedatangan</strong><br>{{ $bookingDetails['check_in_date'] }}</td>
            <td><span class="en-text"><strong>ETA</span></strong><br>{{ $bookingDetails['check_in_time'] }}</td>
            <td><span class="en-text"><strong>Departure Date</span> / Tanggal
                Keberangkatan</strong><br>{{ $bookingDetails['check_out_date'] }}</td>
            <td><span class="en-text"><strong>ETD</span></strong><br>{{ $bookingDetails['check_out_time'] }}</td>
        </tr>
        <tr>
            <td><span class="en-text"><strong>Rate Per Night</span> / Harga Per
                Malam</strong><br>{{ $bookingDetails['total_payment'] }}</td>
            <td><span class="en-text"><strong>Number of Guest</span> / Jumlah
                Tamu</strong><br>{{ $bookingDetails['guest_count'] }}</td>
            <td><span class="en-text"><strong>Advance Payment</span> / Uang
                Muka</strong><br>{{ $bookingDetails['advance_payment'] }}</td>
            <td><span class="en-text"><strong>Room Type</span> / Jenis
                Kamar</strong><br>{{ $bookingDetails['room_name'] }}</td>
            <td><span class="en-text"><strong>Room Number</span> / Nomor
                Kamar</strong><br>{{ $bookingDetails['room_number'] }}</td>
        </tr>
    </table>

    <div class="section-title"><span class="en-text"><strong>Guest Information</span> / Informasi Tamu</div>
    <table class="compact-table">
        <tr>
            <td><span class="en-text"><strong>Name (Mr/Mrs/Miss)</span> / Nama
                (Tn/Ny/Nona)</strong><br>{{ $guestContact['name'] }}</td>
            <td><span class="en-text"><strong>Nationality</span> / Kewarganegaraan</strong><br>Indonesia</td>
            <td><span class="en-text"><strong>Date of Birth</span> / Tanggal Lahir</strong><br>-</td>
        </tr>
        <tr>
            <td><span class="en-text"><strong>ID Card / SIM / Passport No.</span></strong><br>-</td>
            <td><span class="en-text"><strong>Company</span> /
                Perusahaan</strong><br>{{ $bookingDetails['company_name'] }}</td>
            <td><span class="en-text"><strong>Telephone</span> / Telepon</strong><br>{{ $guestContact['phone'] }}</td>
        </tr>
        <tr>
            <td colspan="3"><span class="en-text"><strong>Home Address</span> / Alamat
                Rumah</strong><br>{{ $guestContact['address'] }}</td>
        </tr>
        <tr>
            <td><span class="en-text"><strong>Terms of Payment</span> / Cara
                Pembayaran</strong><br>{{ $bookingDetails['transaction_type'] }}</td>
            <td><span class="en-text"><strong>Credit Card</span> / Kartu Kredit</strong><br>-</td>
            <td><span class="en-text"><strong>Visa Type</span> / Jenis Visa</strong><br><span class="en-text">Tourist /
                    Wisatawan</span></td>
        </tr>
    </table>
    
    <table class="info-table">
        <tr>
            <td class="left-col">
                <div class="compact-text">
                    <strong>SYARAT DAN KETENTUAN</strong><br>

                    <span class="id-text">
                        Penyewa wajib:<br>
                        a. Menggunakan Kamar Kost hanya untuk tempat tinggal dengan sebaik-baiknya.<br>
                        b. Menjaga, merawat dan memelihara Kamar Kost dengan sebaik-baiknya.<br>
                        c. Mematikan lampu, AC, dan mencabut barang elektronik lainnya apabila Penyewa meninggalkan
                        Kamar Kost.<br>
                        d. Menjaga barang berharga dan barang pribadi milik Penyewa dan/atau tamu.<br>
                        e. Menutup pintu Kamar Kost dalam segala kondisi.<br>
                        f. Menutup serta mengunci pintu dan gerbang kost pada saat Penyewa masuk dan keluar kost.<br>
                        g. Menjaga, merawat dan memelihara fasilitas bersama yang ada di kost tanpa terkecuali.<br>
                        h. Memberitahu Ulin Mahoni apabila ada tamu yang menginap.<br>
                        i. Membayar biaya tambahan untuk tamu (khusus keluarga dan/atau teman bukan lawan jenis) yang
                        menginap
                        sebesar <strong>Rp. 100.000,-</strong> per hari dengan maksimal tamu menginap selama 3 hari.<br>
                        j. Membayar biaya tambahan sebesar setengah biaya sewa apabila tamu (khusus keluarga dan/atau
                        teman bukan lawan jenis)
                        menginap lebih dari 3 hari.<br>
                        k. Memberitahukan Ulin Mahoni apabila Penyewa membawa alat elektronik sendiri.<br>
                        l. Mengunci dan/atau menggunakan kunci pada kendaraan bermotor baik milik Penyewa maupun tamu
                        Penyewa.<br>
                        m. Mengembalikan kunci Kamar Kost pada tanggal keluar.<br>
                        n. Mengembalikan Kamar Kost dalam keadaan baik dan seperti semula.<br><br>

                        Penyewa dilarang:<br>
                        a. Menggunakan Kamar Kost sebagai tempat usaha dan/atau kegiatan yang melanggar peraturan
                        perundang-undangan,
                        termasuk perdagangan manusia, narkotika, psikotropika, atau kegiatan terlarang lainnya.<br>
                        b. Menerima tamu melebihi jam 22.00 WIB.<br>
                        c. Menerima tamu lawan jenis di dalam kamar.<br>
                        d. Menerima tamu lawan jenis untuk menginap.<br>
                        e. Membawa hewan peliharaan.<br>
                        f. Membawa dan/atau menyimpan minuman keras yang dapat mengganggu ketertiban penyewa
                        lainnya.<br>
                        g. Menanam, memelihara, membawa, memiliki, menyimpan, menguasai, menyediakan, mengedarkan,
                        menjual,
                        menyalurkan dan/atau menggunakan narkotika.<br>
                        h. Memproduksi, membawa, memiliki, menyimpan, menguasai, menyediakan, mengedarkan, menjual,
                        menyalurkan dan/atau menggunakan psikotropika.<br>
                        i. Meninggalkan barang berharga milik Penyewa dan/atau tamu tanpa pengawasan.<br>
                        j. Menumpuk sampah di Kamar Kost.<br>
                        k. Mencoret dan mengotori Kamar Kost dan fasilitas kost.<br>
                        l. Merubah atau merenovasi Kamar Kost.<br>
                        m. Memasang ornamen dan/atau benda yang menggunakan paku pada dinding kamar.<br>
                        n. Merokok di dalam Kamar Kost maupun ruang lain pada bangunan kost.<br>
                        o. Membawa durian ke Kamar Kost.<br>
                        p. Memasak apapun dan dengan cara apapun di Kamar Kost.<br>
                        q. Menggunakan dan/atau menyimpan senjata tajam dan/atau senjata api tanpa izin dari Instansi
                        Pemerintah.<br>
                    </span>
                </div>

                <div class="compact-text" style="margin-top: 12px;">
                    <strong>DISCLAIMER / PENAFIAN</strong><br>
                    <span class="id-text">
                        Ulin Mahoni tidak bertanggung jawab dan Penyewa melepaskan haknya untuk mengajukan
                        klaim/tuntutan/gugatan
                        kepada Ulin Mahoni, apabila:<br>
                        a. Terjadi kerusakan dan/atau kehilangan terhadap barang pribadi milik Penyewa dan/atau tamu
                        dari Penyewa.<br>
                        b. Terjadi kerusakan dan/atau kehilangan terhadap barang milik Penyewa dan/atau tamu dari
                        Penyewa.<br>
                    </span>
                </div>
            </td>

            <td class="right-col" rowspan="2">
                <div class="signature-container">
                    <div class="document-section">
                        @if ($documentImage)
                            <img src="{{ $documentImage }}" alt="Guest Document" class="document-image"
                                onerror="this.style.display='none'">
                        @else
                            <div class="document-label">No Document Available</div>
                        @endif
                    </div>
                    <div class="signature-section">
                        <div class="compact-text signature-title">
                            <span class="en-text"><strong>Guest Signature</span> / Tanda Tangan Tamu</strong>
                        </div>
                        <div class="signature-spacing"></div>
                        <div class="right-signature-box">
                            <div class="right-icons">
                                <!-- No Durian -->
                                <span class="icon-ban" title="No Durian">
                                    <svg class="main-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                        <path
                                            d="M405.62 292.4l-22.85-28 12.17-34a7 7 0 00-3.06-8.4l-31.28-18.29-1.6-36.23a7 7 0 00-6.29-6.66l-36.22-3.63-18.49-31.4a7 7 0 00-9.06-2.75l-25.61 12.35V86.84c18.8-7.44 38.6-18.24 56.16-33.92a7 7 0 00-9.32-10.44C260.49 86.82 188.7 89.23 188 89.25a7 7 0 00-.17 14h.18c1.85 0 28.48-.91 61-11.4v43.83L223.08 123a7 7 0 00-9.06 2.75l-18.53 31.4-36.23 3.63a7 7 0 00-6.29 6.66l-1.57 36.23L120.12 222a7 7 0 00-3.06 8.4l12.17 34-22.85 28a7 7 0 000 8.85l22.85 28-12.17 34a7 7 0 003.06 8.41l31.28 18.26 1.6 36.24a7 7 0 006.29 6.66l36.22 3.64 18.49 31.4a7 7 0 009.06 2.74L256 454.73l32.92 15.87a7 7 0 009.06-2.74l18.53-31.4 36.23-3.64a7 7 0 006.29-6.66l1.57-36.23 31.28-18.26a7 7 0 003.06-8.4l-12.17-34 22.85-28a7 7 0 000-8.87z"
                                            fill="#000" />
                                        <path
                                            d="M321.19 274.21c-4.86 2.81-11.32 1.19-14.13-3.67-2.81-4.86 1.19-11.32 3.67 14.13 4.86 2.81 11.32 1.19 14.13-3.67 2.81-4.86 1.19-11.32-3.67-14.13z"
                                            fill="#000" />
                                        <path
                                            d="M190.81 274.21c-4.86-2.81-6.48-9.27-3.67-14.13 2.81-4.86 9.27-6.48 14.13-3.67 4.86 2.81 6.48 9.27 3.67 14.13-2.81 4.86-9.27 6.48-14.13 3.67z"
                                            fill="#000" />
                                        <path
                                            d="M256 298.67c-23.5 0-42.67-19.17-42.67-42.67s19.17-42.67 42.67-42.67 42.67 19.17 42.67 42.67-19.17 42.67-42.67 42.67z"
                                            fill="#000" />
                                    </svg>
                                    <svg class="ban-custom" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
                                        <circle cx="50" cy="50" r="46" stroke="red" stroke-width="8"
                                            fill="none" />
                                        <line x1="25" y1="25" x2="75" y2="75" stroke="red"
                                            stroke-width="10" stroke-linecap="round" />
                                    </svg>
                                </span>

                                <!-- No Pets -->
                                <span class="icon-ban" title="No Pets">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 64 64">
                                        <g stroke-width="0">
                                            <path
                                                d="M42.6 8.1c-.3.8-.7 2.7-.9 4.4-.3 1.6-.8 3.9-1.2 5-.9 2.9 6.7 5.7 12.1 4.5 5.9-1.4 8.7-6.9 3.9-7.9-1.1-.2-3.1-1.2-4.4-2.2-1.3-1.1-3.1-1.9-3.9-1.9-.9 0-2.4-.7-3.4-1.6C43.4 7 43 7 42.6 8.1M8 20.1c0 1.7 1 4.9 2.2 7l2.1 3.8-1.8 10.7c-1.5 9-1.6 10.7-.4 11.5 2.5 1.5 4.9 1 4.9-1.1 0-1.1 1.2-4.5 2.7-7.6l2.8-5.6 4.2.6c2.4.3 6.3.9 8.8 1.2l4.5.6v5.2c0 5.5 1.2 7.6 4.2 7.6 1.4 0 1.8-.8 1.8-3.8 0-2 .7-7.7 1.6-12.6.8-4.8 1.4-8.9 1.2-9.1-.2-.1-2.6-1.4-5.5-2.9L36.1 23l-10.2 2.2-10.2 2.2-2.8-3.2C11.3 22.4 10 20.1 10 19s-.4-2-1-2c-.5 0-1 1.4-1 3.1" />
                                            <path
                                                d="M19.8 47.4C17.2 52.7 17.5 54 21 54c3.1 0 3.7-.8 2.1-2.5-.5-.6-1.1-2.5-1.3-4.4l-.3-3.4z" />
                                        </g>
                                        <path fill-opacity=".2"
                                            d="M8 15.4c0 .2.7.7 1.6 1 .8.3 1.2.2.9-.4-.6-1-2.5-1.4-2.5-.6" />
                                    </svg>
                                    <svg class="ban-custom" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
                                        <circle cx="50" cy="50" r="46" stroke="red" stroke-width="8"
                                            fill="none" />
                                        <line x1="25" y1="25" x2="75" y2="75" stroke="red"
                                            stroke-width="10" stroke-linecap="round" />
                                    </svg>
                                </span>

                                <!-- No Smoking -->
                                <span class="icon-ban" title="No Smoking">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 64 64">
                                        <path
                                            d="M27.3 18c-1.7.7-2.3 1.8-2.3 4 0 3.5 2.6 5 9 5 2.7 0 4.2.6 4.9 1.7.7 1.3 2.7 1.9 7.3 2.3 5.6.4 6.4.8 7.6 3.2 1.7 3.7 5.2 3.8 5.2.2 0-3.9-4.8-8.1-10.3-8.9-4-.6-4.7-1.1-5.2-3.7-.4-1.8-1.6-3.3-3.2-3.9-3-1.1-10-1.1-13 .1M4.4 40.4c-.3.8-.4 2.5-.2 3.8.3 2.1.8 2.3 8.1 2.6l7.7.3V39h-7.5c-5.6 0-7.7.4-8.1 1.4M22 43v4h32v-8H22zm34 0c0 3.4.3 4.1 1.8 3.8 1.2-.2 1.7-1.3 1.7-3.8s-.5-3.6-1.7-3.8c-1.5-.3-1.8.4-1.8 3.8" />
                                    </svg>
                                    <svg class="ban-custom" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
                                        <circle cx="50" cy="50" r="46" stroke="red" stroke-width="8"
                                            fill="none" />
                                        <line x1="25" y1="25" x2="75" y2="75"
                                            stroke="red" stroke-width="10" stroke-linecap="round" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Two Column Layout for Penalty and Vehicle Information -->
    <div class="two-column-container">
        <!-- Penalty Information Column -->
        <div class="penalty-column">
            <table class="penalty-table">
                <tr>
                    <td class="penalty-title">
                        <div class="penalty-content">
                            <span class="en-text"><strong>PENALTY INFORMATION / INFORMASI DENDA</strong></span><br><br>

                            <div style="font-weight: normal; line-height:1.3;">

                                Ulin Mahoni tidak bertanggung jawab dan Penyewa melepaskan haknya untuk mengajukan
                                klaim/tuntutan/gugatan kepada Ulin Mahoni, apabila:
                                <br>

                                <ol style="margin-top:3px;">
                                    <li>Terjadi kerusakan dan/atau kehilangan terhadap barang pribadi milik Penyewa dan/atau
                                        tamu dari Penyewa.</li>
                                    <li>Terjadi kerusakan dan/atau kehilangan terhadap kendaraan milik Penyewa dan/atau tamu
                                        dari Penyewa.</li>
                                </ol>

                                <strong>13. Penyewa wajib membayarkan denda kepada Ulin Mahoni, apabila:</strong>

                                <ol type="a" style="margin-top:3px;">
                                    <li>
                                        Membawa Durian ke Kamar Kost. Penyewa wajib membayarkan denda sebesar
                                        <strong>Rp 2,000,000,- (Dua Juta Rupiah).</strong>
                                    </li>
                                    <li>
                                        Merusak Kamar Kost dan/atau fasilitas atau ruangan lain pada bangunan kost.
                                        Penyewa wajib membayarkan denda sebesar kerugian yang diderita oleh Ulin Mahoni.
                                    </li>
                                    <li>
                                        Kebakaran yang terbukti diakibatkan oleh Penyewa sebesar kerugian yang diderita oleh
                                        Ulin Mahoni.
                                        Penyewa wajib membayarkan denda sebesar kerugian yang diderita oleh Ulin Mahoni.
                                    </li>
                                </ol>

                                <div style="margin-top:4px;">
                                    Penyewa wajib membayarkan denda sebagaimana huruf a sampai dengan c di atas.<br>
                                    Apabila Penyewa melanggar baik sebagian maupun seluruh Perjanjian ini dan S&amp;K,
                                    maka Deposit yang telah dibayarkan oleh Penyewa tidak akan dikembalikan.
                                    Dalam hal ini Penyewa melepaskan haknya untuk mengajukan klaim/tuntutan/gugatan kepada Ulin
                                    Mahoni.
                                </div>

                                <div style="margin-top:6px;">
                                    Penalty for lost key card Rp. 50.000<br>
                                    Denda untuk kartu kunci yang hilang Rp. 50.000
                                </div>

                                <div style="display:flex; justify-content:flex-end; margin-top:12px; text-align:right;">
                                    <div>
                                        <strong>Date / Tanggal:</strong><br>
                                        {{ $currentDate }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Vehicle Information Column -->
        <div class="vehicle-column">
            <div class="vehicle-section">
                <!-- Informasi Kendaraan -->
                <div class="section-title">Informasi Kendaraan</div>

                <table class="vehicle-table">
                    <tr>
                        <td width="40%"><strong>Jenis Kendaraan</strong><br></td>
                        <td width="30%"><strong>Merk / Tipe</strong><br></td>
                        <td width="30%"><strong>Warna Kendaraan</strong><br></td>
                    </tr>
                    <tr>
                        <td><strong>STNK atas nama</strong><br></td>
                        <td colspan="2"><strong>Plat Nomor</strong><br></td>
                    </tr>
                </table>

                <div class="section-title">Lampiran Wajib</div>

                <table class="no-border">
                    <tr class="no-border">
                        <td class="no-border"><span class="checkbox-box"></span> Fotokopi / Foto STNK</td>
                    </tr>
                    <tr class="no-border">
                        <td class="no-border"><span class="checkbox-box"></span> Foto Kendaraan</td>
                    </tr>
                    <tr class="no-border">
                        <td class="no-border"><span class="checkbox-box"></span> Fotokopi KTP Penyewa</td>
                    </tr>
                </table>

                <div class="section-title">Pernyataan</div>

                <div class="compact-list">
                    Saya yang bertanda tangan di bawah ini menyatakan bahwa data yang saya isi adalah benar.
                    Saya juga bersedia mematuhi aturan parkir dan ketertiban kendaraan yang berlaku di lingkungan kost.
                </div>

                <div class="compact-list">
                    Form ini berlaku <strong>hanya untuk satu kendaraan.</strong> Jika penghuni memiliki lebih dari satu kendaraan
                    maka akan dikenakan charge sebesar:
                    <br>1. Mobil: Rp. 250.000/bulan
                    <br>2. Motor: Rp. 100.000/bulan
                </div>

                <table class="vehicle-table">
                    <tr>
                        <td width="50%">
                            Tanggal pengisian: _____ / ______ / _____ <br><br>
                            Tanda Tangan Penyewa:
                            <div class="signature-box"></div>
                        </td>

                        <td width="50%">
                            Untuk Pengelola Kost: <br><br>
                            Disetujui oleh: __________________ <br><br>
                            Tanggal Verifikasi: _____ / ______ / _____
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div style="margin-top: 6px; font-size: 7px; text-align: center; color: #555; line-height: 1.1;">
        <p>Dokumen ini dibuat secara elektronik oleh Ulin Mahoni dan sah tanpa tanda tangan basah.</p>
        <p>© {{ date('Y') }} Ulin Mahoni — Semua Hak Dilindungi Undang-Undang.</p>
    </div>
</body>

</html>