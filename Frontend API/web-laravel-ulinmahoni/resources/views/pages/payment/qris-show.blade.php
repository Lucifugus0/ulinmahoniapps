<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran QRIS - Ulin Mahoni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    @include('components.homepage.styles')
    <script>if (localStorage.getItem('dark-mode') !== 'false') document.documentElement.classList.add('dark');</script>
</head>
<body>
    @include('components.homepage.header')
    <div class="header-spacer"></div>

    <main class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-lg mx-auto px-4">

            <!-- Loading -->
            <div id="loadingState" class="bg-white rounded-xl shadow-sm p-8 text-center">
                <p class="text-gray-500">Memuat kode QR...</p>
            </div>

            <!-- QR Display -->
            <div id="qrisDisplay" class="hidden bg-white rounded-xl shadow-sm p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-1">Bayar dengan QRIS</h2>
                <p class="text-gray-500 text-sm mb-6">Scan kode QR menggunakan aplikasi pembayaran Anda</p>

                <div class="flex justify-center mb-4">
                    <img id="qrImage" src="" alt="QRIS Code" class="w-64 h-64 border-2 border-gray-200 rounded-lg">
                </div>

                <div class="flex justify-center mb-6">
                    <button id="downloadBtn" class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download QR Code
                    </button>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 text-left space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Jumlah</span>
                        <span class="text-sm font-bold text-gray-900" id="qrisAmount"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Berlaku hingga</span>
                        <span class="text-sm font-medium text-gray-900" id="qrisExpiry"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Sisa waktu</span>
                        <span class="text-sm font-bold" id="qrisCountdown"></span>
                    </div>
                </div>

                <a href="{{ route('bookings.index') }}" class="w-full inline-flex justify-center rounded-lg border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Kembali ke Daftar Booking
                </a>
            </div>

            <!-- No Data / Expired -->
            <div id="noDataState" class="hidden bg-white rounded-xl shadow-sm p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Kode QR Tidak Tersedia</h2>
                <p class="text-gray-500 text-sm mb-6">Kode QR telah kedaluwarsa atau belum dibuat. Silakan kembali ke halaman pembayaran.</p>
                <a href="{{ route('bookings.index') }}" class="w-full inline-flex justify-center rounded-lg border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Kembali ke Daftar Booking
                </a>
            </div>

        </div>
    </main>

    @include('components.homepage.footer')

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const params = new URLSearchParams(window.location.search);
        const orderId = params.get('order_id');
        const storageKey = orderId ? 'qris_data_' + orderId : 'qris_data_latest';

        const loadingState = document.getElementById('loadingState');
        const qrisDisplay  = document.getElementById('qrisDisplay');
        const noDataState  = document.getElementById('noDataState');

        function showNoData() {
            loadingState.classList.add('hidden');
            noDataState.classList.remove('hidden');
        }

        const stored = localStorage.getItem(storageKey);
        if (!stored) { showNoData(); return; }

        let data;
        try { data = JSON.parse(stored); } catch (e) { showNoData(); return; }

        // Expired check
        const expiryDate = data.expiresAt ? new Date(data.expiresAt) : null;
        if (expiryDate && new Date() > expiryDate) {
            localStorage.removeItem(storageKey);
            showNoData();
            return;
        }

        // Set QR image
        const qrImage = document.getElementById('qrImage');
        if (data.qr_content && data.qr_content.startsWith('data:image')) {
            qrImage.src = data.qr_content;
        } else if (data.qr_content) {
            qrImage.src = 'https://quickchart.io/qr?text=' + encodeURIComponent(data.qr_content) + '&size=300';
        } else {
            showNoData();
            return;
        }

        // Amount
        document.getElementById('qrisAmount').textContent = 'Rp ' + parseFloat(data.amount || 0).toLocaleString('id-ID');

        // Expiry display
        if (expiryDate) {
            document.getElementById('qrisExpiry').textContent = expiryDate.toLocaleString('id-ID');
        }

        // Countdown — capped at 30 minutes from page load (matches server expired_at)
        const countdownEl = document.getElementById('qrisCountdown');
        const maxMs = 30 * 60 * 1000;
        const displayExpiryDate = expiryDate
            ? new Date(Math.min(expiryDate.getTime(), Date.now() + maxMs))
            : null;
        let timer;
        function updateCountdown() {
            if (!displayExpiryDate) { countdownEl.textContent = '-'; return; }
            const diff = displayExpiryDate - new Date();
            if (diff <= 0) {
                countdownEl.textContent = 'Kedaluwarsa';
                countdownEl.className = 'text-sm font-bold text-red-600';
                localStorage.removeItem(storageKey);
                clearInterval(timer);
                return;
            }
            const m = Math.floor(diff / 60000);
            const s = Math.floor((diff % 60000) / 1000);
            countdownEl.textContent = m + 'm ' + s + 's';
            countdownEl.className = diff < 60000
                ? 'text-sm font-bold text-red-600'
                : 'text-sm font-bold text-green-600';
        }
        updateCountdown();
        timer = setInterval(updateCountdown, 1000);

        // Download
        document.getElementById('downloadBtn').addEventListener('click', function () {
            const src = qrImage.src;
            if (!src) return;
            if (src.startsWith('data:image')) {
                const a = document.createElement('a');
                a.href = src;
                a.download = 'qris-code.png';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            } else {
                fetch(src)
                    .then(r => r.blob())
                    .then(blob => {
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'qris-code.png';
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        URL.revokeObjectURL(url);
                    })
                    .catch(() => window.open(src, '_blank'));
            }
        });

        loadingState.classList.add('hidden');
        qrisDisplay.classList.remove('hidden');
    });
    </script>
</body>
</html>
