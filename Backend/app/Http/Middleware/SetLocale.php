<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        /* For authenticated users: use DB locale as source of truth and sync to session.
           This ensures locale persists even if Auth::check() fails transiently
           (e.g. session regeneration, AJAX timing). */
        if (Auth::check() && Auth::user()->locale) {
            $locale = Auth::user()->locale;
            App::setLocale($locale);
            if (session('locale') !== $locale) {
                session(['locale' => $locale]);
            }
        } elseif (session('locale')) {
            App::setLocale(session('locale'));
        }

        return $next($request);
    }
}
