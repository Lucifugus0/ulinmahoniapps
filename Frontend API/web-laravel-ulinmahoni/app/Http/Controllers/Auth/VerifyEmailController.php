<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function __invoke(Request $request, $id, $hash)
    {
        \Log::info('Verification attempt', [
            'id' => $id,
            // 'hash' => $hash,
            // 'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        if (! $request->hasValidSignature()) {
            \Log::error('Invalid verification signature', [
                'url' => $request->fullUrl(),
                'signature' => $request->query('signature')
            ]);
            return response()->json([
                'message' => 'Invalid verification link or signature.'
            ], 403);
        }

        $user = User::findOrFail($id);
        \Log::info('User found', ['user_id' => $user->id, 'email' => $user->email]);

        if (! hash_equals((string) $id, (string) $user->getKey())) {
            \Log::error('User ID mismatch', [
                'provided_id' => $id,
                'user_key' => $user->getKey()
            ]);
            throw new AuthorizationException('User ID mismatch');
        }

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            \Log::error('Hash mismatch', [
                'provided_hash' => $hash,
                'expected_hash' => sha1($user->getEmailForVerification()),
                'email' => $user->email
            ]);
            throw new AuthorizationException('Hash verification failed');
        }

        if ($user->hasVerifiedEmail()) {
            \Log::info('Email already verified', ['user_id' => $user->id]);
            return $request->wantsJson()
                ? response()->json(['message' => 'Email already verified.'])
                : redirect($this->redirectPath());
        }

        // Verify the email
        $user->email_verified_at = now();
        $saved = $user->save();
        
        if ($saved) {
            \Log::info('Email verified successfully', ['user_id' => $user->id]);
            event(new Verified($user));
            // Auto-login the user so the confirmation page can greet them by email
            if (! Auth::check()) {
                Auth::login($user);
            }
        } else {
            \Log::error('Failed to verify email', ['user_id' => $user->id]);
        }

        return $request->wantsJson()
            ? response()->json(['message' => 'Email verified successfully.'])
            : redirect()->route('verification.confirmation')
                ->with('status', 'Email verified successfully!');
    }

    /**
     * Get the post verification redirect path.
     *
     * @return string
     */
    protected function redirectPath()
    {
        return route('verification.confirmation');
    }
}
