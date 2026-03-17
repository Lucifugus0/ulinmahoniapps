<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Api\GoogleCallbackRequest;

class SocialAuthController extends Controller
{
    /**
     * Get the Google OAuth URL
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function redirectToGoogle()
    {
        try {
            // Generate the URL manually to avoid session dependency
            $scopes = [
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile',
                'openid'
            ];
            
            $parameters = [
                'client_id' => config('services.google.client_id'),
                'redirect_uri' => config('services.google.redirect'),
                'response_type' => 'code',
                'scope' => implode(' ', $scopes),
                'access_type' => 'offline',
                'prompt' => 'select_account',
            ];

            $url = 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($parameters);
                
            return response()->json([
                'success' => true,
                'data' => [
                    'url' => $url
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Google Redirect Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate Google OAuth URL',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Handle Google OAuth callback
     * 
     * @param  \App\Http\Requests\Api\GoogleCallbackRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleGoogleCallback(GoogleCallbackRequest $request)
    {
        try {
            // Check for error in the OAuth response
            if ($request->has('error')) {
                throw new \Exception($request->error);
            }

            // Get the user from Google
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();

            if (!$googleUser->getEmail()) {
                throw new \Exception('No email returned from Google');
            }

            // Check if user already exists
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Create a new user
                $email = $googleUser->getEmail();
                $username = strstr($email, '@', true) ?: $email;
                
                $user = User::create([
                    'name' => $googleUser->getName() ?: $username,
                    'username' => $username,
                    'email' => $email,
                    'password' => bcrypt(Str::random(16)),
                    'email_verified_at' => now(),
                ]);
            }
            
            // Create API token
            $tokenResult = $user->createToken('auth-token');
            $token = $tokenResult->plainTextToken;
            
            return response()->json([
                'success' => true,
                'message' => 'Successfully logged in with Google',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'email' => $user->email,
                        'profile_photo_url' => $user->profile_photo_url,
                    ],
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'expires_at' => $tokenResult->accessToken->expires_at->toDateTimeString(),
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Google OAuth API Error: ' . $e->getMessage());
            Log::error('Error Trace: ' . $e->getTraceAsString());
            
            $errorMessage = 'Authentication failed';
            $statusCode = 401;
            
            // Handle specific error cases
            if (str_contains($e->getMessage(), 'Invalid state')) {
                $errorMessage = 'Session expired. Please try again.';
                $statusCode = 400;
            } elseif (str_contains($e->getMessage(), 'Invalid verification code')) {
                $errorMessage = 'Invalid verification code. Please try again.';
                $statusCode = 400;
            } elseif (str_contains($e->getMessage(), 'No email returned')) {
                $errorMessage = 'No email address found in Google account. Please use a Google account with a verified email.';
                $statusCode = 400;
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error' => config('app.debug') ? $e->getMessage() : null
            ], $statusCode);
        }
    }

    /**
     * Logout the user (Revoke the token)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }
}
