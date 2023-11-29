<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Traits\AdminForm;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class NewPasswordController extends Controller
{
    use AdminForm;

    /**
     * Display the password reset view.
     *
     * @return \Inertia\Response
     */
    public function create(Request $request)
    {
        $admin = Admin::whereEmail($request->email)->first();

        abort_if(! $admin, 404);

        return Inertia::render('Auth/ResetPassword', [
            'name' => $admin->name,
            'email' => $admin->email,
            'token' => $request->route('token'),
            'isRegister' => $admin->isInvited(),
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => $this->passwordRules(true),
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise, we will parse the error and return the response.
        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($admin) use (&$request) {

                $event = new PasswordReset($admin);

                if ($request->get('is_register')) {
                    $event = new Registered($admin);
                    $admin->fill([
                        'status' => Admin::STATUS_REGISTERED,
                    ]);

                }

                $admin->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                    'email_verified_at' => now(),
                ])->save();

                event($event);
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
