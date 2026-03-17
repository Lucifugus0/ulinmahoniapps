<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class LanguageController extends Controller
{
    /**
     * Switch the application language and set a cookie.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request)
    {
        $locale = $request->input('locale');
        $redirectUrl = $request->input('redirect_url', url('/'));

        // Validate the locale
        /* Added 'zh' to accepted locales for Simplified Chinese support */
        if (!in_array($locale, ['en', 'id', 'zh'])) {
            $locale = config('app.locale', 'en');
        }

        // Set the language cookie (expires in 1 year)
        Cookie::queue('locale', $locale, 525600); // 525600 minutes = 365 days

        // Set the application locale for the current request
        App::setLocale($locale);

        // Store in session as backup
        session(['locale' => $locale]);

        // Ensure the redirect URL has the correct locale prefix
        $redirectUrl = $this->ensureLocalePrefix($redirectUrl, $locale);

        // Redirect back to the original page with correct locale
        return redirect($redirectUrl);
    }

    /**
     * Ensure the URL has the correct locale prefix.
     *
     * @param  string  $url
     * @param  string  $locale
     * @return string
     */
    private function ensureLocalePrefix($url, $locale)
    {
        // Parse the URL to get the path
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'] ?? '/';
        
        // Remove any existing locale prefix
        /* Added 'zh' to locale prefix regex for URL rewriting */
        $path = preg_replace('/^(id|en|zh)\/?/', '', $path);
        
        // Add the new locale prefix
        $path = '/' . $locale . '/' . ltrim($path, '/');
        
        // Reconstruct the URL
        $newUrl = $path;
        if (isset($parsedUrl['query'])) {
            $newUrl .= '?' . $parsedUrl['query'];
        }
        if (isset($parsedUrl['fragment'])) {
            $newUrl .= '#' . $parsedUrl['fragment'];
        }
        
        return $newUrl;
    }
}
