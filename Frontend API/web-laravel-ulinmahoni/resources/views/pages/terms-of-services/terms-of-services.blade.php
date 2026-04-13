<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syarat dan Ketentuan - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    @include('components.property.styles')
    @include('components.homepage.styles')
    <script>if (localStorage.getItem('dark-mode') !== 'false') document.documentElement.classList.add('dark');</script>
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

        /* Dark mode overrides for terms page */
        html.dark body { background-color: #111827 !important; color: #e5e7eb !important; }
        html.dark .policy-section h2 { color: #f3f4f6 !important; }
        html.dark .policy-section h3 { color: #e5e7eb !important; }
        html.dark .policy-content { color: #d1d5db !important; }
        html.dark .policy-list li { color: #d1d5db !important; }
        html.dark .policy-sub-list li { color: #d1d5db !important; }
        html.dark .legal-card { background-color: #1f2937 !important; border-color: #374151 !important; }
        html.dark .legal-card h1 { color: #f3f4f6 !important; }
        html.dark .breadcrumb-text { color: #9ca3af !important; }
        html.dark .breadcrumb-active { color: #e5e7eb !important; }
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
                    <li><a href="{{ route('homepage') }}" class="breadcrumb-text hover:text-gray-700">Beranda</a></li>
                    <li><span class="mx-2 breadcrumb-text">/</span></li>
                    <li class="breadcrumb-active">Syarat dan Ketentuan</li>
                </ol>
            </nav>

            <!-- Main content card with dark mode support -->
            <div class="legal-card bg-white rounded-lg shadow-sm border border-gray-100 p-6 md:p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-8">SYARAT DAN KETENTUAN</h1>
                
                <div class="prose max-w-none">
                    <div class="policy-section">
                        <p class="policy-content">Selamat datang di Ulin Mahoni dan terima kasih telah mengunjungi Ulin Mahoni, baik melalui situs web maupun aplikasi. Anda diharuskan untuk membaca, memahami, dan mencermati Syarat dan Ketentuan Penggunaan ini (selanjutnya disebut "S&K"). S&K ini merupakan bagian yang tidak terpisahkan dari Kebijakan Privasi dan memiliki kekuatan hukum yang mengikat.</p>
                        <p class="policy-content">Dengan mengunjungi, menggunakan, mengakses dan/atau mendaftar di Ulin Mahoni, maka Anda dianggap telah membaca, memahami, mencermati dan menyetujui keseluruhan isi S&K ini tanpa terkecuali dengan ketentuan sebagai berikut:</p>
                    </div>

                    <!-- Add your Terms of Service content here -->
                    <div class="policy-section">
                        <h2>A. Definisi</h2>
                        <p class="policy-content">Istilah-istilah di bawah ini memiliki pengertian sebagai berikut:</p>
                        <ol class="policy-list">
                            <li class="policy-content"><strong>Pengunjung</strong> adalah perorangan dan/atau badan usaha, baik yang berbadan hukum maupun yang tidak berbadan hukum, yang mengunjungi atau mengakses Ulin Mahoni.</li>
                            <li class="policy-content"><strong>Pemilik Akun</strong> adalah perorangan dan/atau badan usaha, baik yang berbadan hukum maupun yang tidak berbadan hukum, yang mengakses, menggunakan dan terdaftar sebagai pemilik akun Ulin Mahoni.</li>
                            <li class="policy-content"><strong>Ulin Mahoni</strong> adalah situs web www.ulinmahoni.com dan/atau aplikasi Ulin Mahoni. Situs web dan/atau aplikasi tersebut dimiliki, dikelola, dan dioperasikan oleh KAP.</li>
                            <li class="policy-content"><strong>KAP</strong> adalah PT Kelola Aset Properti yang merupakan badan hukum berbentuk Perseroan Terbatas, yang didirikan berdasarkan ketentuan perundang-undangan yang berlaku di Negara Kesatuan Republik Indonesia, sebagai pemilik, pengelola dan pengoperasi Ulin Mahoni.</li>
                            <li class="policy-content"><strong>Properti</strong> adalah bangunan milik Pemilik yang disewakan melalui Ulin Mahoni termasuk namun tidak terbatas pada rumah kos, kamar kos, rumah kontrakan, unit apartemen dan/atau rumah.</li>
                            <li class="policy-content"><strong>Penyewa</strong> adalah Pemilik Akun yang menyewa dan/atau menempati 1 (satu) atau lebih dari 1 (satu) Properti melalui Ulin Mahoni.</li>
                            <li class="policy-content"><strong>Pemilik</strong> adalah setiap orang dan/atau badan usaha, baik yang berbadan hukum maupun yang tidak berbadan hukum, yang secara hukum memiliki properti berdasarkan peraturan perundang-undangan Negara Kesatuan Republik Indonesia.</li>
                            <li class="policy-content"><strong>Tanggal Masuk</strong> adalah tanggal dimulainya Penyewa untuk pindah (check in) dan menempati Properti.</li>
                            <li class="policy-content"><strong>Tanggal Keluar</strong> adalah tanggal Penyewa melakukan check out dari Properti.</li>
                            <li class="policy-content"><strong>Jangka Waktu</strong> adalah jangka waktu sewa Properti.</li>
                            <li class="policy-content"><strong>Biaya Layanan</strong> adalah biaya yang dibebankan kepada Pemilik Akun dan dimaksudkan untuk memungkinkan Ulin Mahoni meningkatkan layanan dengan memelihara dan mengembangkan sistem Ulin Mahoni.</li>
                            <li class="policy-content"><strong>Perjanjian</strong> adalah perjanjian penggunaan Ulin Mahoni dan/atau sewa-menyewa Properti yang dilakukan, disepakati dan disetujui antara Penyewa dengan KAP.</li>
                        </ol>
                    </div>

                    <!-- Continue with other sections as needed -->

                    <div class="policy-section">
                        <h2>B. Pembuatan dan Penggunaan Akun</h2>
                        <p class="policy-content">Untuk dapat menggunakan Ulin Mahoni, maka Pengunjung wajib untuk mematuhi ketentuan sebagai berikut:</p>
                        <ol class="policy-list">
                            <li class="policy-content">Sebelum menggunakan Ulin Mahoni, maka Pengunjung terlebih dahulu diwajibkan untuk melakukan pendaftaran atau pembuatan akun pada Ulin Mahoni, dan mematuhi seluruh S&K pada Ulin Mahoni.</li>
                            <li class="policy-content">Pengunjung tidak dipungut biaya apapun untuk mendaftarkan diri pada Ulin Mahoni.</li>
                            <li class="policy-content">Pihak yang dapat melakukan pendaftaran atau pembuatan wajib minimal berusia 18 (delapan belas) tahun atau sudah pernah menikah atau yang telah memenuhi persyaratan yang diakui secara hukum termasuk dalam kategori orang yang sudah dewasa dan cakap atau terlepas dari hukum perlindungan anak.</li>
                            <li class="policy-content">Guna kepentingan pendaftaran dan penggunaan akun Ulin Mahoni, maka Pengunjung wajib menyerahkan informasi, data dan/atau dokumen termasuk namun tidak terbatas pada identitas diri, kepada KAP melalui Ulin Mahoni, kemudian KAP akan melakukan pengumpulan, pengolahan, dan penyimpanan informasi, data dan/atau dokumen tersebut.</li>
                            <li class="policy-content">Pihak yang melakukan pendaftaran atau pembuatan akun Ulin Mahoni wajib menyediakan informasi, data dan/atau dokumen yang akurat, lengkap, terkini dan valid.</li>
                            <li class="policy-content">Ulin Mahoni hanya meminta OTP melalui email resmi ulinmahoni@gmail.com kepada email Pengunjung yang dicantumkan pada Ulin Mahoni untuk proses pendaftaran.</li>
                            <li class="policy-content">Pemilik Akun dilarang untuk mengalihkan dan/atau memindahtangankan dengan cara apapun dan dengan alasan apapun akun Ulin Mahoni miliknya kepada pihak lain.</li>
                            <li class="policy-content">Apabila Pemilik Akun mengizinkan pihak lain untuk menggunakan akun Ulin Mahoni miliknya, maka Pemilik Akun bertanggung jawab sepenuhnya terhadap kesalahan dan/atau ketidakakuratan data atau informasi yang diberikan kepada KAP. Segala transaksi, resiko, tanggung jawab, dan/atau kerugian yang timbul atau terjadi akibat hal tersebut merupakan tanggung jawab dari Pemilik Akun, dan Pemilik Akun melepaskan KAP dari segala tuntutan dalam bentuk apapun.</li>
                            <li class="policy-content">Pemilik Akun wajib untuk menjaga keamanan akun Ulin Mahoni miliknya, termasuk namun tidak terbatas menjaga username, email, password dan/atau seluruh informasi, data dan/atau dokumen yang berkaitan dengan akun Ulin Mahoni miliknya. Apabila Pemilik Akun lalai, maka segala transaksi, resiko, tanggung jawab, dan/atau kerugian yang timbul atau terjadi akibat hal tersebut merupakan tanggung jawab dari Pemilik Akun, dan Pemilik Akun melepaskan KAP dari segala tuntutan dalam bentuk apapun.</li>
                            <li class="policy-content">Pemilik Akun wajib segera memberitahukan kepada KAP secara tertulis ke email resmi KAP (ulinmahoni@gmail.com), apabila akun Ulin Mahoni miliknya beralih dan/atau berpindahtangan kepada pihak lain tanpa sepengetahuan Pemilik Akun dan/atau KAP. Segala transaksi, resiko, tanggung jawab, dan/atau kerugian yang timbul atau terjadi akibat hal tersebut merupakan tanggung jawab dari Pemilik Akun dan Pemilik Akun melepaskan KAP dari segala tuntutan dalam bentuk apapun.</li>
                            <li class="policy-content">Properti yang ditawarkan pada Ulin Mahoni bukan milik KAP, melainkan properti Pemilik.</li>
                            <li class="policy-content">Pengunjung atau Pemilik Akun dilarang untuk memuat dan/atau menerbitkan:
                                <ol class="policy-sub-list">
                                    <li class="policy-content">Kontribusi atau konten yang melanggar hak cipta, paten, merek dagang, merek layanan, rahasia dagang dan/atau Hak Kekayaan Intelektual lainnya;</li>
                                    <li class="policy-content">Konten yang menyesatkan, mengancam, cabul/pornografi, tidak senonoh, pornografi atau bisa menimbulkan segala kewajiban hukum perdata atau pidana Indonesia atau hukum Internasional;</li>
                                    <li class="policy-content">Iklan yang tidak sah, materi promosi, spam, surat massal dan/atau atau bentuk lainnya;</li>
                                    <li class="policy-content">Konten yang menggunakan kata-kata atau kalimat yang kotor, kasar, melecehkan atau memfitnah Pengguna lain di situs;</li>
                                    <li class="policy-content">Konten yang menganjurkan kekerasan penggulingan Pemerintah atau menghasut, mendorong, mengancam, mengejek, mengintimidasi atau menyalahgunakan siapapun;</li>
                                    <li class="policy-content">Konten yang melanggar privasi atau hak publisitas pihak lain mana pun; dan/atau</li>
                                    <li class="policy-content">Mengandung bug, virus, worm, pintu perangkap, trojan horse dan/atau kode berbahaya lainnya.</li>
                                </ol>
                            </li>
                            <li class="policy-content">KAP selalu berupaya untuk menjaga Ulin Mahoni tetap aman, nyaman, dan berfungsi dengan baik, namun KAP tidak dapat menjamin operasional Ulin Mahoni selalu sempurna dan informasi, data dan/atau dokumen yang terdapat di dalam Ulin Mahoni memungkinkan tidak terjadi secara real time.</li>
                            <li class="policy-content">Pengunjung dan/atau Pemilik Akun dilarang untuk mereproduksi, mendistribusikan, memajang, menjual, menyewakan, mengirimkan, membuat karya turunan, menerjemahkan, memodifikasi, merekayasa balik, membongkar, mengurai dan/atau atau mengeksploitasi Ulin Mahoni.</li>
                            <li class="policy-content">KAP berhak untuk mengganti, mengubah, menangguhkan atau menghentikan semua atau bagian apapun dari Ulin Mahoni setiap saat atau setelah memberikan pemberitahuan sebagaimana dipersyaratkan oleh Undang-Undang dan peraturan yang berlaku. KAP juga dapat meluncurkan layanan tertentu atau fitur tertentu dalam versi beta dan/atau dalam versi apapun, yang mungkin tidak berfungsi dengan baik atau sama seperti versi akhir, dan KAP tidak bertanggung jawab atas hal tersebut. Ulin Mahoni juga dapat membatasi fitur tertentu atau membatasi akses Pemilik Akun ke bagian atau seluruh Ulin Mahoni atas kebijakannya sendiri dan tanpa pemberitahuan.</li>
                            <li class="policy-content">KAP berhak untuk melakukan pemberhentian, penangguhan, pembekuan, dan/atau penutupan baik sementara maupun permanen serta penghapusan baik sebagian maupun seluruhnya akun pada Ulin Mahoni tanpa pemberitahuan terlebih dahulu, apabila terdapat dugaan terjadinya maupun telah terbukti adanya pelanggaran, kecurangan (fraud), pencurian, penipuan, transaksi yang dilarang, dan/atau tindakan lain yang melanggar peraturan perundang-undangan yang berlaku serta adanya perintah dari aparat penegak hukum dan/atau instansi Pemerintah yang sah.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>C. Pemesanan (Booking)</h2>
                        <p class="policy-content">Untuk melakukan pemesanan (booking) Properti melalui Ulin Mahoni, maka Pemilik Akun wajib untuk mematuhi ketentuan sebagai berikut:</p>
                        <ol class="policy-list">
                            <li class="policy-content">KAP telah memperoleh hak dan kewenangan dari Pemilik untuk bertindak atas nama Pemilik dalam menyewakan Properti kepada Penyewa melalui Ulin Mahoni.</li>
                            <li class="policy-content">Pemilik Akun dapat memilih Properti yang cocok dan Jangka Waktu yang diinginkan pada Ulin Mahoni serta melakukan pembayaran.</li>
                            <li class="policy-content">Pemilik Akun wajib melengkapi data, dokumen dan/atau informasi serta mengisi formulir yang disediakan pada Ulin Mahoni dan mengunggah dokumen yang dibutuhkan terkait penyewaan Properti pada Ulin Mahoni.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>D. Gambar, Video, dan Deskripsi Konten</h2>
                        <ol class="policy-list">
                            <li class="policy-content">KAP selalu berusaha untuk menampilkan konten, gambar, video, informasi dan deskripsi Properti seakurat mungkin pada Ulin Mahoni. KAP tidak dapat menjamin seluruh gambar, video dan/atau deskripsi Properti yang terdapat atau ditampilkan pada Ulin Mahoni adalah 100% (seratus persen) akurat, lengkap, terbaru dan bebas error. Atas hal tersebut Pengunjung dan/atau Pemilik Akun sepakat dan setuju untuk melepaskan KAP dari tanggung jawab terhadap adanya ketimpangan atau ketidakakuratan pada gambar, video dan/atau deskripsi Properti termasuk hak cipta, merek dagang atau Hak Kekayaan Intelektual lainnya.</li>
                            <li class="policy-content">KAP berhak untuk melakukan perbaikan terhadap gambar, video, informasi dan/atau deskripsi konten pada Ulin Mahoni berdasarkan hasil review dan/atau laporan yang KAP terima atau hasil pemeriksaan dan penilaian dari tim KAP.</li>
                            <li class="policy-content">KAP atas diskresinya sendiri dan penilaiannya sendiri berhak untuk melakukan menghapus gambar, video, informasi dan/atau deskripsi konten yang tidak sejalan, tidak sesuai dan/atau melanggar prinsip KAP dan/atau S&K ini.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>E. Harga dan/atau Biaya</h2>
                        <ol class="policy-list">
                            <li class="policy-content">Harga Properti adalah harga sebagaimana yang tertera pada masing-masing Properti yang ditampilkan melalui Ulin Mahoni.</li>
                            <li class="policy-content">Harga Properti yang tertera pada Ulin Mahoni sewaktu-waktu dapat berubah tanpa adanya pemberitahuan sebelumnya kepada Pemilik Akun.</li>
                            <li class="policy-content">Apabila terdapat kesalahan harga Properti yang disebabkan oleh tidak diperbaruinya (update) dan/atau disegarkan (refresh) Ulin Mahoni dengan alasan atau akibat apapun, maka hal tersebut merupakan tanggung jawab Pengunjung dan/atau Pemilik Akun. Pengunjung dan/atau Pemilik Akun tidak dapat membebankan tanggung jawab tersebut kepada KAP.</li>
                            <li class="policy-content">Setiap kesalahan harga Properti, jumlah pembayaran, metode pembayaran, nomor referensi, nomor rekening dan/atau nomor virtual account yang Pemilik Akun pilih atau masukan pada Ulin Mahoni, sepenuhnya merupakan tanggung jawab Pemilik Akun. Oleh karenanya Pemilik Akun melepaskan haknya untuk mengajukan klaim dan/atau tuntutan dan/atau gugatan dalam bentuk apapun terhadap KAP.</li>
                            <li class="policy-content">Pemilik Akun dapat dikenakan Biaya Layanan dengan jumlah yang akan tertera pada tagihan atau invoice saat Pemilik Akun melakukan transaksi.</li>
                            <li class="policy-content">Untuk saat ini harga sewa Properti yang ditampilkan pada Ulin Mahoni dalam mata uang Rupiah.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>F. Deposit</h2>
                        <ol class="policy-list">
                            <li class="policy-content">Penyewa wajib membayarakan deposit dengan ketentuan sebagai berikut:
                                <ol class="policy-sub-list">
                                    <li class="policy-content">Untuk jangka waktu sewa 1 (satu) sampai dengan 6 (enam) bulan, maka wajib membayarkan deposit sebesar 1 (satu) bulan harga sewa Properti.</li>
                                    <li class="policy-content">Untuk jangka waktu sewa 7 (tujuh) sampai dengan 12 (dua belas) bulan, maka wajib membayarkan deposit sebesar 2 (dua) bulan harga sewa Properti.</li>
                                </ol>
                            </li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>G. Transaksi dan Prosedur Pembayaran</h2>
                        <ol class="policy-list">
                            <li class="policy-content">Pemilik Akun wajib melakukan transaksi sesuai dengan prosedur pembayaran yang telah ditetapkan pada Ulin Mahoni ini dengan metode pembayaran yang telah dipilih oleh Pemilik Akun, yaitu dapat melalui transfer, kartu kredit, virtual account, payment gateaway, paylater dan/atau sarana pembayaran lainnya yang disediakan pada Ulin Mahoni.</li>
                            <li class="policy-content">Ulin Mahoni akan melakukan proses transaksi sesuai dengan S&K ini serta peraturan perundang-undangan yang berlaku di Negara Kesatuan Republik Indonesia.</li>
                            <li class="policy-content">Pemilik Akun memahami bahwa setiap metode pembayaran memiliki prosedur yang berbeda-beda, dan dengan ini Pemilik Akun bersedia dan setuju untuk menyelesaikan setiap prosedur pembayaran sesuai dengan pilihan Pemilik Akun, termasuk juga menginput bukti pembayaran yang telah dilakukan pada Ulin Mahoni.</li>
                            <li class="policy-content">Pemilik Akun wajib membayarkan seluruh biaya-biaya yang timbul termasuk namun tidak terbatas pada bunga kartu kredit, bunga cicilan, pajak, dan biaya-biaya lain yang timbul sehubungan dengan metode pembayaran yang telah dipilih pada Ulin Mahoni.</li>
                            <li class="policy-content">Pembayaran harga sewa Properti yang dipesan melalui Ulin Mahoni wajib dibayarkan melalui Ulin Mahoni dengan jumlah yang tepat sebagaimana tertera di dalam Ulin Mahoni dan menggunakan mata uang Rupiah.</li>
                            <li class="policy-content">Terdapat jangka waktu pembayaran yang tertera pada Ulin Mahoni. Pemilik Akun memahami kegagalan pembayaran akibat telah lewatnya jangka waktu sehingga pemesanan Properti akan dibatalkan secara otomatis merupakan tanggung jawab dari Pemilik Akun. Dengan demikian, Pemilik dan Pemilik Akun melepaskan haknya untuk mengajukan klaim dan/atau tuntutan dan/atau gugatan dalam bentuk apapun terhadap KAP.</li>
                            <li class="policy-content">Ulin Mahoni akan menerbitkan invoice atas nama Pemilik Akun dengan mencantumkan nilai transaksi yang dilakukan oleh Pemilik Akun.</li>
                            <li class="policy-content">Ulin Mahoni dapat meminta Pemilik Akun untuk melakukan verifikasi terhadap informasi dan/atau dokumen mengenai transaksi pembayaran apabila diperlukan. Apabila Pemilik Akun tidak melakukan verifikasi, maka proses pemesanan tidak akan diselesaikan dan akan dibatalkan.</li>
                            <li class="policy-content">Setelah transaksi pembayaran Properti dari Pemilik Akun terverifikasi oleh Ulin Mahoni, maka Pemilik Akun dan/atau Penyewa menyatakan:
                                <ol class="policy-sub-list">
                                    <li class="policy-content">Telah sepakat dan setuju untuk terikat dan tunduk pada Perjanjian.</li>
                                    <li class="policy-content">Telah membaca, mengerti, memahami, dan menyetujui seluruh gambar, video, informasi dan deskripsi mengenai Properti termasuk namun tidak terbatas pada kondisi, warna, lokasi, letak, interior, fasilitas, dan segala hal yang berkaitan dengan Properti yang mungkin dapat berbeda karena adanya pengaruh dari tampilan pada monitor atau sudut pandang pengambilan foto yang diunggah.</li>
                                    <li class="policy-content">Telah membaca, mengerti, memahami, dan menyetujui seluruh S&K ini.</li>
                                </ol>
                            </li>
                            <li class="policy-content">KAP berhak dan berwenang untuk menolak transaksi dan tidak menyelesaikan proses pembayaran apabila dengan seluruh metode pembayaran yang telah dipilih oleh Pemilik Akun (transfer, kartu kredit, virtual account, payment gateaway, paylater dan/atau sarana pembayaran lainnya), apabila transaksi dan prosedur pembayaran tidak sesuai dengan S&K ini.</li>
                            <li class="policy-content">Segala kerugian yang timbul terhadap bank dan/atau penyedia jasa pembayaran yang diakibatkan oleh adanya pelanggaran dan/atau ketidaksesuaian prosedur dan/atau kecurangan yang dilakukan oleh Pemilik Akun, maka hal tersebut sepenuhnya merupakan tanggung jawab Pemilik Akun. Dengan demikian Pemilik Akun dan Pemilik melepaskan haknya untuk mengajukan klaim dan/atau tuntutan dan/atau gugatan dalam bentuk apapun terhadap KAP.</li>
                            <li class="policy-content">Penyewa memahami bahwa KAP berwenang untuk mengambil keputusan terhadap permasalahan yang terjadi terkait dengan transaksi dan prosedur pembayaran dengan terlebih dahulu mempelajari, menganalisa, dan mempertimbangkan alat bukti yang KAP peroleh. Dengan demikian, keputusan yang dibuat oleh KAP adalah bersifat final dan mengikat Penyewa dan Pemilik. Dalam hal ini Penyewa dan Pemilik melepaskan haknya untuk mengajukan klaim dan/atau tuntutan dan/atau gugatan dalam bentuk apapun terhadap KAP.</li>
                            <li class="policy-content">Pemilik Akun dan/atau Penyewa memahami dan menyetujui segala transaksi pembayaran yang dilakukan diluar Ulin Mahoni dan/atau tanpa sepengetahuan KAP (melalui fasilitas/jaringan pribadi, pengiriman pesan, pengaturan transaksi khusus diluar Ulin Mahoni atau upaya lainnya) merupakan tanggung jawab pribadi dari Pemilik Akun dan/atau Penyewa.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>H. Tanggal Masuk (Check In)</h2>
                        <ol class="policy-list">
                            <li class="policy-content">Penyewa wajib melakukan check in dan verifikasi data melalui Ulin Mahoni pada Tanggal Masuk yang tertera pada Perjanjian.</li>
                            <li class="policy-content">Apabila Penyewa bukan merupakan Pemilik Akun, maka tetap wajib menyerahkan data, informasi dan/atau dokumen serta verifikasi sebelum masuk dan menempati Properti. Apabila Penyewa yang bukan Pemilik Akun menolak untuk melakukan verifikasi, maka Penyewa tidak diperbolehkan untuk masuk dan menempati Properti serta Penyewa tidak dapat menuntut pengembalian atas pembayaran sewa Properti yang telah dibayarkannya. Dengan demikian, Pemilik Akun melepaskan haknya untuk mengajukan klaim dan/atau tuntutan dan/atau gugatan dalam bentuk apapun terhadap KAP dan Pemilik.</li>
                            <li class="policy-content">Penyewa dapat memasuki dan menempati Properti paling cepat pada jam 11.00 WIB pada Tanggal Masuk.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>I. Pindah Kamar (Relocation)</h2>
                        <p class="policy-content">Pindah Kamar (Relocation) hanya dapat dilakukan dengan syarat dan ketentuan, sebagai berikut:</p>
                        <ol class="policy-list">
                            <li class="policy-content">Properti yang telah dipesan tidak tersedia setelah dilakukannya pembayaran dan/atau check in oleh Penyewa.</li>
                            <li class="policy-content">Terdapat perbedaan dan/atau ketidaksesuaian informasi mengenai spesifikasi kamar dan/atau jaringan wifi yang tertera pada Ulin Mahoni setelah dilakukannya pembayaran dan/atau check in oleh Penyewa.</li>
                            <li class="policy-content">Pindah Kamar (Relocation) hanya dapat dilakukan pada Tanggal Masuk dengan terlebih dahulu menyampaikan permintaan secara tertulis kepada Ulin Mahoni.</li>
                            <li class="policy-content">Pindah Kamar (Relocation) hanya berlaku 1 (satu) kali selama Jangka Waktu yang tertera di dalam Perjanjian.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>J. Tanggal Keluar (Check Out)</h2>
                        <ol class="policy-list">
                            <li class="policy-content">Penyewa wajib meninggalkan Properti dan mengembalikan kunci Properti kepada tim KAP yang berada di lokasi Properti paling lambat pada pukul 22.00 WIB pada Tanggal Keluar.</li>
                            <li class="policy-content">Penyewa wajib meninggalkan Properti keadaan baik, terpelihara, seperti kondisi awal dan siap ditempati oleh Penyewa baru.</li>
                            <li class="policy-content">Penyewa dilarang meninggalkan Properti dan mengembalikan kunci Properti, kecuali adanya persetujuan secara tertulis dari KAP.</li>
                            <li class="policy-content">Apabila Penyewa tetap tidak meninggalkan Properti pada batas waktu sebagaimana ditentukan dalam angka 1 di atas, maka Penyewa dikenakan denda sebesar Rp. 300.000,- (tiga ratus ribu Rupiah) untuk setiap hari keterlambatan keluar dari Properti. KAP atas kebijakannya sendiri dapat secara sepihak memotong jumlah deposit Penyewa sejumlah denda yang wajib dibayarkan oleh Penyewa, dan apabila jumlah deposit tidak mencukupi, maka Penyewa wajib melakukan pembayaran atas kekurangannya saat diminta oleh KAP secara transfer ke rekening KAP.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>K. Perpanjangan Jangka Waktu</h2>
                        <ol class="policy-list">
                            <li class="policy-content">Apabila Jangka Waktu sebagaimana tertera dalam Perjanjian telah berakhir dan Penyewa ingin melanjutkan Jangka Waktu dengan Perjanjian yang baru, maka dalam dalam jangka waktu 14 (empat belas) hari sebelum Tanggal Keluar, Penyewa wajib melakukan pemesanan kembali Properti pada Ulin Mahoni. Dalam hal terdapat perbedaan harga sewa Properti pada Perjanjian yang lama dengan Perjanjian yang baru, Penyewa wajib untuk membayar sesuai dengan harga sewa yang baru.</li>
                            <li class="policy-content">Apabila dalam jangka waktu 14 (empat belas) hari sebelum Tanggal Keluar, Penyewa tidak melakukan pemesanan kembali Properti pada Ulin Mahoni dan Properti yang sedang ditempati Penyewa sudah diambil oleh Penyewa lain, maka Penyewa setuju untuk Pindah Kamar (Relication) dan Penyewa melepaskan haknya untuk mengajukan klaim dan/atau tuntutan dan/atau gugatan dalam bentuk apapun terhadap KAP.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>L. Penjadwalan Kembali (Reschedule)</h2>
                        <p class="policy-content">Penjadwalan Kembali (Reschedule) hanya dapat dilakukan dengan syarat dan ketentuan, sebagai berikut:</p>
                        <ol class="policy-list">
                            <li class="policy-content">Transaksi pembayaran telah dilakukan oleh Penyewa melalui Ulin Mahoni, dan telah terverifikasi.</li>
                            <li class="policy-content">Permohonan Penjadwalan Kembali (Reschedule) wajib disampaikan oleh Penyewa melalui Ulin Mahoni paling lambat 7 (tujuh) hari kalender sebelum Tanggal Masuk dan dilakukan melalui Ulin Mahoni.</li>
                            <li class="policy-content">Permohonan Penjadwalan Kembali (Reschedule) akan disetujui oleh Pemilik apabila masih terdapat ketersediaan Properti pada tanggal perubahan yang diajukan oleh Penyewa.</li>
                            <li class="policy-content">Apabila terdapat perubahan harga sewa yang timbul akibat dari Penjadwalan Kembali (Reschedule), maka Penyewa wajib untuk melakukan pembayaran terhadap selisih harga sewa tersebut melalui Ulin Mahoni paling lambat 1x24 (satu kali dua puluh empat) jam sebelum Tanggal Masuk yang baru.</li>
                            <li class="policy-content">KAP berhak untuk menolak permohonan Penjadwalan Kembali (Reschedule) apabila ditemukan adanya kecurangan dan/atau pelanggaran terhadap S&K yang dilakukan oleh Penyewa dan/atau tidak tersedianya Properti.</li>
                            <li class="policy-content">Penyewa memahami bahwa persetujuan Penjadwalan Kembali (Reschedule) merupakan kewenangan Pemilik sepenuhnya. Dalam hal permohonan Penjadwalan Kembali (Reschedule) ditolak oleh Pemilik atau Penyewa tidak memberikan konfirmasi mengenai Penjadwalan Kembali (Reschedule) setelah disetujui oleh Pemilik, maka Jangka Waktu tetap akan dimulai sesuai dengan Tanggal Masuk yang telah disepakati di dalam Perjanjian.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>M. Pembatalan (Cancelation)</h2>
                        <p class="policy-content">Pembatalan (Cancelation) hanya dapat dilakukan dengan syarat dan ketentuan, sebagai berikut:</p>
                        <ol class="policy-list">
                            <li class="policy-content">Transaksi pembayaran telah dilakukan oleh Penyewa melalui Ulin Mahoni dan terverifikasi.</li>
                            <li class="policy-content">Properti yang telah dipesan tidak tersedia setelah dilakukannya pembayaran dan/atau check in oleh Penyewa.</li>
                            <li class="policy-content">Terdapat perbedaan dan/atau ketidaksesuaian informasi mengenai spesifikasi kamar dan/atau jaringan wifi yang tertera pada Ulin Mahoni setelah dilakukannya pembayaran dan/atau check in oleh Penyewa.</li>
                            <li class="policy-content">Adanya pembatalan atau penolakan dari Pemilik terhadap pemesanan Penyewa.</li>
                            <li class="policy-content">Ditemukan adanya kecurangan dan/atau pelanggaran terhadap S&K yang dilakukan oleh Penyewa.</li>
                            <li class="policy-content">Permohonan Pembatalan (Cancelation) wajib disampaikan oleh Penyewa melalui Ulin Mahoni paling lambat 7 (tujuh) hari kalender sebelum Tanggal Masuk dan dilakukan melalui Ulin Mahoni.</li>
                            <li class="policy-content">Penghuni memahami dan setuju apabila pemesanan dibayarkan diluar Ulin Mahoni dan/atau pembayaran sudah diteruskan kepada Pemilik, maka permohonan Pembatalan (Cancelation) hanya dapat dimintakan secara langsung oleh Penyewa kepada Pemilik. Atas hal tersebut, Ulin Mahoni tidak dapat dimintakan pertanggungjawaban dan tidak memiliki kewajiban apapun untuk melakukan proses Pembatalan (Cancelation).</li>
                            <li class="policy-content">Penyewa memahami bahwa KAP berwenang untuk mengambil keputusan terhadap permasalahan yang terjadi terkait dengan Pembatalan (Cancelation) dengan terlebih dahulu mempelajari, menganalisa, dan mempertimbangkan alat bukti yang KAP peroleh. Dengan demikian, keputusan yang dibuat oleh KAP adalah bersifat final dan mengikat Penyewa dan Pemilik. Dalam hal ini Penyewa dan Pemilik melepaskan haknya untuk mengajukan klaim dan/atau tuntutan dan/atau gugatan dalam bentuk apapun terhadap KAP.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>N. Pengembalian Dana (Refund)</h2>
                        <p class="policy-content">Pengembalian Dana (Refund) hanya dapat dilakukan dengan syarat dan ketentuan, sebagai berikut:</p>
                        <ol class="policy-list">
                            <li class="policy-content">Pengembalian Dana (Refund) hanya dilakukan kepada Penyewa apabila telah memenuhi syarat dan ketentuan serta kondisi sebagaimana huruf J angka 1 sampai dengan angka 6 S&K serta ketentuan dan persetujuan dari bank terkait.</li>
                            <li class="policy-content">Penyewa tidak dapat meminta Pengembalian Dana (Refund) terhadap Biaya Layanan yang telah dibayarkan.</li>
                            <li class="policy-content">Pengembalian Dana (Refund) akan dikenakan biaya administrasi sebesar Rp. 30.000,- (tiga puluh ribu Rupiah).</li>
                            <li class="policy-content">Pengembalian Dana (Refund) dapat dikenakan biaya-biaya lain sesuai dengan ketentuan bank. Pembayaran biaya-biaya lain tersebut merupakan tanggung jawab Penyewa dan tidak dapat dibebankan kepada KAP dan/atau Pemilik.</li>
                            <li class="policy-content">Pengembalian Dana (Refund) tidak dapat dilakukan apabila ditemukan adanya kecurangan dan/atau pelanggaran terhadap S&K yang dilakukan oleh Penyewa.</li>
                            <li class="policy-content">Penyewa memahami bahwa KAP berwenang untuk mengambil keputusan terhadap permasalahan yang terjadi terkait dengan Pengembalian Dana (Refund) dengan terlebih dahulu mempelajari, menganalisa, dan mempertimbangkan alat bukti yang KAP peroleh. Dengan demikian, keputusan yang dibuat oleh KAP adalah bersifat final dan mengikat Penyewa dan Pemilik. Dalam hal ini Penyewa dan Pemilik melepaskan haknya untuk mengajukan klaim dan/atau tuntutan dan/atau gugatan dalam bentuk apapun terhadap KAP.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>O. Ketentuan Promo</h2>
                        <ol class="policy-list">
                            <li class="policy-content">KAP dapat memberikan promo berupa pemotongan harga (discount), voucher, cashback, dan/atau promo dalam bentuk lainnya yang akan ditetapkan oleh KAP dari waktu ke waktu sesuai dengan syarat dan ketentuan yang akan ditetapkan untuk setiap promo.</li>
                            <li class="policy-content">KAP berhak dan berwenang untuk sewaktu-waktu menarik dan/atau membatalkan promo dengan melakukan penurunan iklan, menghapus iklan baik sementara maupun permanen dan/atau membatalkan transaksi, menahan dana yang telah dibayarkan serta hal-hal lainnya dalam hal ditemukan adanya manipulasi dan/atau pelanggaran dalam penggunaan promo pada akun maupun transaksi pada Ulin Mahoni yang bertujuan untuk mendapatkan keuntungan pribadi dengan cara curang (fraud) dan/atau melanggar (non-compliance) terhadap S&K.</li>
                            <li class="policy-content">Pemilik Akun hanya diperbolehkan menggunakan 1 (satu) akun untuk mengikuti promo pada Ulin Mahoni. Apabila Pemilik Akun memiliki lebih dari 1 (satu) akun, maka Pemilik Akun hanya berhak menggunakan promo terbatas hanya pada 1 (satu) akun saja.</li>
                            <li class="policy-content">Apabila promo dilakukan oleh pihak ketiga lainnya, maka KAP tidak bertanggung jawab atas segala bentuk promo yang ditawarkan beserta akibatnya. Pemilik Akun memahami dan setuju seluruh tanggung jawab terkait promo tersebut sepenuhnya menjadi beban pihak ketiga, dengan demikian Penyewa dan Pemilik melepaskan haknya untuk mengajukan klaim dan/atau tuntutan dan/atau gugatan dalam bentuk apapun terhadap KAP.</li>
                            <li class="policy-content">KAP berhak memberikan penalti untuk setiap penyalahgunaan transaksi yang menggunakan promo dan atau transaksi palsu sesuai dengan kerugian yang diakibatkan oleh kecurangan transaksi tersebut.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>P. Keamanan serta Perlindungan Informasi dan Data Pribadi</h2>
                        <ol class="policy-list">
                            <li class="policy-content">Pengunjung, Pemilik Akun dan/atau Penyewa dilarang menggunakan Ulin Mahoni untuk:
                                <ol class="policy-sub-list">
                                    <li class="policy-content">Melakukan perbuatan yang melanggar hukum Negara Kesatuan Republik Indonesia.</li>
                                    <li class="policy-content">Melakukan perbuatan yang menghambat kinerja dari Ulin Mahoni dengan menggunakan cara, alat, teknologi, software dan/atau metode apapun.</li>
                                    <li class="policy-content">Melakukan pencurian data dan informasi pada Ulin Mahoni dengan menggunakan cara, alat, teknologi, software dan/atau metode apapun.</li>
                                    <li class="policy-content">Mengakses, memodifikasi, atau memanfaatkan bagian sistem, data, atau akun milik pihak lain tanpa izin yang sah dengan menggunakan cara, alat, teknologi, software dan/atau metode apapun.</li>
                                    <li class="policy-content">Melakukan manpulasi data, perambanan (crawling/scraping), kegiatan otomatisasi dalam transaksi pembayaran, promosi dan lainnya serta aktivitas lain yang secara wajar dapat dinilai sebagai tindakan manipulasi sistem dengan menggunakan cara, alat, teknologi, software dan/atau metode apapun.</li>
                                    <li class="policy-content">Mengunggah, mengirimkan, atau menggunakan kode berbahaya atau tidak sah yang dapat mengganggu, merusak, atau mengakses sistem secara ilegal dengan menggunakan cara, alat, teknologi, software dan/atau metode apapun.</li>
                                    <li class="policy-content">Melakukan aktivitas yang bertujuan untuk menghindari sistem keamanan, merusak integritas data, atau memanipulasi mekanisme autentikasi dan otorisasi dengan menggunakan cara, alat, teknologi, software dan/atau metode apapun.</li>
                                </ol>
                            </li>
                            <li class="policy-content">KAP berkomitmen untuk terus berupaya melindungi informasi dan data pribadi dari Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa dengan sistem keamanan yang wajar dalam pemrosesan data pribadi termasuk prosedur, teknis dan organisasi, untuk mencegah akses, pengumpulan, penggunaan, penyebarluasan, penyalinan, modifikasi, penghapusan yang tidak sah atau risiko yang sama.</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa dengan ini menyetujui untuk memberikan hak dan kewenangan kepada KAP untuk memperoleh, menerima, mengumpulkan, mengolah, menganalisa, menyimpan, melakukan perbaikan dan/atau pembaruan, menampilkan, mengumumkan, melakukan transfer, mengungkapkan, menghapus dan/atau memusnahkan informasi dan data pribadi diberikan oleh Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa pada saat menggunakan Ulin Mahoni. Informasi dan data pribadi tersebut adalah seluruh informasi yang dibutuhkan untuk dapat menggunakan dan melakukan pemesanan serta pembayaran Properti pada Ulin Mahoni tanpa terkecuali.</li>
                            <li class="policy-content">Dengan melakukan konfirmasi dan menyetujui S&K ini, maka Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa setuju bahwa KAP boleh menggunakan informasi dan data pribadi yang telah diberikan melalui Ulin Mahoni untuk tujuan apapun yang berkaitan dengan kegiatan usaha KAP termasuk namun tidak terbatas pada promo, publikasi ulasan, penawaran layanan/fitur, dan/atau tujuan lain yang akan di informasikan kemudian.</li>
                            <li class="policy-content">KAP sangat memahami dan menghargai keamanan serta perlindungan informasi dan data pribadi, oleh karenanya KAP berkomitmen untuk terus berupaya melindungi informasi dan data pribadi milik Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa dengan membuat syarat dan ketentuan mengenai perlindungan informasi dan data pribadi yang relevan. Syarat dan ketentuan mengenai perlindungan informasi dan data pribadi tersebut telah disediakan KAP secara terpisah serta merupakan satu kesatuan dan bagian yang tidak terpisahkan dengan S&K ini (<a href="{{ route('privacy-policy') }}" target="_blank" class="text-teal-600 hover:text-teal-500 underline">Kebijakan Privasi</a>).</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>Q. Hak Kekayaan Intelektual</h2>
                        <ol class="policy-list">
                            <li class="policy-content">KAP merupakan pemilik tunggal dan pemegang sah atas Ulin Mahoni serta seluruh Hak Kekayaan Intelektual yang ada pada Ulin Mahoni termasuk namun tidak terbatas pada perangkat lunak (software), website, konten, gambar, video, suara, data, tulisan, teks, merek dagang, logo, ikon, dan lainnya yang melekat baik langsung maupun tidak langsung pada Ulin Mahoni.</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun, Penyewa dan/atau pihak manapun dilarang keras untuk menggunakan, memodifikasi, menggandakan, mereproduksikan, mempublikasikan, mengalihkan dan/atau memindahtangankan dengan cara apapun dan/atau dengan alasan apapun baik sebagian maupun seluruh Hak Kekayaan Intelektual dari Ulin Mahoni kepada pihak manapun tanpa adanya persetujuan secara tertulis terlebih dahulu dari KAP.</li>
                            <li class="policy-content">Penggunaan Ulin Mahoni oleh Pengunjung, Pemilik, Pemilik Akun, Penyewa dan/atau pihak manapun, tidak akan dianggap atau ditafsirkan bahwa Ulin Mahoni memberikan hak atau lisensi baik secara langsung atau tidak langsung, untuk memperbanyak atau menggunakan Ulin Mahoni. Pengunjung, Pemilik, Pemilik Akun, Penyewa dan/atau pihak manapun yang mengakses Ulin Mahoni tidak dapat mengklaim hak, kepemilikan atau kepentingan apapun di dalamnya. Dengan menggunakan atau mengakses Ulin Mahoni, Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa setuju untuk tidak mengubah, menyesuaikan, menyewa, menjual atau membuat karya turunan dari bagian apapun atas Ulin Mahoni atau kontennya. Tanpa adanya persetujuan tertulis terlebih dahulu dari KAP, Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa dilarang untuk:
                                <ol class="policy-sub-list">
                                    <li class="policy-content">Me-mirror atau membingkai bagian apapun atau seluruh situs di server manapun atau sebagai bagian dari situs website lain manapun; dan</li>
                                    <li class="policy-content">Robot, spider atau perangkat otomatis maupun proses manual lain apapun untuk memantau atau menyalin konten situs.</li>
                                </ol>
                            </li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>R. Pernyataan dan Jaminan</h2>
                        <ol class="policy-list">
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa menyatakan S&K ini merupakan perjanjian yang sah antara Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa dengan KAP. Dengan ini Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa setuju untuk mengikatkan diri dengan segala ketentuan yang tertulis di dalam S&K.</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa menyatakan setuju terhadap seluruh S&K ini tanpa terkecuali, dan akan melaksanakan S&K ini dengan iktikad baik.</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa menyatakan tidak akan mengalihkan baik sebagian maupun seluruh kewajiban sebagaimana S&K ini kepada pihak lain tanpa adanya persetujuan tertulis terlebih dahulu dari KAP.</li>
                            <li class="policy-content">KAP sewaktu-waktu berhak untuk melakukan proses Know Your Customer (KYC) kepada Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa sesuai dengan peraturan perundang-undangan yang berlaku di Negara Kesatuan Republik Indonesia, kebijakan KAP serta S&K ini.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>S. Laporkan Permasalahan</h2>
                        <p class="policy-content">Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa dapat memberikan laporan kepada KAP apabila dalam menggunakan Ulin Mahoni mengalami masalah atau gangguan, termasuk namun tidak terbatas seperti: konten yang menyebut dan menyinggung (terkait suku, agama, ras dan antar golongan (SARA)), konten yang memuat kata-kata, komentar, gambar, pornografi, diskriminasi, merendahkan atau menyudutkan orang lain, vulgar, bersifat ancaman, beriklan atau melakukan promosi selain Ulin Mahoni atau hal-hal lain yang dapat dianggap tidak sesuai dengan nilai dan norma, kebijakan maupun S&K ini, termasuk didalamnya terkait dengan Hak Kekayaan Intelektual. KAP berhak untuk menyelidiki dan melakukan tindakan yang dianggap perlu atas pelanggaran tersebut penghapusan konten, pemblokiran akun dan lainnya.</p>
                    </div>

                    <div class="policy-section">
                        <h2>T. Umpan Balik (Feedback) dan Kiriman</h2>
                        <ol class="policy-list">
                            <li class="policy-content">Umpan balik (feedback) (baik dalam bentuk komentar maupun peringkat) maupun kiriman yang tercantum pada Ulin Mahoni, merupakan umpan balik (feedback) dan kiriman yang KAP terima dari pihak ketiga. KAP tidak dapat memastikan dan tidak melakukan verifikasi lebih lanjut terhadap umpan balik (feedback) maupun kiriman yang diterima, oleh karenanya KAP tidak bertanggung jawab segala hal yang berkaitan dengan umpan balik (feedback) dan kiriman tersebut. Lebih lanjut KAP menyatakan penolakan atas segala bentuk permintaan klaim, gugatan, permintaan kerugian, atau pertanggungjawaban dari dan/atau kepada pihak manapun sehubungan dengan umpan balik (feedback) dan kiriman yang ditampilkan pada Ulin Mahoni. Dengan demikian, Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa setuju untuk melepaskan haknya untuk mengajukan klaim dan/atau tuntutan dan/atau gugatan dalam bentuk apapun terhadap KAP.</li>
                            <li class="policy-content">KAP dengan diskresinya sendiri dan/atau penilaiannya sendiri dapat melakukan tindakan-tindakan terhadap pihak manapun yang memberikan umpan balik (feedback) dan kiriman yang bertentangan dengan peraturan perundang-undangan yang berlaku di Negara Kesatuan Republik Indonesia. Dengan demikian, Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa setuju untuk melepaskan haknya untuk mengajukan klaim dan/atau tuntutan dan/atau gugatan dalam bentuk apapun terhadap KAP.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>U. Force Majeure</h2>
                        <ol class="policy-list">
                            <li class="policy-content">Peristiwa force majeure (keadaan memaksa) adalah keadaan memaksa yang terjadi di luar kemampuan dan tidak dapat dihindari oleh KAP serta Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa yang serta secara langsung mempengaruhi pelaksanaan S&K ini, seperti terjadinya huru-hara, perang, bencana alam seperti gempa bumi, angin topan, banjir besar serta kebakaran, runtuhnya Properti, musnahnya Properti, gangguan jaringan pada Ulin Mahoni, adanya peretasan Ulin Mahoni dan/atau bencana lainnya seperti pemogokan umum, pemberontakan, sabotase, perubahan kondisi dan situasi hukum seperti perubahan kebijakan Pemerintah, Undang-Undang dan/atau resesi ekonomi yang secara nyata menunda atau mencegah pelaksaan kewajiban sebagaimana S&K ini, dan KAP, Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa yang tidak mengalami force majeure tidak dapat menuntut yang pihak mengalami force majeure untuk memenuhi kewajibannya.</li>
                            <li class="policy-content">Apabila terjadi force majeure, maka KAP, Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa yang mengalami force majeure harus memberitahukan secara tertulis tentang terjadinya force majeure tersebut dengan melengkapi secara tertulis data-data/bukti-bukti yang diperlukan selambat-lambatnya 3 (tiga) hari kerja sejak terjadinya force majeure. Apabila tidak/lalai diberitahukan, maka force majeure tersebut dianggap tidak pernah terjadi.</li>
                            <li class="policy-content">Atas pemberitahuan dari KAP, Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa yang mengalami force majeure, maka dalam waktu 3 (tiga) hari kerja sejak menerima pemberitahuan tersebut, KAP, Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa yang tidak mengalama force majeure harus segera memberikan jawabannya apakah akan menerima atau menolak force majeure tersebut. Apabila dalam waktu 3 (tiga) hari kerja jawaban tidak atau lalai diberikan, maka force majeure dianggap diterima.</li>
                            <li class="policy-content">Dalam hal force majeure diterima, maka pelaksanaan S&K ini ditunda untuk jangka waktu yang disepakati bersama untuk kemudian ditetapkan menjadi Kesepakatan Bersama (dalam bentuk Berita Acara) yang menjadi satu-kesatuan dan bagian yang tidak terpisahkan dari S&K ini, sehingga upaya/langkah yang dilakukan (disepakati) akan menyelesaikan permasalahan force majeure yang memuaskan KAP, Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa.</li>
                            <li class="policy-content">Dalam hal force majeure ditolak berdasarkan alasan yang sah, maka semua biaya dan/atau resiko yang timbul dari kejadian force majeure sebagaimana dimaksud pada S&K ini, menjadi beban dan tanggung jawab KAP, Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa yang mengalaminya.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>V. Pengakhiran</h2>
                        <ol class="policy-list">
                            <li class="policy-content">S&K ini dapat diakhir secara sepihak tanpa adanya pemberitahuan terlebih dahulu kepada Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa. Untuk menghindari keragu-raguan pengakhiran dapat terjadi dengan alasan termasuk namun tidak terbatas pada tidak terpenuhinya baik sebagian maupun seluruh S&K ini, terdapat kesepakatan serta pernyataan dan jaminan pada S&K ini yang ternyata tidak benar, tidak akurat atau tidak tepat, adanya peraturan perundang-undangan dan/atau kebijakan pemerintah/instansi yang menghentikan kegiatan usaha dari KAP, sehingga KAP tidak dapat menjalankan kewajibannya berdasarkan S&K ini.</li>
                            <li class="policy-content">Pengakhiran terhadap S&K ini tidak membebaskan Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa dari kewajiban termasuk namun tidak terbatas pada jaminan, ganti kerugian, pengabaian dan/atau pembatasan kewajiban yang sebelumnya telah timbul dan disetujui.</li>
                            <li class="policy-content">Apabila terdapat dana yang tertahan dalam penguasaan KAP, Pemilik Akun dan/atau Penyewa setuju serta memberikan hak dan kewenangan kepada KAP untuk sewaktu-waktu mengurangi dana tertahan tersebut guna memenuhi kewajiban Pemilik Akun dan/atau Penyewa kepada KAP.</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa sepakat untuk mengesampingkan Pasal 1266 Kitab Undang-Undang Hukum Perdata Indonesia sejauh bahwa pernyataan Pengadilan diperlukan untuk pengakhiran S&K ini.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>W. Pembaruan, Perubahan dan Masa Berlaku S&K</h2>
                        <ol class="policy-list">
                            <li class="policy-content">KAP dapat sewaktu-waktu melakukan perubahan atau pembaharuan terhadap S&K ini tanpa adanya pemberitahuan sebelumnya. KAP menyarankan agar Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa membaca secara seksama dan memeriksa S&K ini dari waktu ke waktu untuk mengetahui perubahan atau pembaruan apapun. Dengan tetap mengakses dan menggunakan Ulin Mahoni, maka Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa dianggap menyetujui perubahan-perubahan dalam S&K ini.</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa memahami bahwa versi terbaru dari S&K ini akan menggantikan semua versi sebelumnya. KAP dapat menyediakan website ataupun aplikasi lain yang dikelola oleh pihak lain, dan dengan mengklik tautan tersebut Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa menyatakan, mengakui, dan menyetujui bahwa tindakan tersebut adalah tindakan sukarela dari Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa untuk melihat atau memasuki situs lain yang tidak diawasi atau ditanggung oleh KAP.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>X. Bahasa, Hukum yang Berlaku dan Penyelesaian Sengketa</h2>
                        <ol class="policy-list">
                            <li class="policy-content">S&K dibuat dalam Bahasa Indonesia dan dapat diterjemahkan dalam bahasa lain. Apabila terdapat perbedaan penafsiran dan/atau ketidakkonsistenan antara S&K versi Bahasa Indonesia dengan S&K versi Bahasa Inggris, maka S&K versi Bahasa Indonesia yang berlaku, sah dan mengikat.</li>
                            <li class="policy-content">Keabsahan, penafsiran, dan pelaksanaan S&K ini serta segala akibat yang ditimbulkannya, diatur, dan tunduk pada peraturan perundang-undangan yang berlaku di wilayah Negara Kesatuan Republik Indonesia.</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa sepakat dan setuju segala tindakan hukum atau sengketa yang timbul sehubungan dengan penggunaan Ulin Mahoni, maka akan diselesaikan pada Pengadilan Negeri Jakarta Barat.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>Y. Penyangkalan (Disclaimer) dan Pembatasan Tanggung Jawab</h2>
                        <ol class="policy-list">
                            <li class="policy-content">Ulin Mahoni beserta sebagian maupun seluruh isinya dibuat dan berdasarkan kondisi kondisi "sebagaimana adanya" dan "sebagaimana tersedia". KAP tidak pernah membuat jaminan atau garansi terhadap:
                                <ol class="policy-sub-list">
                                    <li class="policy-content">Fungsi, layanan dan/atau fitur pada Ulin Mahoni tidak akan terganggu maupun terbebas dari adanya kesalahan, namun setiap kesalahan dan kegagalan tersebut akan segera diperbaiki;</li>
                                    <li class="policy-content">Ulin Mahoni terbebas dari virus, malware dan/atau komponen yang berbahaya. Oleh karenanya, KAP tidak menjami dan tidak bertanggung jawab terhadap akurasi, otorisasi, penggabungan dan/atau kualitas konten yang termuat di dalam Ulin Mahoni terbebas dari materi dan komponen yang berbahaya, tidak pantas dan/atau kontroversial.</li>
                                </ol>
                            </li>
                            <li class="policy-content">KAP termasuk namun tidak terbatas pada Direksi, Dewan Komisaris, karyawan, personil dan/atau afiliasinya tidak bertanggung jawab terhadap segala kerusakan dan/atau kerugian dalam bentuk apapun termasuk namun tidak terbatas pada hilangnya uang, keuntungan, nama baik atau reputasi dan/atau kerugian dalam bentuk lainnya yang diakibatkan secara langsung atau tidak langsung dari kelalaian dan/atau ketidakmampuan Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa dalam menggunakan Ulin Mahoni, pembayaran yang tidak menggunakan akun resmi atau virtual account yang termuat pada Ulin Mahoni dan/atau dengan cara lain yang mengatas namakan KAP dan/atau Ulin Mahoni, kesalahan atau kelalaian menulis rekening, informasi dan/atau data lainnya, kesalahan atau kelalaian bank, pencemaran nama baik yang dilakukan oleh pihak lain, tidakan atau tidak adanya tindakan dari pihak lain, iklan terhadap Properti yang ditampilkan oleh Ulin Mahoni diduga merupakan iklan palsu. Dengan demikian, Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa setuju untuk melepaskan haknya untuk mengajukan klaim dan/atau tuntutan dan/atau gugatan dalam bentuk apapun terhadap KAP.</li>
                            <li class="policy-content">KAP termasuk namun tidak terbatas pada Direksi, Dewan Komisaris, karyawan, personil dan/atau afiliasinya tidak bertanggung jawab terhadap kerugian dan/atau kerusakan yang timbul baik secara langsung maupun tidak langsung yang berkaitan dengan penggunaan Ulin Mahoni, termasuk namun tidak terbatas pada kerusakan-kerusaka, kehilangan manfaat ekonomi dan/atau kerugian. Dengan demikian, Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa setuju untuk melepaskan haknya untuk mengajukan klaim dan/atau tuntutan dan/atau gugatan dalam bentuk apapun terhadap KAP.</li>
                            <li class="policy-content">Tidak ada satu ketentuan dalam S&K ini yang dapat diartikan sebagai suatu bentuk jaminan keamanan dan/atau keselamatan terhadap barang pribadi Penyewa selama tinggal di Properti. Penyewa wajib untuk menjaga sendiri barang pribadi yang dibawa dan disimpan atau ditinggalkan pada Properti.</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa memahami dan setuju batasan tanggung jawab KAP adalah:
                                <ol class="policy-sub-list">
                                    <li class="policy-content">Menyediakan S&K berserta syarat dan ketentuan turunannya pada Ulin Mahoni;</li>
                                    <li class="policy-content">Menyediakan sarana pelaporan bagi Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa kepada KAP;</li>
                                    <li class="policy-content">Berhak menghapus dan/atau memblokir konten yang bertentangan dengan dan/atau dilarang oleh peraturan perundang-undangan yang berlaku di Negara Kesatuan Republik Indonesia dan/atau norma yang berlaku dimasyarakat; atau</li>
                                    <li class="policy-content">Mengiklankan Properti.</li>
                                </ol>
                            </li>
                            <li class="policy-content">Apabila terjadi pelanggaran terhadap peraturan perundang-undangan yang berlaku di Negara Kesatuan Republik Indonesia termasuk namun tidak terbatas terjadinya kecurangan atau pelanggaran (fraud), ketidak sesuaian dan/atau pelanggaran ketidakpatuhan hukum (non-compliance) yang dilakukan oleh Pengunjung, Pemilik, Pemilik Akun, Penyewa dan/atau pihak lain, maka:
                                <ol class="policy-sub-list">
                                    <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun, Penyewa dan/atau pihak lain membebaskan KAP dari setiap klaim dan/atau tuntutan dan/atau gugatan dalam bentuk apapun.</li>
                                    <li class="policy-content">KAP berhak dan berwenang untuk menghapus dan/atau memblokir konten, gambar, video, informasi dan memberikan deskripsi apapun yang diperlukan sehubungan dengan kecurangan atau pelanggaran (fraud), ketidak sesuaian dan/atau pelanggaran ketidakpatuhan hukum (non-compliance) serta untuk kepentingan pelaporan dan proses hukum yang dilakukan oleh Aparat Penegak Hukum.</li>
                                </ol>
                            </li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>Z. Ganti Rugi dan Pembebasan</h2>
                        <ol class="policy-list">
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa memahami Ulin Mahoni merupakan marketplace sewa menyewa properti yang mempertemukan dan menghubungkan Pemilik dengan Pemilik Akun dan/atau Penyewa. Segala transaksi yang terjadi merupakan transaksi antara Pemilik dengan Pemilik Akun dan/atau Penyewa. Sehingga Pengunjung, Pemilik, Pemilik Akun, Penyewa dan/atau pihak lain memahami batasan tanggung jawab Ulin Mahoni hanya sebagai penyedia plaform marketplace dalam bentuk website dan/atau aplikasi.</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun, Penyewa dan/atau pihak lain setuju untuk mengganti kerugian dan membebaskan KAP termasuk namun tidak terbatas pada Direksi, Dewan Komisaris, karyawan, personil dan/atau afiliasinya terhadap setiap klaim dan/atau tuntutan dan/atau gugatan dalam bentuk apapun yang timbul karena alasan apapun termasuk namun tidak terbatas pada:
                                <ol class="policy-sub-list">
                                    <li class="policy-content">Penggunaan Ulin Mahoni;</li>
                                    <li class="policy-content">Konten, gambar, video, informasi dan deskripsi yang ditampilkan, diberikan, disediakan dan/atau diakses melalui Ulin Mahoni;</li>
                                    <li class="policy-content">Peretasan yang dilakukan oleh pihak ketiga kepada akun Pemilik Akun;</li>
                                    <li class="policy-content">Kerugian akibat pembayaran tidak resmi yang dilakukan kepada pihak lain selain kepada Ulin Mahoni baik dengan cara apapun;</li>
                                    <li class="policy-content">Kerugian akibat kesalahan penulisan rekening dan/atau informasi lainnya dan/atau kelalaian pihak bank;</li>
                                    <li class="policy-content">Adanya virus dan/atau software yang berbahaya termasuk namun tidak terbatas pada bot, script, automation, tool, hacking tool, dan lain-lain yang diperoleh dengan mengakses dan/atau menghubungkan ke Ulin Mahoni;</li>
                                    <li class="policy-content">Perselisihan antar Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa satu sama lain;</li>
                                    <li class="policy-content">Pelanggaran terhadap S&K ini; dan/atau</li>
                                    <li class="policy-content">Segala tindakan, baik sengaja maupun kelalaian yang melanggar peraturan perundang- undangan</li>
                                </ol>
                            </li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa setuju dan dengan sukarela melepaskan melepaskan segala perlindungan hukum (yang terdapat dalam undang-undang atau peraturan hukum yang lain) yang akan membatasi cakupan ketentuan pelepasan ini.</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun, Penyewa dan/atau pihak lain membebaskan dan melepaskan KAP termasuk namun tidak terbatas pada Direksi, Dewan Komisaris, karyawan, personil dan/atau afiliasinya atas biaya hukum yang timbul sehubungan dengan adanya pelanggaran S&K ini, pelanggaran hukum dan/atau pelanggaran lainnya.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>AA. Lain-lain</h2>
                        <ol class="policy-list">
                            <li class="policy-content">Apabila terdapat perubahan ketentuan hukum atau kebijakan Pemerintah atau keputusan atau perintah Instansi Penegak Hukum yang mengakibatkan salah satu atau lebih dari ketentuan S&K ini menjadi tidak sah, tidak mengikat atau tidak dapat dilaksanakan, maka hal tersebut tidak serta merta mempengaruhi keabsahan dan pelaksanaan dari ketentuan lain dalam S&K ini, dan syarat-syarat dan ketentuan-ketentuan lainnya tetap berlaku penuh sepanjang dapat diberlakukan berdasarkan hukum yang berlaku.</li>
                            <li class="policy-content">S&K ini memuat keseluruhan dari persetujuan di antara Para Pihak mengenai pokok S&K, oleh karenanya semua persetujuan atau perjanjian yang sebelumnya diadakan di antara para pihak mengenai pokok S&K ini, baik secara lisan maupun tertulis, menjadi tidak berlaku lagi, kecuali persetujuan atau perjanjian itu secara tegas dicantumkan sebagai bagian dari syarat-syarat atau ketentuan-ketentuan dalam naskah S&K ini.</li>
                            <li class="policy-content">S&K ini dapat diubah dan/atau diperbarui sewaktu-waktu oleh KAP untuk menyesuaikan dengan kebutuhan dan/atau perubahan hukum yang berlaku. KAP menghimbau kepada Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa untuk membaca dengan seksama dan memeriksa S&K ini secara berkala dari waktu ke waktu agar mengetahui perubahan dan/atau pembaruan yang terjadi. Dengan tetap mengunjungi, menggunakan dan/atau mengakses Ulin Mahoni, maka Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa dianggap telah membaca, mengerti, memahami dan menyetujui S&K ini berikut perubahan dan/atau pembaruannya serta berlaku mengikat.</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa bersama-sama dengan KAP akan menandatangani Perjanjian yang mengatur syarat dan ketentuan yang berlaku pada Properti dan hal-hal lain yang belum diatur dan/atau belum cukup diatur di dalam S&K ini.</li>
                            <li class="policy-content">Ulin Mahoni sewaktu waktu dapat memberikan penawaran lain yang tunduk pada syarat dan ketentuan turunan yang merupakan satu kesatuan dan bagian yang tidak terpisahkan dari S&K. Dengan menyetujui S&K ini, maka Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa menyetujui syarat dan ketentuan turunan yang akan disusun dan ditetapkan oleh Ulin Mahoni dari waktu ke waktu.</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa setuju apabila dibutuhkan, KAP dapat menghubungi melalui email ….</li>
                            <li class="policy-content">Apabila terdapat pertanyaan dan/atau permasalahan, serta terdapat informasi yang ingin diketahui, maka Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa dapat menghubungi KAP melalui kontak di bawah ini:<br>
                                Telepon: …<br>
                                WhatsApp: …<br>
                                Email: ulinmahoni@gmail.com<br><br>
                                Apabila Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa menerima informasi dari selain nomor maupun email yang tertera pada S&K ini yang mengatasnamakan KAP mohon untuk berhati-hati dan melakukan pengecekan kembali.
                            </li>
                        </ol>
                    </div>

                    <!-- Add more sections as needed -->

                    {{-- <div class="pt-6 border-t border-gray-100 mt-8">
                        <p class="text-sm text-gray-500">Terakhir diperbarui: </p>
                    </div> --}}
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    @include('components.homepage.footer')

    @stack('scripts')
</body>
</html>