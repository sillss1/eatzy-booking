@extends('layouts.app')

@section('title', 'Disable Two-Factor Authentication')

@section('content')
    <div class="container">
        <h2>Disable Two-Factor Authentication</h2>

        @if ($errors->any())
            <div style="color: red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p>Enter your password to disable two-factor authentication:</p>

        <form method="POST" action="{{ route('2fa.disable.submit') }}">
            @csrf
            <div>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" style="background-color: #dc3545; border-color: #dc3545;">Disable 2FA</button>
        </form>

        <p style="margin-top: 20px;"><a href="{{ route('2fa.setup') }}">&larr; Cancel</a></p>
    </div>
@endsection