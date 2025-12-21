@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="auth-card">
        <h2>Login</h2>

        @if ($errors->any())
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 1rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}"
            style="box-shadow: none; border: none; padding: 0; margin: 0; background: transparent;">
            @csrf
            <div>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required autofocus>
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" style="width: 100%; margin-top: 1rem;">Login</button>
        </form>
        <p style="margin-top: 1.5rem; text-align: center;">
            <a href="{{ route('password.forgot') }}">Forgot your password?</a>
        </p>
        <p style="text-align: center; margin-bottom: 0;">
            <a href="{{ route('register') }}">Don't have an account? Register</a>
        </p>
    </div>
@endsection