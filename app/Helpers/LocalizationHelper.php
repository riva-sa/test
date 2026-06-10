<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class LocalizationHelper
{
    public const SUPPORTED_LOCALES = ['ar', 'en'];

    public const DEFAULT_LOCALE = 'ar';

    public static function getAlternateLinks(string $currentRoute = null, array $params = []): string
    {
        $route = $currentRoute ?: Route::currentRouteName();
        $params = $params + self::currentRouteParameters();
        $html = '';

        foreach (self::SUPPORTED_LOCALES as $locale) {
            $url = $locale === self::DEFAULT_LOCALE
                ? URL::route($route, $params)
                : URL::route($route, array_merge($params, ['locale' => $locale]));

            $html .= sprintf(
                '<link rel="alternate" hreflang="%s" href="%s" />',
                $locale === 'ar' ? 'ar' : 'en',
                e($url)
            );
            $html .= "\n        ";
        }

        $html .= sprintf(
            '<link rel="alternate" hreflang="x-default" href="%s" />',
            e(URL::route($route, $params))
        );

        return $html;
    }

    public static function getCanonicalUrl(string $currentRoute = null, array $params = []): string
    {
        $route = $currentRoute ?: Route::currentRouteName();
        $params = $params + self::currentRouteParameters();

        return URL::route($route, $params);
    }

    /**
     * Current route parameters with the optional {locale?} segment removed,
     * so alternate/canonical links can be regenerated per-locale while still
     * supplying required params such as {slug}.
     *
     * @return array<string, mixed>
     */
    protected static function currentRouteParameters(): array
    {
        $params = Route::current()?->parameters() ?? [];
        unset($params['locale']);

        return $params;
    }

    public static function getCurrentLocale(): string
    {
        return app()->getLocale();
    }

    public static function getDirection(): string
    {
        return self::getCurrentLocale() === 'ar' ? 'rtl' : 'ltr';
    }

    public static function getHtmlLang(): string
    {
        return self::getCurrentLocale();
    }
}
