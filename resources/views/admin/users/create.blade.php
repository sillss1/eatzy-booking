@extends('layouts.app')

@section('title', 'Create User')

@section('content')
    <div class="container">
        <h2>Create New User</h2>
        <p><a href="{{ route('admin.users') }}">&larr; Back to User List</a></p>

        @if ($errors->any())
            <div style="color: red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div>
                <label for="name">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required>
            </div>
            <div>
                <label for="surname">Surname</label>
                <input type="text" name="surname" id="surname" value="{{ old('surname') }}" required>
            </div>
            <div>
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}" required>
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required>
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required minlength="8">
            </div>
            <div>
                <label for="role">Role</label>
                <select name="role" id="role" required>
                    <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                    <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Owner</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <button type="submit">Create User</button>
        </form>
    </div>
@endsection