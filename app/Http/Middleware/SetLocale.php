<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const SUPPORTED_LOCALES = ['ar', 'en'];

    public const DEFAULT_LOCALE = 'ar';

    protected array $except = [
        'crm/*',
        'developer/*',
        'login',
        'logout',
        'admin/*',
        'api/*',
        'media/*',
        'run-storage-link',
        'single/*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        $locale = $this->resolveLocale($request);

        App::setLocale($locale);
        session()->put('locale', $locale);
        Cookie::queue('locale', $locale, 60 * 24 * 365);

        // Pre-fill the optional {locale?} route segment so that route() calls
        // map positional arguments to the remaining parameters (e.g. {slug})
        // instead of consuming them as the locale. The default locale produces
        // unprefixed URLs (empty segment); other locales prefix the path.
        URL::defaults([
            'locale' => $locale === self::DEFAULT_LOCALE ? '' : $locale,
        ]);

        return $next($request);
    }

    protected function shouldSkip(Request $request): bool
    {
        foreach ($this->except as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }

    protected function resolveLocale(Request $request): string
    {
        $routeLocale = $request->route()?->parameter('locale');

        if ($routeLocale && in_array($routeLocale, self::SUPPORTED_LOCALES, true)) {
            return $routeLocale;
        }

        $firstSegment = $request->segment(1);
        $routeHasLocaleParam = $request->route() && array_key_exists('locale', $request->route()->parameters());

        if ($firstSegment && ! $routeHasLocaleParam && ! in_array($firstSegment, self::SUPPORTED_LOCALES, true) && $this->looksLikeLocaleAttempt($firstSegment)) {
            Log::channel('stack')->warning('Invalid locale detected', [
                'request_uri' => $request->getRequestUri(),
                'detected_locale' => $firstSegment,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        if ($cookieLocale = Cookie::get('locale')) {
            if (in_array($cookieLocale, self::SUPPORTED_LOCALES, true)) {
                return $cookieLocale;
            }
        }

        if ($sessionLocale = session('locale')) {
            if (in_array($sessionLocale, self::SUPPORTED_LOCALES, true)) {
                return $sessionLocale;
            }
        }

        return self::DEFAULT_LOCALE;
    }

    protected function looksLikeLocaleAttempt(string $segment): bool
    {
        return (bool) preg_match('/^[a-z]{2}(-[A-Z]{2})?$/', $segment);
    }
}
