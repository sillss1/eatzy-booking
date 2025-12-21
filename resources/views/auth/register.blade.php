@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="auth-card">
        <h2>Register</h2>

        <form method="POST" action="{{ route('register') }}"
            style="box-shadow: none; border: none; padding: 0; margin: 0; background: transparent;">
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
                <input type="text" name="surname" id="surname" value="{{ old('surname') }}" required>
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
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email"
                    inputmode="email">
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
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    autocomplete="new-password">
            </div>

            <!-- Role Selection -->
            <div style="margin-top: 1rem;">
                <label>Account Type
                    <span class="tooltip"> ⓘ
                        <span class="tooltip-text">In EatZy Booking you can create two types of accounts:
                            - For a restaurant owner - It allows you to post your restaurant(s) on the site, so others can
                            book tables at it
                            - For a customer - It allows you to book tables at restaurants
                        </span>
                    </span>
                </label>
                <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                    <label style="font-weight: normal; font-size: 1rem;">
                        <input type="radio" name="role" value="customer" checked> Customer
                    </label>
                    <label style="font-weight: normal; font-size: 1rem;">
                        <input type="radio" name="role" value="owner"> Restaurant owner
                    </label>
                </div>
            </div>

            <button type="submit" style="width: 100%; margin-top: 1rem;">Register</button>
        </form>
        <p style="margin-top: 1.5rem; text-align: center;">
            <a href="{{ route('login') }}">Already have an account? Login</a>
        </p>
    </div>
@endsection