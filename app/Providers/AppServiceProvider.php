<?php

namespace App\Providers;

use App\Models\UnitOrder;
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

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\ImageOptimizationService::class, function ($app) {
            return new \App\Services\ImageOptimizationService();
        });
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

        $this->configureCommands();
        $this->configureModels();
        $this->translatableComponents();

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
    }
}
