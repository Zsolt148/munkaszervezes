<?php

namespace App\Http\Middleware;

use App\Models\Settings;
use Closure;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class BasicAuthMiddleware
{
    /**
     * @param  Request  $request
     * @return \Illuminate\Http\Response|mixed
     */
    public function handle($request, Closure $next)
    {
        $settings = app(Settings::class);

        if ($settings && $settings->IsBasicAuthEnabled()) {
            if (Request::getUser() !== $settings->htaccess_username || Request::getPassword() !== $settings->htaccess_password) {
                $headers = ['WWW-Authenticate' => 'Basic'];

                return Response::make('Invalid credentials.', 401, $headers);
            }
        }

        return $next($request);
    }
}
