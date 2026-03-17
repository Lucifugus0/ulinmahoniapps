<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header dengan gradient -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 p-8 text-white text-center">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-white/20 rounded-full mb-4 backdrop-blur-sm">
                    <i class="fas fa-lock text-5xl"></i>
                </div>
                <h1 class="text-4xl font-bold mb-2">403</h1>
                <p class="text-xl font-medium">Akses Ditolak</p>
            </div>

            <!-- Content -->
            <div class="p-8">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-3">
                        Maaf, Anda tidak memiliki akses ke halaman ini
                    </h2>
                    <p class="text-gray-600 mb-4">
                        {{ $exception->getMessage() ?: 'Anda tidak memiliki izin untuk mengakses halaman yang Anda minta.' }}
                    </p>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Mengapa ini terjadi?</p>
                            <ul class="list-disc list-inside space-y-1 text-blue-700">
                                <li>Anda tidak memiliki role atau permission yang sesuai</li>
                                <li>Akses Anda telah dibatasi oleh administrator</li>
                                <li>Halaman ini memerlukan level akses yang lebih tinggi</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-150 shadow-md hover:shadow-lg">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Dashboard
                    </a>
                    <button onclick="window.history.back()"
                            class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-all duration-150">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Halaman Sebelumnya
                    </button>
                </div>

                <!-- Contact Admin -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        Butuh akses?
                        <a href="mailto:ho-administrator@ulinmahoni.com" class="text-blue-600 hover:text-blue-800 font-medium">
                            Hubungi Administrator
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-500">
                Error Code: 403 - Forbidden Access
            </p>
        </div>
    </div>
</body>
</html>
