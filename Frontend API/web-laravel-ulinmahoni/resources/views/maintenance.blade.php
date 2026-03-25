{{--
    Maintenance Mode Landing Page
    ==============================
    Standalone full-page maintenance page with no layout dependencies.
    Uses Tailwind CSS via CDN so it works without Vite build.
    Displays bilingual (English / Indonesian) messaging with Ulin Mahoni branding.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - Ulin Mahoni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="/images/frist_icon.png" type="image/png">
    <style>
        /* Subtle floating animation for the maintenance icon */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        /* Slow rotation for the gear icon */
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .spin-slow {
            animation: spin-slow 8s linear infinite;
        }

        /* Pulsing dot animation for the "working" indicator */
        @keyframes pulse-dot {
            0%, 80%, 100% { opacity: 0.3; transform: scale(0.8); }
            40% { opacity: 1; transform: scale(1); }
        }
        .pulse-dot:nth-child(1) { animation: pulse-dot 1.4s ease-in-out infinite; }
        .pulse-dot:nth-child(2) { animation: pulse-dot 1.4s ease-in-out 0.2s infinite; }
        .pulse-dot:nth-child(3) { animation: pulse-dot 1.4s ease-in-out 0.4s infinite; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 relative overflow-hidden">

    {{-- Background decorative shapes --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-200 rounded-full mix-blend-multiply opacity-30 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-indigo-200 rounded-full mix-blend-multiply opacity-30 blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-purple-100 rounded-full mix-blend-multiply opacity-20 blur-3xl"></div>
    </div>

    {{-- Main content card --}}
    <div class="relative z-10 max-w-xl w-full mx-4 text-center">

        {{-- Logo --}}
        <div class="mb-8">
            <img src="/images/frist_icon.png" alt="Ulin Mahoni" class="h-16 mx-auto">
        </div>

        {{-- Maintenance illustration: gear + wrench SVG --}}
        <div class="flex items-center justify-center mb-10 float-animation">
            <svg class="w-32 h-32 text-indigo-500" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                {{-- Large gear --}}
                <g class="spin-slow" style="transform-origin: 55px 55px;">
                    <path d="M55 20 L58 28 C60 28 62 28.5 64 29 L69 22 L74 26 L71 34 C73 35.5 74.5 37 76 39 L84 36 L88 41 L81 46 C81.5 48 82 50 82 52 L90 55 L90 61 L82 64 C82 66 81.5 68 81 70 L88 75 L84 80 L76 77 C74.5 79 73 80.5 71 82 L74 90 L69 94 L64 87 C62 87.5 60 88 58 88 L55 96 L49 96 L46 88 C44 88 42 87.5 40 87 L35 94 L30 90 L33 82 C31 80.5 29.5 79 28 77 L20 80 L16 75 L23 70 C22.5 68 22 66 22 64 L14 61 L14 55 L22 52 C22 50 22.5 48 23 46 L16 41 L20 36 L28 39 C29.5 37 31 35.5 33 34 L30 26 L35 22 L40 29 C42 28.5 44 28 46 28 L49 20 Z"
                        fill="currentColor" opacity="0.15" stroke="currentColor" stroke-width="1.5"/>
                    <circle cx="55" cy="58" r="16" fill="white" stroke="currentColor" stroke-width="2"/>
                    <circle cx="55" cy="58" r="8" fill="currentColor" opacity="0.2"/>
                </g>
                {{-- Wrench --}}
                <g transform="translate(65, 60) rotate(45)">
                    <path d="M0 -4 C6 -10 16 -10 22 -4 L18 0 L22 4 C16 10 6 10 0 4 L0 4 L-24 28 C-26 30 -30 30 -32 28 C-34 26 -34 22 -32 20 L-8 -4 Z"
                        fill="currentColor" opacity="0.6" stroke="currentColor" stroke-width="1"/>
                </g>
            </svg>
        </div>

        {{-- Card with text content --}}
        <div class="bg-white/70 backdrop-blur-lg rounded-2xl shadow-xl shadow-indigo-100/50 px-8 py-10 sm:px-12 border border-white/80">

            {{-- English title --}}
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-2">
                We're Under Maintenance
            </h1>
            {{-- Indonesian title --}}
            <p class="text-lg sm:text-xl text-indigo-600 font-semibold mb-6">
                Sedang Dalam Perbaikan
            </p>

            <div class="w-16 h-0.5 bg-indigo-300 mx-auto mb-6 rounded-full"></div>

            {{-- English subtitle --}}
            <p class="text-gray-600 mb-3 leading-relaxed">
                We're working hard to improve your experience. Please check back soon.
            </p>
            {{-- Indonesian subtitle --}}
            <p class="text-gray-500 mb-8 leading-relaxed text-sm">
                Kami sedang berusaha keras untuk meningkatkan pengalaman Anda. Silakan kembali nanti.
            </p>

            {{-- Working indicator dots --}}
            <div class="flex items-center justify-center gap-1.5 mb-8">
                <span class="pulse-dot w-2 h-2 bg-indigo-400 rounded-full inline-block"></span>
                <span class="pulse-dot w-2 h-2 bg-indigo-400 rounded-full inline-block"></span>
                <span class="pulse-dot w-2 h-2 bg-indigo-400 rounded-full inline-block"></span>
            </div>

            {{-- Estimated return message --}}
            <div class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-700 text-sm font-medium px-5 py-2.5 rounded-full">
                {{-- Clock icon --}}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 6v6l4 2"/>
                </svg>
                <span>We'll be back shortly / Kami akan segera kembali</span>
            </div>
        </div>

        {{-- Footer --}}
        <p class="mt-8 text-xs text-gray-400">
            &copy; {{ date('Y') }} Ulin Mahoni. All rights reserved.
        </p>
    </div>
</body>
</html>
