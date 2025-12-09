@extends('layouts.app')

@section('content')
<div class="container">
    <h1>My Profile</h1>

    <p><strong>Name:</strong> {{ $user->name }}</p>
    <p><strong>Surname:</strong> {{ $user->surname }}</p>
    <p><strong>Description:</strong> {{ $user->profile_description ?? 'No description' }}</p>

    {{-- @if($user->profile_picture)
        <img src="{{ asset('storage/' . $user->profile_picture) }}" width="200">
    @endif --}}

    <br>
    <a href="{{ route('account.edit') }}">
        <button>Edit Profile</button>
    </a>

    <hr>

    <h3>Danger Zone</h3>
    <form action="{{ route('user.delete') }}" method="POST"
          onsubmit="return confirm('Are you sure you want to delete your account?');">
        @csrf
        @method('DELETE')
        <button type="submit" style="background-color: red; color: white; border-color: darkred;">
            Delete My Account
        </button>
    </form>
</div>
@endsection
