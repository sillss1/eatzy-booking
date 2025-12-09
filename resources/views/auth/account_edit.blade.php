@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('account') }}" class="button button-outline">
    ← Back to Profile
    </a>

    <h2>Edit Profile</h2>

    @if($errors->any())
        <div style="color: red;">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('account.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div>
            <label>Name:</label><br>
            <input type="text" name="name" value="{{ old('name', $user->name) }}">
        </div>

        <div>
            <label>Surname:</label><br>
            <input type="text" name="surname" value="{{ old('surname', $user->surname) }}">
        </div>

        <label>Profile picture:</label><br>
        <input type="file" name="profile_picture" accept="image/*">
        @error('profile_picture')
            <div style="color:red;">{{ $message }}</div>
        @enderror

        @if($user->profile_picture)
            <div>
                <label>
                    <input type="checkbox" name="remove_picture">
                    Remove current picture
                </label>
            </div>
        @endif

        <div>
            <label>Description:</label><br>
            <textarea name="profile_description" rows="3">{{ old('profile_description', $user->profile_description) }}</textarea>
        </div>

        <br>
        <button type="submit">Save changes</button>
    </form>

    <hr>
    <h2>Danger Zone</h2>
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
