<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\UnitOrder;
use App\Observers\ProjectObserver;
use App\Observers\UnitOrderObserver;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Entry;
use Filament\Support\Components\Component;
use Filament\Support\Concerns\Configurable;
use Filament\Tables\Columns\Column;
use Filament\Tables\Filters\BaseFilter;
use Firefly\FilamentBlog\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    protected function translatableComponents(): void
    {
        foreach ([Field::class, BaseFilter::class, Placeholder::class, Column::class, Entry::class] as $component) {
            /* @var Configurable $component */
            $component::configureUsing(function (Component $translatable): void {
                /** @phpstan-ignore method.notFound */
                $translatable->translateLabel();
            });
        }
    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands($this->app->isProduction());
    }

    private function configureModels(): void
    {
        Model::shouldBeStrict(! app()->isProduction());
    }

    public function boot(): void
    {
        UnitOrder::observe(UnitOrderObserver::class);
        Project::observe(ProjectObserver::class);

        $this->configureCommands();
        $this->configureModels();
        $this->translatableComponents();

        // Re-run SetLocale on Livewire update requests (/livewire/update), which
        // bypass the public route groups. Without this, URL::defaults(['locale'])
        // is unset during component re-renders and route('frontend.projects.single', $slug)
        // misassigns the slug to the optional {locale?} segment.
        Livewire::addPersistentMiddleware([
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Keep the cached setting() helper (app/Helpers.php) fresh: drop the
        // per-key cache entry whenever a setting is edited in Filament.
        $forgetSetting = function (\TomatoPHP\FilamentSettingsHub\Models\Setting $setting): void {
            \Illuminate\Support\Facades\Cache::forget('settings_hub.'.$setting->name);
        };
        \TomatoPHP\FilamentSettingsHub\Models\Setting::saved($forgetSetting);
        \TomatoPHP\FilamentSettingsHub\Models\Setting::deleted($forgetSetting);

        if (! app()->runningInConsole() && Schema::hasTable('fblog_settings')) {
            try {
                $BlogSettings = Setting::query()->first();
                view()->share('BlogSettings', $BlogSettings);
            } catch (\Throwable $e) {
                logger()->warning('BlogSettings skipped: '.$e->getMessage());
            }
        }

        // LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
        //     $switch
        //         ->visible(outsidePanels: true)
        //         ->locales(['ar','en']);
        // });

        // Custom Blade directive for roles
        Blade::directive('role', function ($role) {
            return "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>";
        });

        Blade::directive('endrole', function () {
            return '<?php endif; ?>';
        });

        // Custom Blade directive for permissions
        Blade::directive('permission', function ($permission) {
            return "<?php if(auth()->check() && auth()->user()->hasPermissionTo({$permission})): ?>";
        });

        Blade::directive('endpermission', function () {
            return '<?php endif; ?>';
        });

        // Email Rate Limiting for Hostinger SMTP
        RateLimiter::for('emails', function (object $job) {
            return Limit::perMinute(5); // Adjust based on Hostinger limits (usually 200/hr)
        });
    }
}
