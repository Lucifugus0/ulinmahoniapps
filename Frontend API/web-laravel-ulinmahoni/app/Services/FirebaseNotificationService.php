<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

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
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): array
    {
        $tokens = $user->activeDeviceTokens()->pluck('token')->toArray();

        if (empty($tokens)) {
            return ['sent' => 0, 'failed' => 0, 'message' => 'No active device tokens'];
        }

        return $this->sendToTokens($tokens, $title, $body, $data, $user->id);
    }

    /**
     * Send push notification to multiple users.
     */
    public function sendToMultipleUsers(array $userIds, string $title, string $body, array $data = []): array
    {
        $tokens = DeviceToken::whereIn('user_id', $userIds)
            ->where('is_active', true)
            ->get();

        $results = ['sent' => 0, 'failed' => 0];

        foreach ($tokens as $deviceToken) {
            $result = $this->sendToSingleToken($deviceToken->token, $title, $body, $data);
            if ($result) {
                $results['sent']++;
            } else {
                $results['failed']++;
                $deviceToken->update(['is_active' => false]);
            }
        }

        return $results;
    }

    /**
     * Send push notification to all admin users.
     */
    public function sendToAdmins(string $title, string $body, array $data = []): array
    {
        $adminIds = User::where('is_admin', 1)
            ->whereHas('activeDeviceTokens')
            ->pluck('id')
            ->toArray();

        if (empty($adminIds)) {
            return ['sent' => 0, 'failed' => 0, 'message' => 'No admin device tokens'];
        }

        return $this->sendToMultipleUsers($adminIds, $title, $body, $data);
    }

    /**
     * Send to a single FCM token.
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

            $message = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                ],
            ];

            if (!empty($data)) {
                // FCM data values must be strings
                $message['message']['data'] = array_map('strval', $data);
            }

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

            // 404 = invalid token, 400 = malformed token → deactivate
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
     * Send to multiple tokens and track results.
     */
    private function sendToTokens(array $tokens, string $title, string $body, array $data, int $userId): array
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

            // Create JWT
            $now = time();
            $header = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);
            $payload = json_encode([
                'iss' => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
            ]);

            $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
            $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

            $signatureInput = $base64Header . '.' . $base64Payload;
            openssl_sign($signatureInput, $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256);
            $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

            $jwt = $signatureInput . '.' . $base64Signature;

            // Exchange JWT for access token
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
