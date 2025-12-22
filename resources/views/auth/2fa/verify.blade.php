@extends('layouts.app')

@section('title', 'Two-Factor Verification')

@section('content')
    <div class="container">
        <h2>Two-Factor Verification</h2>

        @if ($errors->any())
            <div style="color: red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p>Enter the 6-digit code from your authenticator app:</p>

        <form method="POST" action="{{ route('2fa.verify.submit') }}">
            @csrf
            <div>
                <input type="text" name="code" id="code" maxlength="6" pattern="\d{6}" required autofocus
                    style="width: 150px; font-size: 24px; text-align: center; letter-spacing: 8px;">
            </div>
            <button type="submit">Verify</button>
        </form>
    </div>
@endsection