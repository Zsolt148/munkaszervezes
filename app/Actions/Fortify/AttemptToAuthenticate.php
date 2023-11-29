<?php

namespace App\Actions\Fortify;

use App\Events\Blocked;
use App\Events\Failed;
use App\Helpers\Fortify;
use App\Models\Admin;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\LoginRateLimiter;

class AttemptToAuthenticate
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
     * @param  Request  $request
     * @param  callable  $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        $admin = Admin::where(Fortify::username(), $request->only(Fortify::username()))->first();

        if ($admin && $admin->isBlocked()) {
            $this->throwBlockedException($request);
        }

        if ($this->guard->attempt(
            $request->only(Fortify::username(), 'password'),
            $request->boolean('remember'))
        ) {
            return $next($request);
        }

        $this->throwFailedAuthenticationException($request, $admin);
    }

    /**
     * Attempt to authenticate using a custom callback.
     *
     * @param  Request  $request
     * @param  callable  $next
     * @return mixed
     */
    protected function handleUsingCustomCallback($request, $next)
    {
        $user = call_user_func(Fortify::$authenticateUsingCallback, $request);

        if (! $user) {
            $this->fireFailedEvent($request);

            return $this->throwFailedAuthenticationException($request);
        }

        $this->guard->login($user, $request->boolean('remember'));

        return $next($request);
    }

    /**
     * Throw a failed authentication validation exception.
     *
     * @param  Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function throwFailedAuthenticationException($request, ?Admin $admin = null)
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
     * @param  Request  $request
     * @return void
     */
    protected function fireFailedEvent($request, Admin $admin)
    {
        event(new Failed(config('fortify.guard'), $admin, [
            Fortify::username() => $request->{Fortify::username()},
            'password' => $request->password,
            'ip' => $request->ip(),
        ]));
    }
}
