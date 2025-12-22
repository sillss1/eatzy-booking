@extends('layouts.app')

@section('content')
    <div class="container">

        <a href="{{ route('account.me') }}" class="button button-outline">
            ← Back to Profile
        </a>

        @if(session('success'))
            <div style="color: green; font-weight: 500; margin-bottom: 10px;">
                {{ session('success') }}
            </div>
        @endif

        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <h2 style="margin: 0;">Edit Profile</h2>
            <div class="tooltip" style="display: flex; align-items: center;">
                ⓘ
                <span class="tooltip-text">All of these information, except your email,
                    are shown to the people visiting your profile.
                </span>
            </div>
        </div>
        @if($errors->any())
            <div style="color: red;">{{ $errors->first() }}</div>
        @endif

        <div>
            <label>Profile picture:</label>
            @if(Auth::user()->profile_picture)
                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile Picture"
                    class="profile-avatar-small">
                <br>

                <form action="{{ route('account.remove_picture') }}" method="POST"
                    style="display: inline-block; margin-top: 6px;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="button button-outline" style="margin-bottom: 0px;"
                        onclick="return confirm('Are you sure you want to remove your profile picture?');">
                        Remove Picture
                    </button>
                </form>
            @else
                <img src="{{ asset('storage/restaurant_photos/default_pfp.jpg') }}" alt="Default Avatar"
                    class="profile-avatar-small">
                <br>
            @endif
        </div>

        <form id="profile-form" action="{{ route('account.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="file" name="profile_picture" accept="image/*">

            <div>
                <label>* Name:</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}">
            </div>

            <div>
                <label>* Surname:</label>
                <input type="text" name="surname" value="{{ old('surname', $user->surname) }}">
            </div>

            <div>
                <label>Description:</label>
                <textarea name="profile_description"
                    rows="3">{{ old('profile_description', $user->profile_description) }}</textarea>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <button type="button" id="toggle-account-edit" class="button button-outline" style="margin: 0;"> ⮟ Edit
                    Account Details </button>
                <div class="tooltip" style="display: flex; align-items: center;">
                    ⓘ
                    <span class="tooltip-text">- Your username is your main identificator on the site.
                        It is visible when you book a table or review a restaurant.
                        - Your email is a crucial part of the authentication process.

                        *Both have to be unqiue
                    </span>
                </div>
            </div>

            <div id="account-edit-fields" class="account-edit-fields">
                <div>
                    <label>* Username:</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}">
                    @error('username')
                        <div style="color:red;">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label>* Email:</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}">
                    @error('email')
                        <div style="color:red;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <br>
            <button type="submit">Save changes</button>
        </form>

        <hr>

        <h2>Danger Zone</h2>
        <form action="{{ route('account.delete') }}" method="POST"
            onsubmit="return confirm('Are you sure you want to delete your account?');">
            @csrf
            @method('DELETE')
            <button type="submit" style="background-color: red; color: white; border-color: darkred;">
                Delete My Account
            </button>
        </form>

    </div>

@endsection

<script src="{{ asset('js/profile-edit.js') }}"></script>