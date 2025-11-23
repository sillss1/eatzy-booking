@extends('layouts.app')

@section('title', 'Edit Restaurant')

@section('content')
    <h2>Edit Restaurant</h2>

    <form method="POST" action="{{ route('restaurants.update', $restaurant->id) }}">
        @csrf
        @method('PUT')
        @include('restaurants._form')
        <button type="submit">Save changes</button>
    </form>

    <form method="POST" action="{{ route('restaurants.destroy', $restaurant->id) }}">
        @csrf
        @method('DELETE')
        <button type="submit">Delete Restaurant</button>
    </form>
@endsection
