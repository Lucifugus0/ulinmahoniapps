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
        if (Auth::check() && Auth::user()->locale) {
            App::setLocale(Auth::user()->locale);
        } elseif (session('locale')) {
            App::setLocale(session('locale'));
        }

        return $next($request);
    }
}
