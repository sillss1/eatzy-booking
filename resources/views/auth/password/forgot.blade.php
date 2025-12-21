@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
    <div class="container">
        <h2>Forgot Password</h2>

        @if (session('status'))
            <div class="alert alert-success"
                style="color: #155724; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 6px; margin-bottom: 20px;">
                ✅ {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger"
                style="color: #721c24; padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 6px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
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