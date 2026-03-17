
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi - {{ config('app.name') }}</title>
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
    </style>
</head>
<body class="font-inter antialiased bg-white text-gray-900 tracking-tight">
    <!-- Header -->
    @include('components.homepage.header')
    <div class="header-spacer"></div>

    <main>
        <div class="container mx-auto px-4 py-8">
            <!-- Breadcrumb -->
            <nav class="mb-8">
                <ol class="flex items-center space-x-2 text-gray-500">
                    <li><a href="{{ route('homepage') }}" class="hover:text-gray-700">Beranda</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li class="text-gray-700">Kebijakan Privasi</li>
                </ol>
            </nav>

            <!-- Main Content -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 md:p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-8">KEBIJAKAN PRIVASI DAN PERLINDUNGAN DATA PRIBADI</h1>
                
                <div class="prose max-w-none">
                    <div class="policy-section">
                        <p class="policy-content">Selamat datang di Ulin Mahoni dan terima kasih telah mengunjungi Ulin Mahoni, baik melalui situs web maupun aplikasi. Anda diharuskan untuk membaca, memahami, dan mencermati Kebijakan Privasi dan Perlindungan Data Pribadi ini (selanjutnya disebut "Kebijakan Privasi"). Kebijakan Privasi ini merupakan bagian yang tidak terpisahkan dari Syarat dan Ketentuan dan memiliki kekuatan hukum yang mengikat.</p>
                        <p class="policy-content">Dengan mengunjungi, menggunakan, mengakses dan/atau mendaftar di Ulin Mahoni, maka Anda dianggap telah membaca, memahami, mencermati dan menyetujui seluruh isi Kebijakan Privasi ini tanpa terkecuali dengan ketentuan sebagai berikut:</p>
                    </div>

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
                            <li class="policy-content"><strong>Data Pribadi</strong> adalah data mengenai Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa yang teridentifikasi atau dapat diidentifikasi secara tersendiri atau dikombinasikan dengan informasi lainnya baik secara langsung maupun tidak langsung melalui sistem elektronik dan/atau non-elektronik.</li>
                            <li class="policy-content"><strong>Perlindungan Data Pribadi</strong> adalah upaya untuk melindungi Data Pribadi dan/atau Informasi dalam rangkaian pemrosesan Data Pribadi untuk menjamin hak konstitusional subjek data pribadi.</li>
                            <li class="policy-content"><strong>Informasi</strong> adalah informasi, pernyataan, gagasan, dan/atau tanda yang memuat nilai, makna, dan/atau pesan, baik data, fakta mengenai Pengunjung, Pemilik, Pemilik Akun dan/atau Penyewa, maupun penjelasannya yang dapat dilihat, didengar, dan/atau dibaca, yang disajikan dalam kemasan dan format sesuai dengan perkembangan teknologi informasi dan komunikasi secara elektronik dan/atau nonelektronik.</li>
                            <li class="policy-content"><strong>Pemrosesan Data Pribadi</strong> adalah perbuatan yang meliputi memperoleh, mengumpulkan, mengolah, menganalisis, menyimpan, memperbaiki, memperbarui, menampilkan, mengumumkan, mengalihkan, menyebarluaskan, mengungkapkan, menghapus dan/atau memusnahkan Data Pribadi dan/atau Informasi.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>B. Memperoleh dan Mengumpulkan Data Pribadi</h2>
                        <p class="policy-content">Pengumpulan Data Pribadi dan/atau Informasi oleh Ulin Mahoni dimulai ketika Pengunjung, Pemilik Akun, dan/atau Penyewa mengakses Ulin Mahoni. Dengan mengakses dan/atau menggunakan Ulin Mahoni, Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa menyetujui untuk memberikan Data Pribadi dan/atau Informasi kepada Ulin Mahoni.</p>
                        
                        <p class="policy-content">Ulin Mahoni dapat memperoleh Data Pribadi dan/atau Informasi ketika:</p>
                        <ol class="policy-list">
                            <li class="policy-content">Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa mengakses dan/atau menggunakan Ulin Mahoni;</li>
                            <li class="policy-content">Pengunjung mendaftar dan membuat akun di Ulin Mahoni;</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa melakukan verifikasi yang dikirim dari email resmi seperti yang tercantum dalam Syarat dan Ketentuan;</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa melakukan pemesanan Properti di Ulin Mahoni;</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa mengirimkan formulir yang tersedia di Ulin Mahoni;</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa mengisi survei di Ulin Mahoni;</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa berinteraksi dengan KAP melalui telepon, surat, faks, pesan/teks, WhatsApp, email, pertemuan tatap muka dan/atau pertemuan daring, media sosial, Ulin Mahoni dan/atau cookies;</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa mengirimkan kritik dan/atau saran;</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa memperbarui data akun di Ulin Mahoni;</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa menggunakan fitur di Ulin Mahoni yang memerlukan izin untuk mengakses data yang relevan di perangkat milik Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa;</li>
                            <li class="policy-content">Dari pihak ketiga mana pun, baik yang bekerja sama langsung maupun tidak langsung dengan Ulin Mahoni;</li>
                            <li class="policy-content">Memperoleh informasi yang dapat diakses secara publik; dan/atau</li>
                            <li class="policy-content">Dari pihak lain dan/atau sumber lain yang diperlukan sehubungan dengan penggunaan Ulin Mahoni oleh Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa.</li>
                        </ol>
                        
                        <p class="policy-content">Data Pribadi dan/atau Informasi yang dapat dikumpulkan oleh KAP melalui Ulin Mahoni, terdiri dari:</p>
                        
                        <ol class="policy-list">
                            <li class="policy-content"><strong>Identitas Pribadi</strong>
                                <ol class="policy-sub-list">
                                    <li class="policy-content">Nama lengkap;</li>
                                    <li class="policy-content">Tempat dan tanggal lahir;</li>
                                    <li class="policy-content">Jenis kelamin;</li>
                                    <li class="policy-content">Alamat;</li>
                                    <li class="policy-content">Kewarganegaraan;</li>
                                    <li class="policy-content">Agama;</li>
                                    <li class="policy-content">Status perkawinan;</li>
                                    <li class="policy-content">Usia;</li>
                                    <li class="policy-content">Nomor Induk Kependudukan pada Kartu Keluarga Elektronik atau Nomor Paspor; dan</li>
                                    <li class="policy-content">Informasi pribadi lainnya.</li>
                                </ol>
                            </li>
                            <li class="policy-content"><strong>Keuangan</strong>
                                <ol class="policy-sub-list">
                                    <li class="policy-content">Nama bank;</li>
                                    <li class="policy-content">Nomor rekening bank;</li>
                                    <li class="policy-content">Nomor kartu kredit;</li>
                                    <li class="policy-content">Nama pemilik rekening;</li>
                                    <li class="policy-content">Informasi keuangan lainnya.</li>
                                </ol>
                            </li>
                            <li class="policy-content"><strong>Perangkat</strong>
                                <ol class="policy-sub-list">
                                    <li class="policy-content">Informasi komputer atau perangkat mobil;</li>
                                    <li class="policy-content">Data statistik;</li>
                                    <li class="policy-content">Data iklan;</li>
                                    <li class="policy-content">Alamat IP;</li>
                                    <li class="policy-content">Data log atau file log;</li>
                                    <li class="policy-content">Nomor seri perangkat;</li>
                                    <li class="policy-content">Nama/tipe perangkat;</li>
                                    <li class="policy-content">ID perangkat;</li>
                                    <li class="policy-content">Status online;</li>
                                    <li class="policy-content">Waktu aktifasi;</li>
                                    <li class="policy-content">Versi firmware;</li>
                                    <li class="policy-content">Informasi pembaruan;</li>
                                    <li class="policy-content">Informasi koneksi wireless;</li>
                                    <li class="policy-content">Tipe dan versi sistem operasi;</li>
                                    <li class="policy-content">Nomor versi aplikasi;</li>
                                    <li class="policy-content">Identifikator notifikasi push;</li>
                                    <li class="policy-content">Informasi jaringan seluler;</li>
                                    <li class="policy-content">Tipe browser;</li>
                                    <li class="policy-content">Data Wi-Fi (SSID, BSSID, alamat Mac Wi-Fi, kata sandi Wi-Fi, alamat Mac Bluetooth perangkat), alamat Mac perangkat, alamat Mac Bluetooth perangkat);</li>
                                    <li class="policy-content">Nilai kepercayaan perangkat;</li>
                                    <li class="policy-content">Informasi lain perangkat.</li>
                                </ol>
                            </li>
                            <li class="policy-content"><strong>Kontak</strong>
                                <ol class="policy-sub-list">
                                    <li class="policy-content">Nomor telepon (rumah atau seluler);</li>
                                    <li class="policy-content">Alamat email; dan</li>
                                    <li class="policy-content">Informasi kontak lainnya.</li>
                                </ol>
                            </li>
                            <li class="policy-content"><strong>Harga, pembayaran, dan transaksi</strong>
                                <ol class="policy-sub-list">
                                    <li class="policy-content">Harga properti;</li>
                                    <li class="policy-content">Uang muka;</li>
                                    <li class="policy-content">Nominal transaksi;</li>
                                    <li class="policy-content">Alamat lengkap dan detail properti yang dipesan; dan</li>
                                    <li class="policy-content">Informasi terkait harga, pembayaran, dan transaksi lainnya.</li>
                                </ol>
                            </li>
                            <li class="policy-content"><strong>Lokasi</strong>
                                <ol class="policy-sub-list">
                                    <li class="policy-content">Kode negara;</li>
                                    <li class="policy-content">Zona waktu;</li>
                                    <li class="policy-content">Preferensi bahasa; dan</li>
                                    <li class="policy-content">Informasi tentang lokasi lainnya.</li>
                                </ol>
                            </li>
                            <li class="policy-content">Kesehatan dan kebugaran</li>
                            <li class="policy-content">Pencegahan penipuan</li>
                            <li class="policy-content">Nomor verifikasi yang dikirim melalui email sesuai dengan Syarat dan Ketentuan;</li>
                            <li class="policy-content">Kesehatan dan/atau kebugaran;</li>
                            <li class="policy-content">Biometrik;</li>
                            <li class="policy-content">Lokasi; dan/atau</li>
                            <li class="policy-content">Informasi lain yang diperlukan sehubungan dengan penggunaan Ulin Mahoni.</li>
                        </ol>
                        
                        <p class="policy-content">Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa di Ulin Mahoni harus berusia minimal 18 (delapan belas) tahun atau sudah menikah atau telah memenuhi persyaratan yang diakui secara hukum di Negara Kesatuan Republik Indonesia, termasuk dalam kategori dewasa dan cakap atau bebas dari undang-undang perlindungan anak. Jika Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa adalah anak di bawah umur dan/atau penyandang disabilitas, maka KAP berhak untuk mengakhiri, menangguhkan, membekukan, dan/atau menutup baik sementara maupun permanen serta menghapus sebagian atau seluruh akun di Ulin Mahoni tanpa pemberitahuan terlebih dahulu, kecuali Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa memberikan persetujuan tertulis yang tegas dari orang tua dan/atau wali yang sah melalui Ulin Mahoni.</p>
                        
                        <p class="policy-content">Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa dapat memilih untuk memberikan Data Pribadi dan/atau Informasi kepada Ulin Mahoni. Jika Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa memilih untuk tidak memberikan Data Pribadi dan/atau Informasi, maka KAP tidak akan dapat memberikan layanan dan/atau menanggapi permintaan yang diajukan oleh Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa melalui Ulin Mahoni.</p>
                        
                        <p class="policy-content">KAP berasumsi bahwa Data Pribadi dan/atau Informasi yang diberikan oleh Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa, termasuk setiap perubahannya, baik yang ada saat ini maupun yang akan datang, adalah benar, sah, dan akurat. Jika terbukti sebaliknya, KAP tidak bertanggung jawab atas hal tersebut dan Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa melepaskan KAP dari segala tuntutan dan/atau gugatan dalam bentuk apapun dan/atau dari pihak manapun yang dirugikan.</p>
                    </div>

                    <div class="policy-section">
                        <h2>C. Penggunaan Data Pribadi</h2>
                        <p class="policy-content">KAP akan menggunakan Data Pribadi dan/atau Informasi baik sebagian maupun seluruhnya untuk:</p>
                        <ol class="policy-list">
                            <li class="policy-content">Pendaftaran atau pembuatan akun;</li>    
                            <li class="policy-content">Menggunakan akun;</li>
                            <li class="policy-content">Memperbarui akun;</li>
                            <li class="policy-content">Melakukan pemesanan, pembayaran Properti, dan pembayaran uang muka;</li>
                            <li class="policy-content">Menghubungi Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa terkait pemberian informasi dan/atau penggunaan promosi dan/atau layanan di Ulin Mahoni;</li>
                            <li class="policy-content">Memberikan konfirmasi atas pertanyaan yang diajukan oleh Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa kepada KAP;</li>
                            <li class="policy-content">Menanggapi, menyetujui dan/atau menolak permintaan dari Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa kepada KAP;</li>
                            <li class="policy-content">Melakukan penyesuaian fitur, pemeliharaan, pengembangan dan/atau perbaikan pada Ulin Mahoni;</li>
                            <li class="policy-content">Mempublikasikan komentar dan/atau penilaian yang diberikan oleh Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa dalam bentuk apapun melalui Ulin Mahoni;</li>
                            <li class="policy-content">Melakukan survei tingkat kepuasan Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa dalam menggunakan Ulin Mahoni;</li>
                            <li class="policy-content">Meningkatkan layanan Ulin Mahoni;</li>
                            <li class="policy-content">Menerapkan Syarat dan Ketentuan;</li>
                            <li class="policy-content">Pemantauan dan/atau penyelidikan transaksi mencurigakan atau transaksi yang diduga mengandung unsur penipuan atau pelanggaran hukum;</li>
                            <li class="policy-content">Melakukan penyelesaian masalah, keluhan dan/atau sengketa;</li>
                            <li class="policy-content">Memfasilitasi dan/atau melaksanakan segala bentuk verifikasi yang dianggap perlu oleh KAP sebelum KAP dapat mendaftarkan Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa, termasuk untuk KYC (Know Your Customer) dan/atau penilaian kredit (jika diperlukan);</li>
                            <li class="policy-content">Memberikan pemberitahuan kepada Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa mengenai pembaruan dan/atau perubahan layanan Ulin Mahoni;</li>
                            <li class="policy-content">Melakukan pemeliharaan, pengembangan, pengujian, peningkatan dan/atau personalisasi Ulin Mahoni untuk memenuhi kebutuhan dan preferensi Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa, termasuk mengaktifkan fitur untuk mempersonalisasi akun, seperti daftar Properti, lama menginap di Properti dan/atau kriteria Properti;</li>
                            <li class="policy-content">Memantau dan/atau menganalisis aktivitas, perilaku, dan data demografis Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa termasuk kebiasaan dan penggunaan berbagai layanan yang tersedia di Ulin Mahoni;</li>
                            <li class="policy-content">Kepentingan pemasaran, yaitu mode komunikasi, informasi pemasaran, materi promosi dari Properti dan/atau afiliasinya;</li>
                            <li class="policy-content">Menawarkan atau menyediakan layanan dari afiliasi atau mitra KAP; dan/atau</li>
                            <li class="policy-content">Tujuan lain yang akan diberitahukan kepada Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa pada saat pengumpulan dan/atau penggunaan Data Pribadi dan/atau Informasi, jika diwajibkan oleh peraturan perundang-undangan.</li>
                        </ol>
                        
                        <p class="policy-content">Selain tujuan-tujuan yang disebutkan di atas, KAP dan/atau afiliasi atau mitra KAP juga dapat menggunakan Data Pribadi dan/atau Informasi untuk tujuan termasuk namun tidak terbatas pada pemasaran Properti, produk baru, penawaran khusus, buletin, survei, atau informasi lain yang akan dikirimkan KAP melalui media apa pun dan dalam bentuk apa pun (seperti: poster, infografis, teks) dan tawaran KAP kepada Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa. Dalam hal ini, Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa dapat memilih untuk tidak berpartisipasi, berhenti berlangganan, atau menggunakan pemasaran apa pun yang dikirim KAP dengan mengikuti pedoman atau petunjuk berhenti berlangganan yang ditetapkan oleh KAP. KAP juga dapat menggunakan Data Pribadi dan/atau Informasi sebagai pertimbangan KAP dalam melakukan riset pasar di mana KAP akan terus memperhatikan dan menghormati Data Pribadi dan/atau Informasi yang diberikan oleh Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa, oleh karena itu KAP akan menyajikan dalam bentuk rincian anonim dan hanya akan digunakan untuk tujuan statistik. Selanjutnya, KAP dapat membuat, menggunakan, melisensikan, atau mengungkapkan data agregat seperti data statistik dan/atau demografis untuk tujuan apa pun. Data agregat mungkin berasal dari Data Pribadi dan/atau Informasi tetapi tidak akan dianggap sebagai Data Pribadi dan/atau Informasi karena data ini tidak akan secara langsung atau tidak langsung mengungkap identitas Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa karena KAP akan berusaha memastikan:</p>
                        
                        <ol class="policy-list">
                            <li class="policy-content">semua pengenal telah dihapus sedemikian rupa sehingga data yang akan digunakan KAP, baik sendiri maupun bersama dengan data lain yang tersedia, tidak dapat dikaitkan atau dihubungkan dengan atau tidak dapat mengidentifikasi orang mana pun; dan/atau</li>
                            <li class="policy-content">Data tersebut kemudian akan digabungkan dengan data sejenis sehingga data asli menjadi bagian dari kumpulan data yang lebih besar.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>D. Keamanan dan Penyimpanan Data Pribadi</h2>
                        <ol class="policy-list">
                            <li class="policy-content">Kantor Akuntan Publik (KAP) berkomitmen kuat untuk melindungi dan memastikan keamanan semua Data Pribadi dan/atau Informasi. KAP akan menerapkan langkah-langkah pencegahan (prosedur fisik, elektronik, dan manajerial) terhadap penggunaan Data Pribadi dan/atau Informasi oleh pihak yang tidak berwenang.</li>
                            <li class="policy-content">Untuk memastikan keamanan Data Pribadi dan/atau Informasi, KAP menginformasikan kepada semua karyawan KAP tentang pedoman privasi dan keamanan serta secara ketat menegakkan perlindungan privasi di perusahaan.</li>
                            <li class="policy-content">KAP hanya menyimpan Data Pribadi dan/atau Informasi selama akun Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa aktif atau untuk jangka waktu yang disyaratkan oleh KAP sebagaimana diatur dalam Kebijakan Privasi dan/atau peraturan perundang-undangan.</li>
                            <li class="policy-content">KAP terlebih dahulu dengan cermat memeriksa apakah perlu menyimpan Data Pribadi dan/atau Informasi yang dikumpulkan, dan jika penyimpanan diperlukan, KAP akan menyimpan Data Pribadi dan/atau Informasi untuk jangka waktu tersingkat yang diizinkan oleh hukum.</li>
                            <li class="policy-content">KAP diizinkan, atas kebijakannya sendiri, untuk menyimpan Data Pribadi dan Informasi dengan pihak ketiga yang ditunjuk oleh KAP, tetapi dengan tetap memperhatikan keamanan Data Pribadi dan/atau Informasi tersebut. Dalam hal ini, Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa menyetujui hal ini.</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa bertanggung jawab atas keamanan dan mitigasi akun pribadi termasuk namun tidak terbatas pada pembuatan kata sandi yang kuat, memelihara kerahasiaan kata sandi, dan membatasi akses ke Ulin Mahoni.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>E. Koreksi dan Pembaruan Data Pribadi</h2>
                        <ol class="policy-list">
                            <li class="policy-content">Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa dapat memperbaiki dan/atau memperbarui Data Pribadi dan/atau Informasi mereka melalui Ulin Mahoni. Selain melalui Ulin Mahoni, Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa dapat memperbaiki dan/atau memperbarui Data Pribadi dan/atau Informasi mereka dengan mengajukan permintaan kepada Kantor Akuntan Publik melalui email ulinmahoni@gmail.com.</li>
                            <li class="policy-content">KAP akan memverifikasi koreksi dan/atau pembaruan Data Pribadi dan/atau Informasi kepada Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa melalui email ulinmahoni@gmail.com.</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa harus memastikan bahwa Data Pribadi dan/atau Informasi mereka benar, sah, dan akurat. Jika terdapat kesalahan dan/atau perbedaan dalam Data Pribadi dan/atau Informasi, Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa harus segera memberitahukan kepada Kantor Akuntan Publik.</li>
                            <li class="policy-content">Jika ada biaya yang mungkin timbul sebagai akibat dari koreksi ini, Anda akan membayar biaya tersebut untuk mengakses Data Pribadi, yang akan kami beritahukan terlebih dahulu untuk mendapatkan persetujuan Anda.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>F. Distribusi, Pengalihan, dan/atau Pengungkapan Data Pribadi dan/atau Informasi</h2>
                        <p class="policy-content">KAP menyatakan dan menjamin bahwa KAP tidak akan menjual, mendistribusikan, mengalihkan dan/atau mengungkapkan Data Pribadi dan/atau Informasi kepada pihak manapun, kecuali dalam hal-hal berikut:</p>
                        <ol class="policy-list">
                            <li class="policy-content">Ada permintaan dari lembaga pemerintah, termasuk namun tidak terbatas pada Kepolisian, Kejaksaan dan/atau Lembaga Peradilan untuk keperluan proses penegakan hukum, pemenuhan kewajiban dan/atau persyaratan di hadapan hukum;</li>
                            <li class="policy-content">Jika diperlukan oleh pihak yang bekerja sama dengan KAP sebagai mitra untuk pelaksanaan pesanan, proses pembayaran, transaksi, pengiriman surat, surel, SMS atau media komunikasi lainnya, analisis data, layanan bantuan, pemenuhan hasil pencarian, peningkatan sistem keamanan dan privasi serta pemeliharaan, perbaikan dan/atau pengembangan Ulin Mahoni;</li>
                            <li class="policy-content">Penyediaan informasi yang relevan kepada anak perusahaan dan/atau afiliasi KAP, konsultan bisnis, konsultan pemasaran, bank dan/atau lembaga pembiayaan non-bank, serta penyedia layanan pembayaran untuk kepentingan bisnis KAP; dan/atau</li>
                            <li class="policy-content">Jika terjadi pengalihan kepemilikan dari KAP kepada pihak lain.</li>
                        </ol>
                        <p class="policy-content mt-4">Dengan ini, Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa menyetujui Tindakan KAP untuk mendistribusikan, mengalihkan dan/atau mengungkapkan Data Pribadi dan/atau Informasi sebagaimana disebutkan di atas.</p>
                    </div>

                    <div class="policy-section">
                        <h2>G. Penghapusan dan Pemusnahan Data Pribadi</h2>
                        <ol class="policy-list">
                            <li class="policy-content">Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa dapat meminta KAP untuk menghapus Data Pribadi dan Informasi dalam aplikasi Ulin Mahoni dengan menggunakan fitur "Hapus Akun" pada profil akun atau mengirimkan permintaan tertulis kepada KAP melalui email ulinmahoni@gmail.com.</li>
                            <li class="policy-content">KAP, atas kebijakannya sendiri, berhak untuk menghapus dan/atau memusnahkan Data Pribadi dan/atau Informasi yang dimiliki oleh Ulin Mahoni sesuai dengan peraturan perundang-undangan yang berlaku.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>H. Cookies</h2>
                        <p class="policy-content">KAP akan menggunakan cookies untuk memperoleh dan mengumpulkan Data Pribadi dan/atau Informasi. Cookies adalah file teks kecil yang secara otomatis akan memakan ruang dan mengidentifikasi perangkat komputer, tablet, dan/atau smartphone milik Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa serta berfungsi untuk menyimpan referensi dan konfigurasi sementara Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa mengakses Situs dan/atau menggunakan Ulin Mahoni. Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa dapat memodifikasi browser mereka untuk menerima atau menolak cookies.</p>
                    </div>

                    <div class="policy-section">
                        <h2>I. Pembatasan Tanggung Jawab</h2>
                        <p class="policy-content">Dalam penggunaan Data Pribadi dan/atau Informasi, Kantor Akuntan Publik menerapkan sistem keamanan yang memadai, termasuk enkripsi untuk penggunaan dan pengolahan Data Pribadi dan/atau Informasi. Adapun pembatasan tanggung jawab Kantor Akuntan Publik adalah sebagai berikut:</p>
                        <ol class="policy-list">
                            <li class="policy-content">KAP tidak bertanggung jawab atas pertukaran Data Pribadi dan/atau Informasi yang dilakukan oleh Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa yang berpotensi mengakibatkan kebocoran Data Pribadi dan/atau Informasi. Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa melepaskan tanggung jawab KAP atas segala kerugian yang diderita oleh Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa;</li>
                            <li class="policy-content">KAP tidak bertanggung jawab atas keaslian, kebenaran, keakuratan dan kelengkapan Data dan/atau Informasi yang diberikan oleh Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa kepada KAP;</li>
                            <li class="policy-content">Apabila Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa merupakan anak di bawah umur dan/atau penyandang disabilitas, maka KAP tidak bertanggung jawab atas Data Pribadi atau Informasi yang diberikan. KAP menghimbau agar orang tua atau wali mendampingi anak dan/atau penyandang disabilitas dalam memberikan Data Pribadi dan/atau Informasi;</li>
                            <li class="policy-content">KAP tidak bertanggung jawab atas kebocoran Data Pribadi dan/atau Informasi yang terjadi akibat keadaan kahar (force majeure). Keadaan kahar dalam hal ini meliputi namun tidak terbatas pada: pemogokan; penutupan perusahaan; kerusuhan, kekacauan, serangan teroris dan/atau ancaman; perang; kebakaran; ledakan; bencana alam atau non-alam; wabah atau pandemi; tidak ada atau terganggunya jaringan telekomunikasi, informatika dan/atau listrik; kegagalan sistem atau aplikasi yang disebabkan oleh pihak ketiga di luar wewenang KAP; kegagalan atau tidak berfungsinya sistem dan/atau aplikasi dan/atau jaringan perbankan; ketentuan peraturan perundang-undangan, peraturan pemerintah dan/atau putusan pengadilan. Dalam hal terjadi keadaan kahar, KAP akan memberitahukan kepada Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa baik secara lisan maupun tertulis selambat-lambatnya 14 (empat belas) hari kalender sejak terjadinya keadaan kahar tersebut dan berusaha sebaik-baiknya untuk memenuhi kewajiban sesuai dengan Kebijakan Privasi ini.</li>
                            <li class="policy-content">Ulin Mahoni and its marketing and/or promotional materials may contain links to websites operated by third parties. Therefore, Ulin Mahoni cannot control and cannot accept responsibility for the activities of these third-party websites related to the collection, use, storage, and disclosure of Personal Data and/or Information by such third parties. If Visitors, Owners, Account Holders and/or Renters access a third-party website, then the Visitors, Owners, Account Holders and/or Renters will be subject to the terms and conditions and policies of the third-party website. Therefore, KAP is not responsible for, cannot guarantee and/or cannot ensure the confidentiality, security and/or use of Personal Data and/or Information on third-party websites. Visitors, Owners, Account Holders and/or Renters are required to read the terms and conditions and privacy policies applicable to the third-party website to understand how they collect and use Personal Data and/or Information.</li>
                        </ol>
                    </div>

                    <div class="policy-section">
                        <h2>J. Ganti Rugi dan Pelepasan Hak</h2>
                        <p class="policy-content">Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa menyatakan bahwa mereka tidak akan menuntut dan akan melepaskan KAP termasuk namun tidak terbatas pada Pemegang Saham, Dewan Komisaris, Direksi, karyawan, agen, anak perusahaan dan/atau afiliasi dari tanggung jawab atas segala kerugian material maupun immaterial yang diderita oleh Pengunjung, Pemilik, Pemegang Akun, Penyewa dan/atau pihak ketiga manapun.</p>
                    </div>

                    <div class="policy-section">
                        <h2>K. Bahasa, Hukum yang Berlaku dan Penyelesaian Sengketa</h2>
                        <ol class="policy-list">
                            <li class="policy-content">Kebijakan Privasi ini ditulis dalam bahasa Indonesia dan dapat diterjemahkan ke dalam bahasa lain. Jika terdapat perbedaan penafsiran dan/atau ketidakkonsistenan antara versi Kebijakan Privasi dalam bahasa Indonesia dengan versi terjemahannya, maka versi bahasa Indonesialah yang akan berlaku, sah, dan mengikat.</li>
                            <li class="policy-content">Keabsahan, penafsiran dan pelaksanaan Kebijakan Privasi ini serta segala akibat yang timbul karenanya diatur dan tunduk pada peraturan perundang-undangan yang berlaku di wilayah Negara Kesatuan Republik Indonesia.</li>
                            <li class="policy-content">Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa menyetujui bahwa jika diperlukan, KAP dapat menghubungi mereka melalui email ulinmahoni@gmail.com.</li>
                            <li class="policy-content">Jika ada pertanyaan dan/atau masalah, serta terdapat informasi yang ingin diketahui, maka Pengunjung, Pemilik, Pemegang Akun, dan/atau Penyewa dapat menghubungi KAP melalui kontak di bawah ini:
                                <ul class="list-disc pl-6 mt-2 space-y-1">
                                    <li>Telepon: +62 811-8809-9700</li>
                                    <li>WhatsApp: <a href="https://wa.me/6281188099700" class="text-blue-600 hover:underline">+62 811-8809-9700</a> (Ulin Mahoni)</li>
                                    <li>Email: ulinmahoni@gmail.com</li>
                                </ul>
                                <p class="mt-2">Jika Pengunjung, Pemilik, Pemilik Akun, dan/atau Penyewa menerima informasi dari selain nomor atau email yang tercantum dalam Syarat dan Ketentuan ini yang mengklaim berasal dari KAP, harap berhati-hati dan periksa kembali.</p>
                            </li>
                        </ol>
                    </div>

                    {{-- <div class="pt-6 border-t border-gray-100 mt-8">
                        <p class="text-sm text-gray-500">Terakhir diperbarui: {{ date('d F Y') }}</p>
                    </div> --}}
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    @include('components.homepage.footer')

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
</body>
</html>
