@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
    <div class="container">
        <h2>Forgot Password</h2>

        @if (session('success'))
            <div style="color: green; padding: 10px; background: #e8f5e9; margin-bottom: 15px;">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="color: red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p>Enter your email address to receive a password reset link.</p>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus>
            </div>
            <button type="submit">Send Reset Link</button>
        </form>

        <p><a href="{{ route('login') }}">Back to Login</a></p>
    </div>
@endsection