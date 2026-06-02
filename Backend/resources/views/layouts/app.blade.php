<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />


    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder@1.13.0/dist/Control.Geocoder.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

    <script>
        // <!-- Default to dark mode — only switch to light if explicitly set -->
        if (localStorage.getItem('dark-mode') === 'false') {
            document.querySelector('html').classList.remove('dark');
            document.querySelector('html').style.colorScheme = 'light';
        } else {
            document.querySelector('html').classList.add('dark');
            document.querySelector('html').style.colorScheme = 'dark';
        }
    </script>
</head>

<body class="font-inter antialiased bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400"
    :class="{ 'sidebar-expanded': sidebarExpanded }" x-data="{ sidebarOpen: false, sidebarExpanded: localStorage.getItem('sidebar-expanded') == 'true' }" x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebar-expanded', value))">

    <script>
        if (localStorage.getItem('sidebar-expanded') == 'true') {
            document.querySelector('body').classList.add('sidebar-expanded');
        } else {
            document.querySelector('body').classList.remove('sidebar-expanded');
        }

        function formatRupiah(input) {
            let value = input.value.replace(/[^,\d]/g, '').toString();
            let split = value.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/g);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            input.value = rupiah ? 'IDR ' + rupiah : '';
        }

        function formatPrice(price) {
            // Convert to a number and format it
            return Number(price).toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }
    </script>

    <!-- Page wrapper -->
    <div class="flex h-[100dvh] overflow-hidden">

        <x-app.sidebar :variant="$attributes['sidebarVariant']" />

        <!-- Content area -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden @if ($attributes['background']) {{ $attributes['background'] }} @endif"
            x-ref="contentarea">

            <x-app.header :variant="$attributes['headerVariant']" />

            <main class="grow">
                {{ $slot }}
            </main>

        </div>

    </div>

    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    @livewireScriptConfig

    <!-- Firebase Web Push Notifications — CDN-loaded, only for authenticated users -->
    @auth
    <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js"></script>
    <script>
        /* Initialize Firebase and register for web push notifications.
         * Requests permission on page load, gets FCM token, and sends
         * it to the server for storage in device_tokens table. */
        (function() {
            if (!('serviceWorker' in navigator) || !('Notification' in window)) return;

            var firebaseConfig = {
                apiKey: "{{ config('firebase.web.api_key') }}",
                authDomain: "{{ config('firebase.web.auth_domain') }}",
                projectId: "{{ config('firebase.web.project_id') }}",
                storageBucket: "{{ config('firebase.web.storage_bucket') }}",
                messagingSenderId: "{{ config('firebase.web.messaging_sender_id') }}",
                appId: "{{ config('firebase.web.app_id') }}",
            };
            var vapidKey = "{{ config('firebase.web.vapid_key') }}";

            firebase.initializeApp(firebaseConfig);
            var messaging = firebase.messaging();

            /* Register service worker and pass Firebase config to it */
            navigator.serviceWorker.register('/firebase-messaging-sw.js')
                .then(function(registration) {
                    registration.active && registration.active.postMessage({
                        type: 'FIREBASE_CONFIG', config: firebaseConfig
                    });
                    navigator.serviceWorker.ready.then(function(reg) {
                        reg.active && reg.active.postMessage({
                            type: 'FIREBASE_CONFIG', config: firebaseConfig
                        });
                    });

                    /* Request permission if not yet decided */
                    if (Notification.permission === 'default') {
                        Notification.requestPermission().then(function(permission) {
                            if (permission === 'granted') registerToken(registration);
                        });
                    } else if (Notification.permission === 'granted') {
                        registerToken(registration);
                    }
                })
                /* Web push is non-critical. If the SW script 404s (e.g. not yet
                   copied to a webroot), is blocked, or the browser refuses it,
                   degrade silently — log to console only. Without this .catch()
                   the rejection bubbles to the global `unhandledrejection`
                   handler and pops a blocking "Kesalahan Sistem" modal over
                   the whole admin UI. */
                .catch(function(err) {
                    console.warn('FCM service worker registration skipped:', err && err.message ? err.message : err);
                });

            /* Get FCM token and send to server */
            function registerToken(registration) {
                messaging.getToken({ vapidKey: vapidKey, serviceWorkerRegistration: registration })
                    .then(function(token) {
                        if (!token) return;
                        /* Skip if token already sent this session */
                        if (sessionStorage.getItem('fcm_token') === token) return;

                        fetch('/web/device-token', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                token: token,
                                device_name: navigator.userAgent.substring(0, 100),
                            }),
                        }).then(function() {
                            sessionStorage.setItem('fcm_token', token);
                        });
                    })
                    /* Same rationale as the SW .catch() above — a getToken
                       failure (permission, network, missing VAPID) must not
                       escalate into a blocking system-error modal. */
                    .catch(function(err) {
                        console.warn('FCM getToken skipped:', err && err.message ? err.message : err);
                    });
            }

            /* Handle foreground messages — show browser notification */
            messaging.onMessage(function(payload) {
                var data = payload.data || {};
                if (Notification.permission === 'granted') {
                    new Notification(data.title || 'Ulin Mahoni', {
                        body: data.body || '',
                        icon: '/favicon.ico',
                    });
                }
            });
        })();
    </script>
    @endauth

    @yield('js-page')
    @stack('scripts')
</body>

</html>
