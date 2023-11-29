<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveMaintenanceSettingsRequest;
use App\Http\Requests\SaveSecuritySettingsRequest;
use App\Http\Resources\SettingsResource;
use App\Models\Settings;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class SettingsController extends Controller
{
    protected Settings $settings;

    public function __construct()
    {
        $this->settings = app(Settings::class);
    }

    public function index()
    {
        Gate::authorize('update', $this->settings);

        return Inertia::render('Settings/Index', [
            'settings' => SettingsResource::make($this->settings),
        ]);
    }

    public function saveMaintenanceMode(SaveMaintenanceSettingsRequest $request)
    {
        $settings = $this->settings;

        $settings->update([
            'is_maintenance' => $request->status,
            'maintenance_password' => $request->password,
            'allowed_ips' => $request->ips,
        ]);

        if ($settings->is_maintenance == true) {

            $settings->maintenance_password
                ? Artisan::call('down', ['--secret' => $settings->maintenance_password])
                : Artisan::call('down');

        } else {
            Artisan::call('up');
        }

        return redirect()->back()->with('success', __('Maintenance mode saved'));
    }

    public function saveSecurity(SaveSecuritySettingsRequest $request)
    {
        $this->settings->update([
            'htaccess_username' => $request->username,
            'htaccess_password' => $request->password,
        ]);

        return redirect()->back()->with('success', __('Htaccess security saved'));
    }
}
