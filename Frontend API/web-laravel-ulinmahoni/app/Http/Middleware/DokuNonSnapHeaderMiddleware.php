<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DokuNonSnapHeaderMiddleware
{
    /**
     * Handle an incoming request and validate DOKU headers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Validate required DOKU headers
        $requiredHeaders = [
            // 'X-TIMESTAMP',
            // 'X-SIGNATURE', 
            // 'X-PARTNER-ID',
            // 'X-EXTERNAL-ID',
            // 'CHANNEL-ID',
            // 'Authorization'
            'Client-Id',
            'Request-Id',
            'Request-Timestamp',
            'Signature'
        ];

        $missingHeaders = [];
        
        foreach ($requiredHeaders as $header) {
            if (!$request->header($header)) {
                $missingHeaders[] = $header;
            }
        }

        if (!empty($missingHeaders)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing required headers',
                'errors' => [
                    'headers' => $missingHeaders
                ]
            ], 422);
        }

        // Log DOKU request for security monitoring
        // \Log::info('DOKU Request Received', [
        //     'headers' => [
        //         'x-timestamp' => $request->header('X-TIMESTAMP'),
        //         'x-partner-id' => $request->header('X-PARTNER-ID'),
        //         'x-external-id' => $request->header('X-EXTERNAL-ID'),
        //         'channel-id' => $request->header('CHANNEL-ID'),
        //         'signature_present' => $request->header('X-SIGNATURE') ? 'yes' : 'no',
        //         'authorization_present' => $request->header('Authorization') ? 'yes' : 'no',
        //     ],
        //     'method' => $request->getMethod(),
        //     'url' => $request->getPathInfo(),
        //     'timestamp' => now()->toISOString()
        // ]);

        // Validate Bearer token authorization
        // if (!$this->validateAuthorization($request)) {
        //     \Log::warning('DOKU Authorization Validation Failed', [
        //         'client-id' => $request->header('Client-Id'),
        //         'request-id' => $request->header('Request-Id'),
        //         'timestamp' => now()->toISOString()
        //     ]);

        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Unauthorized',
        //         'responseCode' => '4014701',
        //         'responseMessage' => 'Unauthorized. Invalid Token (B2B)'
        //     ], 401);
        // }

        // Validate DOKU signature
        // if (!$this->validateDokuSignature($request)) {
        //     \Log::warning('DOKU Signature Validation Failed', [
        //         'x-partner-id' => $request->header('X-PARTNER-ID'),
        //         'x-external-id' => $request->header('X-EXTERNAL-ID'),
        //         'timestamp' => now()->toISOString()
        //     ]);

        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Invalid signature',
        //         'responseCode' => '4017300',
        //         'responseMessage' => 'Unauthorized. Signature Not Match'
        //     ], 401);
        // }

        // Validate timestamp (prevent replay attacks - max 5 minutes difference)
        if (!$this->validateTimestamp($request->header('Request-Timestamp'))) {
            \Log::warning('DOKU Timestamp Validation Failed', [
                'request-timestamp' => $request->header('Request-Timestamp'),
                'current_time' => now()->toISOString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid timestamp',
                'responseCode' => '4017301',
                'responseMessage' => 'Invalid Timestamp'
            ], 401);
        }

        return $next($request);
    }

    /**
     * Validate Bearer token authorization
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function validateAuthorization(Request $request): bool
    {
        $authHeader = $request->header('Authorization');

        if (empty($authHeader)) {
            return false;
        }

        // Check if Authorization header starts with "Bearer "
        if (!preg_match('/^Bearer\s+(.+)$/i', $authHeader, $matches)) {
            \Log::warning('Invalid Authorization format', [
                'format' => 'Authorization header must be in Bearer token format'
            ]);
            return false;
        }

        $token = $matches[1];

        // Validate token is not empty
        if (empty($token)) {
            return false;
        }

        // TODO: Add additional token validation logic here
        // For example, validate against stored tokens or JWT verification
        // For now, we just validate the Bearer format

        return true;
    }

    /**
     * Validate DOKU signature using HMAC SHA256
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function validateDokuSignature(Request $request): bool
    {
        $signature = $request->header('X-SIGNATURE');

        if (empty($signature)) {
            return false;
        }

        try {
            $clientId = config('services.doku.client_id');
            $secretKey = config('services.doku.secret_key');

            if (empty($clientId) || empty($secretKey)) {
                \Log::error('DOKU credentials not configured');
                return false;
            }

            // Get request data
            $httpMethod = strtoupper($request->getMethod());
            $endpointUrl = $request->getPathInfo();
            $timestamp = $request->header('X-TIMESTAMP');
            $requestBody = $request->getContent();

            // Minify JSON body (remove whitespace) - JSON.stringify(jsonObject, null, 0)
            if (!empty($requestBody)) {
                $jsonObject = json_decode($requestBody);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $requestBody = json_encode($jsonObject, JSON_UNESCAPED_SLASHES);
                }
            } else {
                $requestBody = '';
            }

            // Create lowercase hex of SHA256 hash of request body
            $hashedBody = strtolower(hash('sha256', $requestBody));

            // Get access token from Bearer header
            $accessToken = $request->bearerToken() ?? '';

            // Build string to sign according to DOKU SNAP specification
            // Format: HTTPMethod:Path:AccessToken:HashedBody:Timestamp
            $stringToSign = sprintf(
                "%s:%s:%s:%s:%s",
                $httpMethod,
                $endpointUrl,
                $accessToken,
                $hashedBody,
                $timestamp
            );

            \Log::info('Signature validation details', [
                'method' => $httpMethod,
                'path' => $endpointUrl,
                'hashed_body' => $hashedBody,
                'timestamp' => $timestamp,
                'string_to_sign' => $stringToSign
            ]);

            // Generate HMAC signature using SHA512 and encode to Base64 (matching CryptoJS.HmacSHA512)
            $signatureBytes = hash_hmac('sha512', $stringToSign, $secretKey, true);
            $calculatedSignature = base64_encode($signatureBytes);

            // Compare signatures (timing-safe comparison)
            $isValid = hash_equals($calculatedSignature, $signature);

            if (!$isValid) {
                \Log::warning('Signature mismatch', [
                    'expected' => $calculatedSignature,
                    'received' => $signature,
                    'string_to_sign' => $stringToSign,
                    'hashed_body' => $hashedBody
                ]);
            }

            return $isValid;

        } catch (\Exception $e) {
            \Log::error('Signature validation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Validate timestamp to prevent replay attacks
     *
     * @param  string|null  $timestamp
     * @return bool
     */
    protected function validateTimestamp(?string $timestamp): bool
    {
        if (empty($timestamp)) {
            return false;
        }

        try {
            // Parse timestamp (format: 2024-01-15T10:30:45+07:00)
            $requestTime = \Carbon\Carbon::parse($timestamp);
            $currentTime = now();

            // Allow maximum 5 minutes difference
            $maxDifference = 5 * 60; // 5 minutes in seconds
            $timeDifference = abs($currentTime->diffInSeconds($requestTime));

            if ($timeDifference > $maxDifference) {
                \Log::warning('Timestamp outside acceptable range', [
                    'request_time' => $requestTime->toISOString(),
                    'current_time' => $currentTime->toISOString(),
                    'difference_seconds' => $timeDifference
                ]);
                return false;
            }

            return true;

        } catch (\Exception $e) {
            \Log::error('Timestamp validation error', [
                'timestamp' => $timestamp,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}