@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="container">
    <h2>Register</h2>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div>
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name')
              <span id="name-error" class="error" role="alert">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="surname">Surname</label>
            <input type="text" name="surname" id="surname" required>
        </div>
        <div>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="{{ old('username') }}" required>
            @error('username')
              <span id="username-error" class="error" role="alert">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" inputmode="email">
            @error('email')
              <span id="email-error" class="error" role="alert">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required autocomplete="new-password">
            @error('password')
              <span id="password-error" class="error" role="alert">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password">
        </div>
        <button type="submit">Register</button>
    </form>
    <p>
        <a href="{{ route('login') }}">Already have an account? Login</a>
    </p>
</div>
@endsection