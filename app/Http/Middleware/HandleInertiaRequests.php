<?php

namespace App\Http\Middleware;

use App\Helpers\AdminFeatures;
use App\Http\Resources\SettingsResource;
use App\Models\Admin;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array
     */
    public function share(Request $request)
    {
        $admin = $request->user('admin');
        $settings = app(Settings::class);
        $version = new \PragmaRX\Version\Package\Version();

        return array_merge(parent::share($request), [
            'env' => app()->environment(),

            'csrf_token' => csrf_token(),

            'url' => config('app.url'),

            'locale' => fn () => app()->getLocale(),

            'locales' => mapForSelect(config('app.locales'), sameValue: false),

            'language' => fn () => translations(
                base_path('lang/hu.json')
            ),

            'auth' => [
                'id' => optional($admin, fn (Admin $admin) => $admin->id),
                'user' => $admin,
                'notifications' => Auth::check() ?? Auth::user()->notifications,
                'roles' => optional($admin, fn (Admin $admin) => $admin->roles->pluck('name')->toArray()
                ),
                'roles_text' => optional($admin, fn (Admin $admin) => $admin->roles->pluck('name')->map(fn ($name) => __('role.'.$name))->join(', ')
                ),
                'permissions' => optional($admin, fn (Admin $admin) =>
                    // All permissions which apply on the user (inherited and direct)
                    $admin->getAllPermissions()->pluck('name')->toArray()
                ),
                'role' => [
                    'isSuperadmin' => Gate::allows('isSuperadmin', $admin),
                ],
            ],

            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },

            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],

            'page' => [
                'logo' => $settings?->getMedia('logos')->first()?->getUrl() ?? null,
                'favicon' => $settings?->getMedia('favicons')->first()?->getUrl('favicon') ?? null,
                'author' => $settings?->seo_page_author ?? null,
                'description' => $settings?->seo_page_description ?? null,
                'maintenance' => $settings?->is_maintenance,
                'settings' => $settings ? SettingsResource::make($settings) : null,
                'primary_color' => $settings?->primary_color,
                'telescope' => config('telescope.enabled'),
            ],

            'version' => $version->compact() ?? '',

            'hasAdminProxy' => $request->session()->has('admin-proxy-original'),

            'features' => [

                // Admin related
                'canRegister' => AdminFeatures::canRegister(),
                'hasProfileFeatures' => AdminFeatures::hasProfileFeatures(),
                'hasSecurityFeatures' => AdminFeatures::hasSecurityFeatures(),
                'canUpdateProfileInformation' => AdminFeatures::canUpdateProfileInformation(),
                'canUpdatePassword' => AdminFeatures::canUpdatePasswords(),
                'canManageTwoFactorAuthentication' => AdminFeatures::canManageTwoFactorAuthentication(),
                'canDeleteAccount' => AdminFeatures::canDeleteAccount(),

                // Admin site related
                'hasLogs' => AdminFeatures::hasLogs(),
            ],
        ]);
    }
}
