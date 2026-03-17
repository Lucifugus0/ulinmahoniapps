<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class LocaleHelper
{
    /**
     * Generate locale-aware route URL
     *
     * @param string $name Route name
     * @param array $parameters Route parameters
     * @param string|null $locale Specific locale (defaults to current locale)
     * @return string
     */
    public static function route(string $name, array $parameters = [], string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();

        // Remove 'locale.' prefix if it exists
        $name = str_replace('locale.', '', $name);

        // Check if locale-prefixed route exists
        if (Route::has("locale.{$name}")) {
            return route("locale.{$name}", array_merge(['locale' => $locale], $parameters));
        }

        // Fallback to original route name
        return route($name, $parameters);
    }

    /**
     * Get alternate locale URL (for language switcher)
     *
     * @param string|null $locale Target locale (defaults to opposite of current)
     * @return string
     */
    public static function alternateUrl(string $locale = null): string
    {
        // If no locale specified, toggle between id and en
        if (!$locale) {
            $locale = App::getLocale() === 'id' ? 'en' : 'id';
        }

        $route = request()->route();

        if (!$route) {
            return url("/{$locale}/homepage");
        }

        // Get current route parameters
        $params = $route->parameters();

        // Replace locale parameter
        $params['locale'] = $locale;

        return route($route->getName(), $params);
    }

    /**
     * Get all available locales
     *
     * @return array
     */
    public static function availableLocales(): array
    {
        return config('app.locales', ['id', 'en']);
    }

    /**
     * Get locale display name
     *
     * @param string|null $locale
     * @return string
     */
    public static function localeName(string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        $names = config('app.locale_names', [
            'id' => 'Bahasa Indonesia',
            'en' => 'English',
        ]);

        return $names[$locale] ?? $locale;
    }
}
