@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <div class="container">
        <h1>Edit User</h1>

        @if($errors->any())
            <div style="color: red; margin-bottom: 15px;">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PUT')

            <div>
                <label>Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
            </div>

            <div>
                <label>Surname</label>
                <input type="text" name="surname" value="{{ old('surname', $user->surname) }}" required>
            </div>

            <div>
                <label>Username</label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" required>
            </div>

            <div>
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="button">Save Changes</button>
                <a href="{{ route('admin.users') }}" class="button button-outline">Cancel</a>
            </div>
        </form>
    </div>
@endsection