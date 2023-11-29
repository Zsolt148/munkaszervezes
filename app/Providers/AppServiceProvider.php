<?php

namespace App\Providers;

use App\Helpers\PermissionRegistrar;
use App\Models\Settings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (class_exists(TelescopeApplicationServiceProvider::class)) {
            $this->app->register(TelescopeServiceProvider::class);
        }

        // Singleton settings
        $this->app->singleton(Settings::class,
            fn () => cache()->rememberForever('settings',
                fn () => Settings::query()->with('media')->first()
            )
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(PermissionRegistrar $permissionLoader)
    {
        $this->app->register(AuthServiceProvider::class);

        // PermissionRegistrar
        if ($this->app->config['permission.register_permission_check_method']) {
            $permissionLoader->clearClassPermissions();
            $permissionLoader->registerPermissions();
        }

        $this->app->singleton(PermissionRegistrar::class, function ($app) use ($permissionLoader) {
            return $permissionLoader;
        });

        //Model::shouldBeStrict(!$this->app->isProduction());

        // Since this is a performance concern only, don't halt
        // production for violations.
        Model::preventLazyLoading(! $this->app->isProduction());

        // As these are concerned with application correctness,
        // leave them enabled all the time.
        Model::preventAccessingMissingAttributes();
        Model::preventSilentlyDiscardingAttributes();

        // Turn off 'data' wrap on json resources
        JsonResource::withoutWrapping();

        /*
         * Usage:
         * Illuminate\Validation\Rules\Password\Password::defaults()
         */
        Password::defaults(function () {
            return Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols();
        });
    }
}
