@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
    <div class="container">
        <h2>Reset Password</h2>

        @if ($errors->any())
            <div style="color: red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            <div>
                <label for="password">New Password</label>
                <input type="password" name="password" id="password" required minlength="8">
            </div>
            <div>
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
            </div>
            <button type="submit">Reset Password</button>
        </form>
    </div>
@endsection