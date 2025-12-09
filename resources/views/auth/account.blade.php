@extends('layouts.app')

@section('content')
<div class="container">
    @if($user->profile_picture)
        <img src="{{ asset('storage/' . $user->profile_picture) }}"
             alt="Profile Picture"
             class="profile-avatar">
    @else
        <img src="{{ asset('storage/restaurant_photos/default_pfp.jpg') }}"
             alt="Default Avatar"
             class="profile-avatar">
    @endif

    <h1 style="margin-bottom: 3px;">{{ $user->username }}</h1>
    <h3 style="margin-bottom: 30px;">Joined at: {{ $user->joined_at }}</h3>

    <p style="margin-bottom: 5px;"><strong>Name:</strong> {{ $user->name }} {{ $user->surname }}</p>
    <p style="margin-top: 5px;"><strong>Description:</strong> {{ $user->profile_description ?? 'No description' }}</p>

    @auth
        @if(Auth::id() === $user->id)
            <br>
            <a href="{{ route('account.edit') }}">
                <button>Edit Profile / Account details</button>
            </a>
        @endif
    @endauth
    
</div>
@endsection
