<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <title>{{ $CRM_ISS->nilai ?? 'Ulin Mahoni' }}</title>


        <!-- style -->
        <!-- Custom styles served from public/css/ -->
        <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        
        <!-- Styles -->
        @livewireStyles
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.jqueryui.min.css">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <!-- Dark mode: default to dark — only disable if explicitly set to false -->
        <script>
            if (localStorage.getItem('dark-mode') !== 'false') {
                document.documentElement.classList.add('dark');
            }
        </script>
        <style>
            /* Chrome, Safari, Edge, Opera */
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
            }
        </style>
    </head>
    <body
        class="font-inter antialiased bg-slate-100 dark:bg-gray-900 text-slate-600 dark:text-gray-300"
        :class="{ 'sidebar-expanded': sidebarExpanded }"
        x-data="{ sidebarOpen: false, sidebarExpanded: localStorage.getItem('sidebar-expanded') == 'true' }"
        x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebar-expanded', value))"    
    >
        @include('sweetalert::alert')
        <script>
            if (localStorage.getItem('sidebar-expanded') == 'true') {
                document.querySelector('body').classList.add('sidebar-expanded');
            } else {
                document.querySelector('body').classList.remove('sidebar-expanded');
            }
            // <!-- Default to dark mode -->
            if (localStorage.getItem('dark-mode') !== 'false') {
                document.documentElement.classList.add('dark');
            }
        </script>

        <!-- Page wrapper -->
        <div class="flex h-screen overflow-hidden">

            <x-app.sidebar />

            <!-- Content area -->
            <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden @if($attributes['background']){{ $attributes['background'] }}@endif" x-ref="contentarea">

                <x-app.header />

                <main>
                    {{ $slot }}
                </main>

            </div>

        </div>

        @livewireScripts
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        

        <script>
            function formatDate(date){
                let d = new Date(date);
                const formattedDate = d.getFullYear() + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2);
                return formattedDate;
            }

            function formatCurrency(num) {
                var num_parts = num.toString().split(".");
                num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                return 'IDR ' + num_parts.join(".");
            }
            function divider(num) {
                var num_parts = num.toString().split(".");
                num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                return ' ' + num_parts.join(".");
            }

            const loadFile = function (event) {
                const output = document.getElementById("output");
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function () {
                    URL.revokeObjectURL(output.src); // free memory
                };
            };

            const loadFileMultiple = function (event, idView) {
                const output = document.getElementById(idView);
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function () {
                    URL.revokeObjectURL(output.src); // free memory
                };
            };
        </script>
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
    </body>
</html>
