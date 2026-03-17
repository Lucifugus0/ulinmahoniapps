<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Priority: Cookie > URL prefix > Session > Browser Accept-Language > Default (id)
        $locale = $this->determineLocale($request);

        // Set application locale
        App::setLocale($locale);

        // Store in session for persistence
        Session::put('locale', $locale);

        // Share with all views
        view()->share('currentLocale', $locale);

        return $next($request);
    }

    /**
     * Determine the locale based on multiple sources
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    private function determineLocale(Request $request): string
    {
        // 1. Check cookie (highest priority for persistence)
        $cookieLocale = $request->cookie('locale');
        /* Added 'zh' to accepted locales for Simplified Chinese support */
        if ($cookieLocale && in_array($cookieLocale, ['id', 'en', 'zh'])) {
            return $cookieLocale;
        }

        // 2. Check URL prefix
        $urlSegment = $request->segment(1);
        if (in_array($urlSegment, ['id', 'en', 'zh'])) {
            return $urlSegment;
        }

        // 3. Check session
        if (Session::has('locale')) {
            $locale = Session::get('locale');
            if (in_array($locale, ['id', 'en', 'zh'])) {
                return $locale;
            }
        }

        // 4. Default to Indonesian (since most content is Indonesian)
        // Note: Browser Accept-Language detection removed to ensure consistent default
        return 'id';
    }
}
