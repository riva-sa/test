<?php

/*
 * Here you can define your own helper functions.
 * Make sure to use the `function_exists` check to not declare the function twice.
 */

if (! function_exists('example')) {
    function example(): string
    {
        return 'This is an example function you can use in your project.';
    }
}

if (! function_exists('setting')) {
    /**
     * Cached replacement for filament-settings-hub's `setting()` helper.
     *
     * The package defines its own `setting()` via `require_once` inside its
     * service provider (guarded by `function_exists`). Because this file is
     * loaded by Composer's autoloader — before any service provider boots —
     * our definition wins and the package's uncached version is skipped.
     *
     * Settings are static config values, so they are cached forever and only
     * invalidated when a Setting row is saved/deleted (see AppServiceProvider).
     * The previous behaviour ran a fresh DB query on every call; the navbar
     * and layout alone triggered several queries on every page load.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        try {
            $value = \Illuminate\Support\Facades\Cache::rememberForever(
                'settings_hub.'.$key,
                fn () => optional(
                    \TomatoPHP\FilamentSettingsHub\Models\Setting::query()
                        ->where('name', $key)
                        ->first(['payload'])
                )->payload
            );

            return $value ?? $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }
}

//
