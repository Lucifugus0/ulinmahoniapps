/* Firebase Messaging Service Worker
 * Handles background push notifications when the browser tab is not focused.
 * Must be in public/ root — service workers scope to their directory.
 * Server sends data-only messages (no 'notification' key), so we must
 * explicitly call showNotification to display the push. */

importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js');

/* Listen for config message from the main page to initialize Firebase */
let messagingInitialized = false;

self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'FIREBASE_CONFIG' && !messagingInitialized) {
        firebase.initializeApp(event.data.config);
        const messaging = firebase.messaging();

        /* Handle background messages — server sends data-only payloads
         * so we must explicitly show the notification */
        messaging.onBackgroundMessage((payload) => {
            const data = payload.data || {};
            const title = data.title || 'Ulin Mahoni';
            const options = {
                body: data.body || '',
                icon: '/favicon.ico',
                data: data,
            };
            self.registration.showNotification(title, options);
        });

        messagingInitialized = true;
    }
});

/* Handle notification click — open the app or focus existing tab */
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            if (clientList.length > 0) {
                return clientList[0].focus();
            }
            return clients.openWindow('/');
        })
    );
});
