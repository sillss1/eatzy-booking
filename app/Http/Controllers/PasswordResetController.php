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

    // Get credentials based on request type (forgot link or reset password)
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

    // Override to skip remember_token (our table doesn't have it)
    protected function resetPassword($user, $password)
    {
        $this->setUserPassword($user, $password);

        // $user->setRememberToken(Str::random(60)); // Skipped: Table lacks remember_token

        $user->save();

        event(new \Illuminate\Auth\Events\PasswordReset($user));

        $this->guard()->login($user);
    }
}
