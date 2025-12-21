@extends('layouts.app')

@section('title', 'Create Restaurant')

@section('content')
    <div class="container">
        <h1>Create Restaurant</h1>

        @if($errors->any())
            <div style="color: red; margin-bottom: 15px;">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.restaurants.store') }}">
            @csrf

            <div>
                <label>Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>

            <div>
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div>
                <label>Address</label>
                <input type="text" name="address" value="{{ old('address') }}" required>
            </div>

            <div>
                <label>Capacity</label>
                <input type="number" name="capacity" min="1" value="{{ old('capacity') }}" required>
            </div>

            <div>
                <label>Description</label>
                <textarea name="description" required>{{ old('description') }}</textarea>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="button">Create Restaurant</button>
                <a href="{{ route('admin.resources') }}" class="button button-outline">Cancel</a>
            </div>
        </form>
    </div>
@endsection