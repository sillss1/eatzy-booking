@extends('layouts.app')

@section('title', 'Setup Two-Factor Authentication')

@section('content')
    <div class="container">
        <h2>Setup Two-Factor Authentication</h2>

        @if ($errors->any())
            <div style="color: red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($user->two_factor_enabled)
            <div style="color: green; padding: 10px; background: #e8f5e9; margin-bottom: 15px;">
                Two-factor authentication is <strong>enabled</strong>.
            </div>
            <p><a href="{{ route('2fa.disable') }}" class="button">Disable 2FA</a></p>
        @else
            <p>Scan the QR code below with your authenticator app (Google Authenticator, Authy, etc.):</p>

            <div style="margin: 20px 0; padding: 20px; background: #f5f5f5; text-align: center;">
                {!! $qrCodeSvg !!}
            </div>

            <p><strong>Manual entry key:</strong> <code>{{ $secret }}</code></p>

            <form method="POST" action="{{ route('2fa.enable') }}">
                @csrf
                <div>
                    <label for="code">Enter the 6-digit code from your authenticator app:</label>
                    <input type="text" name="code" id="code" maxlength="6" pattern="\d{6}" required autofocus
                        style="width: 120px; font-size: 18px; text-align: center; letter-spacing: 5px;">
                </div>
                <button type="submit">Enable 2FA</button>
            </form>
        @endif

        <p style="margin-top: 20px;"><a href="{{ route('account.me') }}">&larr; Back to Profile</a></p>
    </div>
@endsection