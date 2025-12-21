<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TwoFactorController extends Controller
{
    // Show 2FA setup page
    public function showSetup()
    {
        $user = Auth::user();
        $google2fa = new \PragmaRX\Google2FA\Google2FA();

        if (!$user->two_factor_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->two_factor_secret = $secret;
            $user->save();
        } else {
            $secret = $user->two_factor_secret;
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name', 'EatZ'),
            $user->email,
            $secret
        );

        // Generate QR code as SVG using BaconQrCode
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return view('auth.2fa.setup', compact('secret', 'qrCodeSvg', 'user'));
    }

    // Enable 2FA
    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if (!$user->verify2FACode($request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code.']);
        }

        $user->two_factor_enabled = true;
        $user->save();

        return redirect()->route('account.me')
            ->with('success', 'Two-factor authentication enabled successfully.');
    }

    // Show disable confirmation
    public function showDisable()
    {
        return view('auth.2fa.disable');
    }

    // Disable 2FA
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = Auth::user();

        if (!\Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $user->disable2FA();

        return redirect()->route('account.me')
            ->with('success', 'Two-factor authentication disabled.');
    }

    // Show 2FA verification form (during login)
    public function showVerify()
    {
        if (!session()->has('2fa:user_id')) {
            return redirect()->route('login');
        }

        return view('auth.2fa.verify');
    }

    // Verify 2FA code during login
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = session('2fa:user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->verify2FACode($request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code.']);
        }

        // Clear 2FA session and log in
        session()->forget('2fa:user_id');
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/restaurants');
    }
}
