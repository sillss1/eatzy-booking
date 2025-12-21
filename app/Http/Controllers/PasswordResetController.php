<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailModel;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    // Show forgot password form
    public function showForgotForm()
    {
        return view('auth.password.forgot');
    }

    // Handle forgot password request
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Don't reveal if email exists (security)
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('success', 'If that email exists, a reset link has been sent.');
        }

        $token = PasswordReset::createToken($request->email);
        $resetLink = url('/password/reset/' . $token);

        // Check if mail is configured
        if (!env('MAIL_HOST') || !env('MAIL_USERNAME')) {
            return back()->with('success', 'Mail not configured. Reset link: ' . $resetLink);
        }

        try {
            $mailData = [
                'subject' => 'Reset Your Password - EatZy',
                'view' => 'emails.reset-password',
                'resetUrl' => $resetLink,
                'name' => $user->name,
            ];

            Mail::to($user->email)->send(new MailModel($mailData));
            return back()->with('success', 'Password reset link sent to your email!');
        } catch (\Exception $e) {
            // Fallback: show link if email fails
            return back()->with('success', 'Email service error. Reset link: ' . $resetLink);
        }
    }

    // Show reset password form
    public function showResetForm($token)
    {
        $reset = PasswordReset::findByToken($token);

        if (!$reset) {
            return redirect()->route('password.forgot')
                ->withErrors(['msg' => 'Invalid or expired reset link.']);
        }

        return view('auth.password.reset', compact('token'));
    }

    // Handle password reset
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $reset = PasswordReset::findByToken($request->token);

        if (!$reset) {
            return redirect()->route('password.forgot')
                ->withErrors(['msg' => 'Invalid or expired reset link.']);
        }

        $user = User::where('email', $reset->email)->first();

        if (!$user) {
            return redirect()->route('password.forgot')
                ->withErrors(['msg' => 'User not found.']);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete used token
        $reset->delete();

        return redirect()->route('login')
            ->with('success', 'Password reset successfully. Please login.');
    }
}
