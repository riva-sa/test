# Research: Riva Public Website Localization

## Key Decisions

### Locale URL Strategy
- **Decision**: URL prefix (`/en`, `/ar`) with no-prefix defaulting to Arabic
- **Rationale**: Backward compatible with existing Arabic URLs; SEO-friendly; no need for query parameters or subdomains
- **Alternatives considered**: Subdomain (`en.riva.com`) — more complex DNS/setup, splits SEO authority

### Locale Persistence
- **Decision**: URL prefix wins always; persistent cookie as fallback for no-prefix visits
- **Rationale**: URL is explicit user action; cookie ensures returning users retain preference without requiring URL prefix
- **Alternatives considered**: Session-only — lost on browser close, poor UX

### Language Switcher UI
- **Decision**: Two separate text links ("English" / "العربية") side by side in navbar
- **Rationale**: Most accessible pattern; clear current state; scales to future languages via dropdown extension
- **Alternatives considered**: Toggle button (ambiguous current state), flag icons (cultural sensitivity, accessibility)

### JS-Disabled Fallback
- **Decision**: Links degrade to standard `<a href="/en/...">` full-page navigation
- **Rationale**: Graceful degradation; no JS dependency for core navigation
- **Alternatives considered**: `<noscript>` notice (blocks access), query param fallback (more complex routing)

### Translation Coverage Verification
- **Decision**: Mixed — automated Artisan command scanning Blade/Livewire for missing keys + manual visual QA
- **Rationale**: Automated scan catches regressions in CI; manual QA verifies visual correctness in context

## Technology Choices

| Concern | Choice | Rationale |
|---------|--------|-----------|
| Translation files | Laravel PHP array files | Native Laravel; no extra dependencies; cached via `php artisan translate:cache` |
| Middleware registration | `bootstrap/app.php` | Laravel 11.x style; alias middleware |
| Route grouping | `Route::prefix('/{locale?}')` | Built-in Laravel routing; regular expression constraint for locale |
| SEO tags | `LocalizationHelper` service | Encapsulates hreflang/canonical generation; single responsibility |
| Testing | Pest + Laravel HTTP tests | Matches project testing standard (constitution III) |
