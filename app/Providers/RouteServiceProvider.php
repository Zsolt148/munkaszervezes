<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    public const ADMIN = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            $this->mapAdminRoutes();
        });
    }

    protected function mapAdminRoutes()
    {
        Route::middleware('admin')
            ->group(function () {

                Route::group([], base_path('routes/auth.php'));

                Route::middleware(['auth:admin'])
                    ->prefix('profile')
                    ->group(base_path('routes/profile.php'));

                Route::middleware(['auth:admin', 'verified']) // 'role:admin|superadmin'
                    ->group(base_path('routes/web.php'));

                Route::middleware(['auth:admin', 'verified'])
                    ->group(base_path('routes/devops.php'));
            });

        // Admin api
        Route::middleware('api')
            ->name('api:')
            ->prefix('api')
            ->group(base_path('routes/api.php'));
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('none', function (Request $request) {
            return Limit::none();
        });
    }
}
