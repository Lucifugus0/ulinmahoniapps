<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordReset;
use App\Notifications\PasswordResetConfirmationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Rules\Password;
use Illuminate\Support\Facades\Password as PasswordFacade;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users', 'alpha_dash'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['nullable', 'string', 'max:20', 'unique:users'],
            'password' => ['required', 'string', new Password],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'name' => $request->username,
                'username' => $request->username,
                'email' => $request->email,
                'phone_number' => $request->phone_number ?? null,
                'password' => Hash::make($request->password),
                'status' => 1,
                'is_admin' => 0
            ]);

            // Send email verification notification
            $user->sendEmailVerificationNotification();

            // Create token for immediate login if needed
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Registration successful. Please check your email to verify your account.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'email' => $user->email,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'status' => $user->status,
                        'is_admin' => $user->is_admin,
                        'profile_photo_url' => $user->profile_photo_url,
                        'email_verified_at' => $user->email_verified_at,
                    ],
                    'token' => $token,
                    'requires_email_verification' => true
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function registerWithoutVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users', 'alpha_dash'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['nullable', 'string', 'max:20', 'unique:users'],
            'nik' => ['nullable', 'string', 'max:16', 'unique:users'],
            'password' => ['required', 'string', new Password],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'name' => $request->username,
                'username' => $request->username,
                'email' => $request->email,
                'phone_number' => $request->phone_number ?? null,
                'nik' => $request->nik ?? null,
                'password' => Hash::make($request->password),
                'status' => 1,
                'is_admin' => 0,
            ]);

            // Mark email as verified immediately (no email verification required for API registration)
            $user->markEmailAsVerified();

            // Create token for immediate login
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Registration successful.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'email' => $user->email,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'status' => $user->status,
                        'is_admin' => $user->is_admin,
                        'profile_photo_url' => $user->profile_photo_url,
                        'email_verified_at' => $user->email_verified_at,
                    ],
                    'token' => $token,
                    'requires_email_verification' => false
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';
        
        // First, find the user by email or phone
        $user = User::where($loginField, $request->login)->first();

        // Check if user exists and email is not verified
        if ($user && $loginField === 'email' && !$user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please verify your email address before logging in. Check your email for the verification link.',
                'requires_email_verification' => true
            ], 403);
        }

        // Check if user exists and is active
        if ($user) {
            if ($user->status != 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Your account is inactive. Please contact support.'
                ], 403);
            }
        }

        // Attempt authentication
        if (Auth::attempt([$loginField => $request->login, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'username' => $user->username,
                        'phone_number' => $user->phone_number,
                        'email' => $user->email,
                        'status' => $user->status,
                        'is_admin' => $user->is_admin,
                        'profile_photo_url' => $user->profile_photo_url,
                        'profile_photo_path' => $user->profile_photo_path,
                        'email_verified_at' => $user->email_verified_at,
                    ],
                    'token' => $token
                ]
            ]);
        }

        // If we reach here, credentials are invalid
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials',
            'error_code' => 'invalid_credentials'
        ], 401);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get the user
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email not found'
            ], 404);
        }

        // Generate a token
        $token = Str::random(60);
        
        // Save the token in the password_resets table
        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Send the reset link email
        $user->sendPasswordResetNotification($token);

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset link sent successfully',
            'token' => $token // For testing, remove in production
        ], 200);
    }

    /**
     * Reset the user's password using a token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'string', new Password],
            'password_confirmation' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email is not valid',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get the password reset record
        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$reset) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid password reset token'
            ], 422);
        }

        // Check if token is valid
        if (!Hash::check($request->token, $reset->token)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid password reset token'
            ], 422);
        }

        // Check if token is expired (1 hour)
        if (Carbon::parse($reset->created_at)->addHour()->isPast()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password reset token has expired'
            ], 422);
        }

        // Update user's password
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Send password reset confirmation email
        try {
            $user->notify(new PasswordResetConfirmationNotification($user));
            Log::info('Password reset confirmation notification queued', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        } catch (\Exception $e) {
            // Log the error but don't fail the password reset
            Log::error('Failed to queue password reset confirmation notification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
        }

        // Delete the used token
        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Password has been reset successfully'
        ], 200);
    }

    

    /**
     * Resend email verification notification
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            // Check if email is already verified
            if ($user->hasVerifiedEmail()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email already verified'
                ], 400);
            }

            // Send verification email using the custom notification
            $user->sendEmailVerificationNotification();

            return response()->json([
                'status' => 'success',
                'message' => 'Verification email sent successfully. Please check your email.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send verification email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error during logout'
            ], 500);
        }
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'status' => $user->status,
                    'is_admin' => $user->is_admin,
                    'profile_photo_url' => $user->profile_photo_url,
                ]
            ]
        ]);
    }

    public function updateProfile(Request $request, $id)
    {
        // $user = $request->user() ;
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => ['nullable', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:users,username,'.$user->id],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user->update([
                'name' => $request->name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone_number' => $request->phone_number,
                'username' => $request->username,
                'email' => $request->email,
            ]);

            // Handle profile picture update if present
            if ($request->has('profile_picture') && $request->profile_picture) {
                $base64Image = $request->input('profile_picture');
                // Validate base64 string
                $data = base64_decode($base64Image, true);
                if ($data === false) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid base64 string for profile picture.'
                    ], 422);
                }

                // Detect image type
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->buffer($data);
                $mimeParts = explode('/', $mime);
                $type = strtolower(end($mimeParts));

                if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid image type. Only JPG, PNG, GIF, and WebP are allowed.'
                    ], 422);
                }

                $filename = 'profile-photos/' . uniqid() . '.' . $type;
                $disk = \Storage::disk('public');

                // Delete old profile picture if exists
                if ($user->profile_photo_path && $disk->exists($user->profile_photo_path)) {
                    $disk->delete($user->profile_photo_path);
                }

                // Store the new image
                $disk->put($filename, $data);
                $user->profile_photo_path = $filename;
                $user->save();
                $user->refresh();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'username' => $user->username,
                        'phone_number' => $user->phone_number,
                        'email' => $user->email,
                        'status' => $user->status,
                        'is_admin' => $user->is_admin,
                        'profile_photo_url' => $user->profile_photo_url,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Profile update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the user's profile picture.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  User ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfilePicture(Request $request, $id)
    {
        \Log::info('Starting profile picture update', ['user_id' => $id]);
        
        $validator = Validator::make($request->all(), [
            'profile_picture' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::findOrFail($id);
            $base64Image = $request->input('profile_picture');
            
            \Log::debug('Processing profile picture update', [
                'user_id' => $user->id,
                'has_previous_photo' => !empty($user->profile_photo_path),
                'base64_length' => strlen($base64Image)
            ]);
            
            // Decode the base64 string
            $data = base64_decode($base64Image, true);
            if ($data === false) {
                throw new \Exception('Invalid base64 string');
            }
            
            // Detect the image type
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->buffer($data);
            $mimeParts = explode('/', $mime);
            $type = strtolower(end($mimeParts));
            
            if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                throw new \Exception('Invalid image type. Only JPG, PNG, and GIF are allowed.');
            }
            
            // Generate unique filename with proper extension
            $filename = 'profile-photos/' . uniqid() . '.' . $type;
            
            // Get the storage disk
            $disk = Storage::disk('public');
            
            // Delete old profile picture if exists
            if ($user->profile_photo_path && $disk->exists($user->profile_photo_path)) {
                \Log::debug('Deleting old profile photo', [
                    'old_path' => $user->profile_photo_path,
                    'exists' => $disk->exists($user->profile_photo_path)
                ]);
                $disk->delete($user->profile_photo_path);
            }
            
            // Store the new image
            $disk->put($filename, $data);
            \Log::debug('New profile photo stored', ['path' => $filename]);
            
            // Update user's profile photo path
            $user->profile_photo_path = $filename;
            $user->save();
            
            // Refresh to get updated attributes
            $user->refresh();
            
            \Log::info('Profile picture updated successfully', [
                'user_id' => $user->id,
                'new_path' => $filename
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile picture updated successfully',
                'data' => [
                    'profile_picture' => $user->profile_picture,
                    'profile_photo_path' => $user->profile_photo_path,
                    'profile_photo_url' => $user->profile_photo_url,
                    'storage_path' => $filename,
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to update profile picture', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update profile picture',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  User ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', new Password],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find the user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Current password is incorrect'
            ], 422);
        }

        try {
            // Update the user's password
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Password updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
