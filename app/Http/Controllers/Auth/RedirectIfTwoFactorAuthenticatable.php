<?php

namespace App\Http\Controllers\Auth;

use App\Events\Blocked;
use App\Events\Failed;
use App\Helpers\Fortify;
use App\Models\Admin;
use App\Traits\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;
use Laravel\Fortify\LoginRateLimiter;

class RedirectIfTwoFactorAuthenticatable
{
    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * The login rate limiter instance.
     *
     * @var \Laravel\Fortify\LoginRateLimiter
     */
    protected $limiter;

    /**
     * @var int
     */
    protected $attempts = Admin::LOGIN_ATTEMPTS;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(StatefulGuard $guard, LoginRateLimiter $limiter)
    {
        $this->guard = $guard;
        $this->limiter = $limiter;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        $user = $this->validateCredentials($request);

        if (Fortify::confirmsTwoFactorAuthentication()) {
            if (
                optional($user)->two_factor_secret &&
                is_null(optional($user)->two_factor_confirmed_at) &&
                in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))
            ) {
                return $this->twoFactorChallengeResponse($request, $user);
            } else {
                return $next($request);
            }
        }

        if (optional($user)->two_factor_secret &&
            in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
            return $this->twoFactorChallengeResponse($request, $user);
        }

        return $next($request);
    }

    /**
     * Attempt to validate the incoming credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function validateCredentials($request)
    {
        if (Fortify::$authenticateUsingCallback) {
            return tap(call_user_func(Fortify::$authenticateUsingCallback, $request), function ($user) use ($request) {
                if (! $user) {
                    $this->throwFailedAuthenticationException($request);
                }
            });
        }

        $model = $this->guard->getProvider()->getModel();

        return tap($model::where(Fortify::username(), $request->{Fortify::username()})->first(), function ($user) use ($request) {
            if (! $user || ! $this->guard->getProvider()->validateCredentials($user, ['password' => $request->password])) {
                $this->throwFailedAuthenticationException($request, $user);
            }
        });
    }

    /**
     * Throw a failed authentication validation exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function throwFailedAuthenticationException($request, $admin = null)
    {
        $this->limiter->increment($request);

        if ($admin instanceof Admin) {

            $this->incrementLoginAttempts($request, $admin);

            if ($admin->isBlocked()) {
                $this->throwBlockedException($request);
            }

            $this->fireFailedEvent($request, $admin);

            throw ValidationException::withMessages([
                Fortify::username() => [trans('auth.failed_attempts', ['left_attempts' => $this->attempts - $admin->login_attempts])],
            ]);
        }

        throw ValidationException::withMessages([
            Fortify::username() => [trans('auth.failed')],
        ]);

    }

    /**
     * @param  Request  $request
     * @return void
     */
    protected function incrementLoginAttempts($request, Admin $admin)
    {
        // increment without logs
        // because the loging attempts number will be logged
        // in the next request with the IP
        activity()->withoutLogs(function () use (&$admin) {
            $admin->increment('login_attempts');
        });

        // If login attempts are at limit
        // Dispatch event that he is blocked
        if ($admin->login_attempts == $this->attempts) {

            $admin->updateQuietly([
                'status' => Admin::STATUS_BLOCKED,
                'blocked_at' => now(),
            ]);

            Blocked::dispatch(config('fortify.guard'), $admin);
        }
    }

    /**
     * Throw a failed authentication validation exception.
     *
     * @param  Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function throwBlockedException($request)
    {
        $this->limiter->increment($request);

        throw ValidationException::withMessages([
            Fortify::username() => [trans('auth.blocked')],
        ]);
    }

    /**
     * Fire the failed authentication attempt event with the given arguments.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    protected function fireFailedEvent($request, $user)
    {
        event(new Failed(config('fortify.guard'), $user, [
            Fortify::username() => $request->{Fortify::username()},
            'password' => $request->password,
            'ip' => $request->ip(),
        ]));
    }

    /**
     * Get the two factor authentication enabled response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function twoFactorChallengeResponse($request, $user)
    {
        $request->session()->put([
            'login.id' => $user->getKey(),
            'login.remember' => $request->filled('remember'),
        ]);

        TwoFactorAuthenticationChallenged::dispatch($user);

        return $request->wantsJson()
                    ? response()->json(['two_factor' => true])
                    : redirect()->route('two-factor.login');
    }
}
