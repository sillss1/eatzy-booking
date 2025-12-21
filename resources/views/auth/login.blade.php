@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="container">
        <h2>Login</h2>

        @if ($errors->any())
            <div>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required autofocus>
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>
            <a href="{{ route('password.forgot') }}">Forgot your password?</a>
        </p>
        <p>
            <a href="{{ route('register') }}">Don't have an account? Register</a>
        </p>
    </div>
@endsection