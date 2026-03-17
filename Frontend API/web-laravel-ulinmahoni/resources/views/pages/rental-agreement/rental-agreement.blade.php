<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perjanjian Sewa Menyewa - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @include('components.property.styles')
    @include('components.homepage.styles')
    <script>if (localStorage.getItem('dark-mode') === 'true') document.documentElement.classList.add('dark');</script>
    <style>
        .policy-section {
            margin-bottom: 2.5rem;
        }
        .policy-section h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 1rem;
        }
        .policy-section h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin: 1.5rem 0 0.75rem 0;
        }
        .policy-content {
            color: #4b5563;
            line-height: 1.75;
            margin-bottom: 1rem;
        }
        .policy-list {
            list-style-type: decimal;
            padding-left: 1.5rem;
            margin: 1rem 0;
        }
        .policy-list li {
            margin-bottom: 0.5rem;
        }
        .policy-sub-list {
            list-style-type: lower-latin;
            padding-left: 1.5rem;
            margin: 0.5rem 0;
        }
        .agreement-party {
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: #f9fafb;
            border-left: 4px solid #0d9488;
        }
    </style>
</head>
<body class="font-inter antialiased bg-white text-gray-900 tracking-tight">
    <!-- Header -->
    @include('components.homepage.header')
    <div class="header-spacer"></div>

    <main class="py-12">
        <div class="container mx-auto px-4 py-8">
            <!-- Breadcrumb -->
            <nav class="mb-8">
                <ol class="flex items-center space-x-2 text-gray-500">
                    <li><a href="{{ route('homepage') }}" class="hover:text-gray-700">Beranda</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li class="text-gray-900">Perjanjian Sewa Menyewa</li>
                </ol>
            </nav>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 md:p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2 text-center">PERJANJIAN SEWA MENYEWA KAMAR KOST</h1>
                <p class="text-center text-gray-600 mb-8">Nomor: …</p>

                <div class="prose max-w-none">
                    <div class="policy-section">
                        <p class="policy-content">Pada hari ini …, tanggal … … …, bertempat di …, telah dibuat dan ditandatangani Perjanjian Sewa Menyewa Kamar Kost ("Perjanjian"), oleh dan antara:</p>
                    </div>

                    <!-- Para Pihak -->
                    <div class="policy-section">
                        <div class="agreement-party">
                            <p class="policy-content"><strong>1. PT KELOLA ASET PROPERTI</strong>, suatu Badan Hukum berbentuk Perseroan Terbatas yang secara sah didirikan berdasarkan Hukum Negara Republik Indonesia, berkedudukan di Jakarta Barat, dan beralamat di APL Tower - Central Park Lantai 39, Jl. Letjen S. Parman Kaveling 28, RT 003/RW 005, Kelurahan Tanjung Duren Selatan, Kecamatan Grogol Petamburan, Kota Administrasi Jakarta Barat, Provinsi DKI Jakarta 11470, dalam hal ini diwakili oleh <strong>RUDIYANSYAH</strong>, yang bertindak dalam jabatannya selaku Direktur, dari dan oleh karenanya berhak dan berwenang bertindak untuk dan atas nama PT Kelola Aset Properti, untuk selanjutnya disebut sebagai <strong>"Ulin Mahoni"</strong>.</p>
                        </div>

                        <div class="agreement-party">
                            <p class="policy-content"><strong>2. …</strong>, Warga Negara Indonesia, lahir di …, pada tanggal … … …, bertempat tinggal di …, RT …/RW …, Kelurahan …, Kecamatan …, Kota Jakarta …, Provinsi …, pemegang Kartu Tanda Penduduk dengan Nomor Induk Kependudukan (NIK) …, dalam hal ini bertindak untuk dirinya sendiri, untuk selanjutnya disebut sebagai <strong>"Penyewa"</strong>.</p>
                        </div>

                        <p class="policy-content">Ulin Mahoni dan Penyewa secara bersama-sama disebut sebagai <strong>"Para Pihak"</strong>, dan secara masing-masing disebut sebagai <strong>"Pihak"</strong> atau sesuai referensi penyebutannya.</p>
                    </div>

                    <!-- Ketentuan Perjanjian -->
                    <div class="policy-section">
                        <p class="policy-content">Para Pihak dengan ini sepakat untuk mengadakan Perjanjian dengan syarat dan ketentuan sebagai berikut:</p>

                        <ol class="policy-list">
                            <li class="policy-content">Ulin Mahoni sepakat untuk menyewakan kamar kost Nomor …, yang berlokasi di … kepada Penyewa ("Kamar Kost").</li>

                            <li class="policy-content">Jangka Waktu Sewa adalah selama … (…) bulan yang terhitung sejak tanggal … ("Tanggal Masuk") sampai dengan tanggal … ("Tanggal Keluar").</li>

                            <li class="policy-content">Biaya Sewa yang wajib dibayarkan oleh Penyewa kepada Ulin Mahoni adalah sebesar Rp. ……,- (… Rupiah) per bulan. Penyewa wajib membayarkan Biaya Sewa kepada Ulin Mahoni dengan metode pembayaran yang telah dipilih, yaitu dapat melalui transfer, kartu kredit, virtual account, payment gateaway, paylater dan/atau sarana pembayaran lainnya yang disediakan baik pada pada Ulin Mahoni maupun mesin Electronic Data Capture ("EDC") dilokasi kost.</li>

                            <li class="policy-content">Penyewa wajib membayarkan Deposit kepada Ulin Mahoni sebesar Rp. …,- (… Rupiah) dengan metode pembayaran melalui transfer ke rekening Ulin Mahoni, yaitu:
                                <div class="mt-3 mb-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <table class="w-full text-sm">
                                        <tr>
                                            <td class="py-1 font-semibold text-gray-700" style="width: 180px;">Bank</td>
                                            <td class="py-1">: BRI</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1 font-semibold text-gray-700">Cabang</td>
                                            <td class="py-1">: Tanjung Duren</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1 font-semibold text-gray-700">Nomor Rekening</td>
                                            <td class="py-1">: 0505.01.001671.56.7</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1 font-semibold text-gray-700">Atas Nama</td>
                                            <td class="py-1">: PT. Kelola Aset Properti</td>
                                        </tr>
                                    </table>
                                </div>
                            </li>

                            <li class="policy-content">Penyewa dapat memasuki dan menempati Properti paling cepat pada jam 14.00 WIB.</li>

                            <li class="policy-content">Penyewa wajib meninggalkan Properti dan mengembalikan kunci Properti kepada tim Ulin Mahoni yang berada di lokasi Properti paling lambat pada jam 12.00 WIB pada Tanggal Keluar.</li>

                            <li class="policy-content">Apabila Penyewa ingin memperpanjang Jangka Waktu Sewa sebagaimana tertera dalam Perjanjian ini, maka dalam jangka waktu 14 (empat belas) hari sebelum Tanggal Keluar, Penyewa wajib melakukan pemesanan kembali Properti melalui Ulin Mahoni atau secara langsung kepada Ulin Mahoni. Dalam hal terdapat perbedaan harga sewa Properti pada Perjanjian yang lama dengan Perjanjian yang baru, Penyewa wajib untuk membayar sesuai dengan Biaya Sewa yang baru. Penyewa wajib membayarkan Biaya Sewa kepada Ulin Mahoni dengan metode pembayaran yang telah dipilih, yaitu dapat melalui transfer, kartu kredit, virtual account, payment gateaway, paylater dan/atau sarana pembayaran lainnya yang disediakan baik pada Aplikasi/Website Ulin Mahoni maupun mesin EDC dilokasi kost.</li>

                            <li class="policy-content">Apabila dalam jangka waktu 14 (empat belas) hari sebelum Tanggal Keluar, Penyewa tidak melakukan pemesanan kembali Properti pada Ulin Mahoni dan Properti yang sedang ditempati Penyewa sudah diambil oleh Penyewa lain, maka Penyewa setuju untuk Pindah Kamar (Relocation) dan Penyewa melepaskan haknya untuk mengajukan klaim dan/atau tuntutan dan/atau gugatan dalam bentuk apapun terhadap Ulin Mahoni.</li>

                            <li class="policy-content">Apabila Perjanjian ini berakhir dan tidak diperpanjang, maka Ulin Mahoni akan mengembalikan Deposit sebagaimana poin 4 kepada Penyewa paling lambat 3 (tiga) hari kerja setelah Tanggal Keluar. Deposit akan dikembalikan setelah pengecekan kamar oleh management dan angka pengembalian akan disesuaikan dengan ceklist kerusakan di kamar (apabila ada kerusakan). Deposit akan dikembalikan melalui transfer ke rekening Penyewa, yaitu:
                                <div class="mt-3 mb-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <table class="w-full text-sm">
                                        <tr>
                                            <td class="py-1 font-semibold text-gray-700" style="width: 180px;">Bank</td>
                                            <td class="py-1">: …</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1 font-semibold text-gray-700">Cabang</td>
                                            <td class="py-1">: …</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1 font-semibold text-gray-700">Nomor Rekening</td>
                                            <td class="py-1">: …</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1 font-semibold text-gray-700">Atas Nama</td>
                                            <td class="py-1">: …</td>
                                        </tr>
                                    </table>
                                </div>
                            </li>

                            <li class="policy-content">Penyewa wajib:
                                <ol class="policy-sub-list">
                                    <li class="policy-content">Menggunakan Kamar Kost hanya untuk tempat tinggal dengan sebaik-baiknya.</li>
                                    <li class="policy-content">Menjaga, merawat dan memelihara Kamar Kost dengan sebaik-baiknya.</li>
                                    <li class="policy-content">Mematikan lampu, AC, dan mencabut barang elektronik lainnya, apabila Penyewa meninggalkan Kamar Kost.</li>
                                    <li class="policy-content">Menjaga barang berharga dan barang pribadi milik Penyewa dan/atau tamu.</li>
                                    <li class="policy-content">Menutup pintu Kamar Kost dalam segala kondisi.</li>
                                    <li class="policy-content">Menutup serta mengunci pintu dan gerbang kost, pada saat Penyewa masuk dan keluar kost.</li>
                                    <li class="policy-content">Menjaga, merawat dan memelihara fasilitas bersama yang ada di kost tanpa terkecuali.</li>
                                    <li class="policy-content">Memberitahu Ulin Mahoni apabila ada tamu yang menginap.</li>
                                    <li class="policy-content">Membayar biaya tambahan untuk tamu (khusus keluarga dan/atau teman bukan lawan jenis) yang menginap sebesar Rp. 100.000,- (seratus ribu Rupiah) per hari dengan maksimal tamu menginap selama 3 (tiga) hari.</li>
                                    <li class="policy-content">Membayar biaya tambahan sebesar setengah Biaya Sewa apabila tamu (khusus keluarga dan/atau teman bukan lawan jenis) menginap lebih dari 3 (tiga) hari.</li>
                                    <li class="policy-content">Memberitahukan Ulin Mahoni apabila Penyewa membawa alat elektronik sendiri.</li>
                                    <li class="policy-content">Mengunci dan/atau menggunakan kunci ganda pada kendaraan bermotor baik milik Penyewa maupun milik tamu Penyewa.</li>
                                    <li class="policy-content">Mengembalikan kunci Kamar Kost pada Tanggal Keluar.</li>
                                    <li class="policy-content">Mengembalikan Kamar Kost dalam keadaan baik dan seperti semula.</li>
                                </ol>
                            </li>

                            <li class="policy-content">Penyewa dilarang:
                                <ol class="policy-sub-list">
                                    <li class="policy-content">Menggunakan Kamar Kost sebagai tempat usaha dan/atau kegiatan yang melanggar peraturan perundang-undangan yang berlaku, termasuk namun tidak terbatas pada kegiatan prostitusi, perdagangan manusia, anak, organ, narkotika dan/atau psikotropika.</li>
                                    <li class="policy-content">Menerima tamu melebihi jam 22.00 WIB.</li>
                                    <li class="policy-content">Menerima tamu lawan jenis di dalam kamar.</li>
                                    <li class="policy-content">Menerima tamu lawan jenis untuk menginap.</li>
                                    <li class="policy-content">Membawa hewan peliharaan.</li>
                                    <li class="policy-content">Membawa dan/atau menyimpan minuman keras yang dapat mengganggu ketertiban penyewa lainnya dan/atau lingkungan sekitar.</li>
                                    <li class="policy-content">Menanam, memproduksi, memelihara, membawa, memiliki, menyimpan, menguasai, menyediakan, mengedarkan, menjual, menyalurkan dan/atau menggunakan Narkotika.</li>
                                    <li class="policy-content">Memproduksi, membawa, memiliki, menyimpan, menguasai, menyediakan, mengedarkan, menjual, menyalurkan dan/atau menggunakan Psikotropika.</li>
                                    <li class="policy-content">Meninggalkan barang berharga milik Penyewa dan/atau tamu tanpa pengawasan Penyewa dan/atau tamu.</li>
                                    <li class="policy-content">Menumpuk sampah di Kamar Kost.</li>
                                    <li class="policy-content">Mencoret dan mengotori Kamar Kost dan fasilitas kost.</li>
                                    <li class="policy-content">Merubah atau merenovasi Kamar Kost.</li>
                                    <li class="policy-content">Memasang ornamen dan/atau benda yang menggunakan paku pada Kamar Kost.</li>
                                    <li class="policy-content">Merokok di dalam Kamar Kost maupun ruang lain pada bangunan kost yang merupakan bagian dilarang merokok.</li>
                                    <li class="policy-content">Membawa Durian ke Kamar Kost.</li>
                                    <li class="policy-content">Memasak apapun dan dengan cara apapun di Kamar Kost.</li>
                                    <li class="policy-content">Membawa dan/atau menyimpan senjata tajam dan/atau senjata api tanpa izin yang sah dari Instansi Pemerintah.</li>
                                </ol>
                            </li>

                            <li class="policy-content">Ulin Mahoni tidak bertanggung jawab dan Penyewa melepaskan haknya untuk mengajukan klaim/tuntutan/gugatan kepada Ulin Mahoni, apabila:
                                <ol class="policy-sub-list">
                                    <li class="policy-content">Terjadi kerusakan dan/atau kehilangan terhadap barang pribadi milik Penyewa dan/atau tamu dari Penyewa.</li>
                                    <li class="policy-content">Terjadi kerusakan dan/atau kehilangan terhadap kendaraan milik Penyewa dan/atau tamu dari Penyewa.</li>
                                </ol>
                            </li>

                            <li class="policy-content">Penyewa wajib membayarkan denda kepada Ulin Mahoni, apabila:
                                <ol class="policy-sub-list">
                                    <li class="policy-content">Membawa Durian ke Kamar Kost. Penyewa wajib membayarkan denda sebesar Rp.2.000.000,- (Dua Juta Rupiah).</li>
                                    <li class="policy-content">Merusak Kamar Kost dan/atau fasilitas atau ruangan lain pada bangunan kost. Penyewa wajib membayarkan denda sebesar kerugian yang diderita oleh Ulin Mahoni.</li>
                                    <li class="policy-content">Kebakaran yang terbukti diakibatkan oleh Penyewa sebesar kerugian yang diderita oleh Ulin Mahoni. Penyewa wajib membayarkan denda sebesar kerugian yang diderita oleh Ulin Mahoni.</li>
                                </ol>
                                <p class="policy-content mt-2">Penyewa wajib membayarkan denda sebagaimana huruf a sampai dengan c di atas kepada Ulin Mahoni melalui transfer ke rekening Ulin Mahoni atau melalui mesin EDC di lokasi kost paling lambat 3 (tiga) hari sejak terjadinya pelanggaran.</p>
                            </li>

                            <li class="policy-content">Apabila Penyewa melanggar baik sebagian maupun seluruh Perjanjian ini dan S&K, maka Deposit yang telah dibayarkan oleh Penyewa tidak akan dikembalikan. Dalam hal ini Penyewa melepaskan haknya untuk mengajukan klaim/tuntutan/gugatan kepada Ulin Mahoni.</li>

                            <li class="policy-content">Hal-hal yang belum diatur dalam Perjanjian ini namun sudah diatur di dalam Syarat dan Ketentuan Penggunaan Ulin Mahoni ("S&K"), maka S&K berlaku, sah dan mengikat Para Pihak. Selanjutnya apabila terdapat hal-hal yang belum diatur dalam Perjanjian ini dan dalam S&K, maka akan diatur kemudian oleh Para Pihak dalam suatu addendum atau amandemen Perjanjian yang merupakan satu kesatuan dan bagian yang tidak terpisahkan dari Perjanjian ini serta berlaku mengikat setelah ditandatangani oleh Para Pihak.</li>

                            <li class="policy-content">Apabila terdapat ketidakkonsistenan, perbedaan dan/atau pertentangan ketentuan yang termuat di dalam Perjanjian dengan ketentuan yang termuat di dalam S&K, maka Ulin Mahoni berhak dan berwenang untuk menentukan ketentuan mana yang berlaku dan Penyewa setuju untuk memberikan hak dan kewenangan tersebut kepada Ulin Mahoni.</li>

                            <li class="policy-content">Keabsahan, penafsiran, dan pelaksanaan S&K ini serta segala akibat yang ditimbulkannya, diatur, dan tunduk pada peraturan perundang-undangan yang berlaku di wilayah Negara Kesatuan Republik Indonesia.</li>

                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa sepakat dan setuju segala tindakan hukum atau sengketa yang timbul sehubungan dengan penggunaan Ulin Mahoni, maka akan diselesaikan pada Pengadilan Negeri Jakarta Barat.</li>
                        </ol>
                    </div>

                    <!-- Closing Statement -->
                    <div class="policy-section mt-8">
                        <p class="policy-content text-center">
                            Demikianlah Perjanjian ini ditandatangani oleh Para Pihak dalam 2 (dua) rangkap asli, masing-masing bermeterai cukup dan mempunyai kekuatan hukum yang sama, dimana setiap Pihak mendapat 1 (satu) rangkap Ulin Mahoni asli Perjanjian ini.
                        </p>
                    </div>

                    <!-- Placeholder for signatures -->
                    <div class="policy-section mt-12">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                            <div class="text-center">
                                <p class="font-semibold mb-16">Ulin Mahoni</p>
                                <div class="border-t-2 border-gray-300 pt-2 mt-16">
                                    <p class="font-semibold">RUDIYANSYAH</p>
                                    <p class="text-sm text-gray-600">Direktur</p>
                                </div>
                            </div>
                            <div class="text-center">
                                <p class="font-semibold mb-16">Penyewa</p>
                                <div class="border-t-2 border-gray-300 pt-2 mt-16">
                                    <p class="font-semibold">( … )</p>
                                    <p class="text-sm text-gray-600">Nama Penyewa</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    @include('components.homepage.footer')

    @stack('scripts')
</body>
</html>
