<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Http\Responses\RegisterResponse as FortifyRegisterResponse;

class RegisterResponse extends FortifyRegisterResponse
{
    public function toResponse($request)
    {
        // Log out the user if they were logged in during registration
        auth()->logout();
        
        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect()->route('login')
                ->with('status', 'Registration successful! Please check your email to verify your account.');
    }
}
