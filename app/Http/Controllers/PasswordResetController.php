<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    use SendsPasswordResetEmails, ResetsPasswords {
        ResetsPasswords::credentials insteadof SendsPasswordResetEmails;
        ResetsPasswords::broker insteadof SendsPasswordResetEmails;
    }

    protected $redirectTo = '/login';

    /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        // If this is the reset request (has password/token), use ResetsPasswords logic
        if ($request->has('password')) {
            return $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            );
        }

        // Otherwise it's the send link request
        return $request->only('email');
    }

    public function showLinkRequestForm()
    {
        return view('auth.password.forgot');
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.password.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Reset the given user's password.
     * Overridden to skip remember_token update as the column doesn't exist.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $this->setUserPassword($user, $password);

        // $user->setRememberToken(Str::random(60)); // Skipped: Table lacks remember_token

        $user->save();

        event(new \Illuminate\Auth\Events\PasswordReset($user));

        $this->guard()->login($user);
    }
}
