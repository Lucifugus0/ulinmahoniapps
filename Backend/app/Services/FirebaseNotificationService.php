<?php

namespace App\Services;

use App\Models\ChatConversation;
use App\Models\DeviceToken;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * Firebase Cloud Messaging service for sending push notifications.
 * Uses FCM v1 API with OAuth2 JWT authentication.
 * Replicated from Frontend API's FirebaseNotificationService for Backend use.
 */
class FirebaseNotificationService
{
    private ?string $projectId;
    private ?string $credentialsPath;
    private ?string $accessToken = null;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id');
        $this->credentialsPath = config('firebase.credentials');
    }

    /**
     * Send push notification to a specific user (all active devices).
     * Fetches all active device tokens for the user and sends to each.
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): array
    {
        $tokens = $user->activeDeviceTokens()->pluck('token')->toArray();

        if (empty($tokens)) {
            return ['sent' => 0, 'failed' => 0, 'message' => 'No active device tokens'];
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Send the same push notification to multiple users (used by broadcasts).
     * Collects all active device tokens for the given user IDs and sends in one batch.
     * Returns aggregated counts of successful and failed sends.
     */
    public function sendToMultipleUsers(array $userIds, string $title, string $body, array $data = []): array
    {
        if (empty($userIds)) {
            return ['sent' => 0, 'failed' => 0, 'message' => 'No recipients'];
        }

        $tokens = DeviceToken::whereIn('user_id', $userIds)
            ->where('is_active', 1)
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return ['sent' => 0, 'failed' => 0, 'message' => 'No active device tokens for any recipient'];
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Send chat message notification to all participants except the sender.
     * Used by both ChatController (web) and ChatApiController (API) to avoid duplication.
     * Includes conversation_id in data payload for mobile deep-linking.
     */
    public function sendChatNotification(ChatConversation $conversation, User $sender, string $messageText): void
    {
        /** Get all participants except the sender */
        $otherParticipants = $conversation->participants()
            ->where('user_id', '!=', $sender->id)
            ->with('user')
            ->get();

        /** Truncate message body to 200 chars for notification display */
        $truncatedMessage = mb_strlen($messageText) > 200
            ? mb_substr($messageText, 0, 200) . '...'
            : $messageText;

        /** Build sender display name from first_name or name field */
        $senderName = $sender->first_name ?? $sender->name ?? 'Admin';

        foreach ($otherParticipants as $participant) {
            if ($participant->user) {
                $this->sendToUser(
                    $participant->user,
                    $senderName,
                    $truncatedMessage,
                    [
                        'conversation_id' => (string) $conversation->id,
                        'type' => 'chat_message',
                        'sender_name' => $senderName,
                    ]
                );
            }
        }
    }

    /**
     * Send to a single FCM token via FCM v1 API.
     * Returns true on success, false on failure.
     * Auto-deactivates invalid tokens on 400/404 responses.
     */
    public function sendToSingleToken(string $token, string $title, string $body, array $data = []): bool
    {
        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                Log::error('Firebase: Failed to obtain access token');
                return false;
            }

            $client = new Client();
            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

            /** Title/body live in both `data` (for in-app handling) and `notification`
             *  (so the OS displays it natively in background/terminated state, even for
             *  message types the mobile app doesn't have a custom handler for, e.g. broadcast). */
            $messageData = array_merge(
                ['title' => $title, 'body' => $body],
                !empty($data) ? array_map('strval', $data) : []
            );

            $message = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data' => $messageData,
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                            'channel_id' => 'ulin_mahoni_default',
                        ],
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                            ],
                        ],
                    ],
                ],
            ];

            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $message,
            ]);

            return $response->getStatusCode() === 200;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $responseBody = json_decode($e->getResponse()->getBody()->getContents(), true);

            /** 404 = invalid token, 400 = malformed token — deactivate in database */
            if (in_array($statusCode, [400, 404])) {
                Log::info("Firebase: Deactivating invalid token", ['token' => substr($token, 0, 20) . '...']);
                DeviceToken::where('token', $token)->update(['is_active' => false]);
            }

            Log::error('Firebase: FCM send failed', [
                'status' => $statusCode,
                'error' => $responseBody['error']['message'] ?? 'Unknown error',
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Firebase: Unexpected error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send to multiple tokens and track success/failure counts.
     */
    private function sendToTokens(array $tokens, string $title, string $body, array $data): array
    {
        $results = ['sent' => 0, 'failed' => 0];

        foreach ($tokens as $token) {
            if ($this->sendToSingleToken($token, $title, $body, $data)) {
                $results['sent']++;
            } else {
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Get Google OAuth2 access token from service account credentials.
     * Creates a JWT signed with the service account's private key,
     * then exchanges it for an access token via Google's OAuth2 endpoint.
     * Token is cached for the lifetime of this service instance.
     */
    private function getAccessToken(): ?string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        try {
            if (!$this->credentialsPath || !file_exists($this->credentialsPath)) {
                Log::error('Firebase: Credentials file not found', ['path' => $this->credentialsPath]);
                return null;
            }

            $credentials = json_decode(file_get_contents($this->credentialsPath), true);

            if (!$credentials) {
                Log::error('Firebase: Invalid credentials JSON');
                return null;
            }

            /** Create JWT header and payload for Google OAuth2 */
            $now = time();
            $header = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);
            $payload = json_encode([
                'iss' => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
            ]);

            /** Base64url-encode header and payload */
            $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
            $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

            /** Sign with RS256 using service account private key */
            $signatureInput = $base64Header . '.' . $base64Payload;
            openssl_sign($signatureInput, $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256);
            $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

            $jwt = $signatureInput . '.' . $base64Signature;

            /** Exchange JWT for access token at Google OAuth2 endpoint */
            $client = new Client();
            $response = $client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ],
            ]);

            $tokenData = json_decode($response->getBody()->getContents(), true);
            $this->accessToken = $tokenData['access_token'] ?? null;

            return $this->accessToken;
        } catch (\Exception $e) {
            Log::error('Firebase: Token generation failed', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
