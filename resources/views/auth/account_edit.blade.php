@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Profile</h1>

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

        <div>
            <label>Description:</label><br>
            <textarea name="profile_description" rows="3">{{ old('profile_description', $user->profile_description) }}</textarea>
        </div>
{{-- 
        <div>
            <label>Profile picture (optional):</label><br>
            <input type="file" name="profile_picture" accept="image/*">
        </div>
--}}
        <br>
        <button type="submit">Save changes</button>
    </form>

    <br>
    <a href="{{ route('account') }}">Back to Profile</a>

</div>
@endsection
