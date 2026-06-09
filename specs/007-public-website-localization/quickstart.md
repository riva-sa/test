# Quickstart: Public Website Localization

## Prerequisites

- PHP 8.2+
- Laravel 11.x
- Livewire 3.x

## Implementation Order

### 1. Translation Files
```bash
# Create Arabic translation file (extract existing strings)
touch lang/ar/public.php

# Create English translation file
touch lang/en/public.php
```

### 2. Middleware
```bash
php artisan make:middleware SetLocale
```
Update `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'set.locale' => \App\Http\Middleware\SetLocale::class,
    ]);
})
```

### 3. Routes
Wrap public routes in locale-prefixed group in `routes/web.php`.

### 4. Layout
Update `resources/views/components/layouts/app.blade.php`:
- Dynamic `lang` and `dir` attributes on `<html>`
- Hreflang and canonical `<link>` tags in `<head>`

### 5. Blade Views
Replace hardcoded Arabic strings with `@lang('public.key')` in each public Blade view.

### 6. Livewire Components
Replace hardcoded strings with `__('public.key')` in PHP component files. Update `#[Title]` attributes to use translation helper.

### 7. Language Switcher
Add two text links in `nav-bar.blade.php`:
```blade
<a href="{{ route(Route::currentRouteName(), ['locale' => 'ar']) }}">العربية</a>
<a href="{{ route(Route::currentRouteName(), ['locale' => 'en']) }}">English</a>
```

### 8. Testing
```bash
php artisan test --filter=Localization
```

## Key Files

| File | Purpose |
|------|---------|
| `app/Http/Middleware/SetLocale.php` | Locale detection from URL/cookie/session |
| `app/Helpers/LocalizationHelper.php` | SEO hreflang/canonical tag generation |
| `lang/ar/public.php` | Arabic translation strings |
| `lang/en/public.php` | English translation strings |
| `bootstrap/app.php` | Middleware registration |

## Rollback

See `Rollback Plan` in spec.md for step-by-step revert instructions.
