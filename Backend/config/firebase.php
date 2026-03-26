<?php

/**
 * Firebase configuration — reads project ID and credentials path from .env.
 * Used by FirebaseNotificationService to authenticate with FCM v1 API.
 */
return [
    'project_id' => env('FIREBASE_PROJECT_ID'),
    'credentials' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase-credentials.json')),
];
