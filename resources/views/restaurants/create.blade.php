@extends('layouts.app')

@section('title', 'Add Restaurant')

@section('content')
    <h2>Add Restaurant</h2>

    <form method="POST" action="{{ route('restaurants.store') }}">
        @csrf
        @include('restaurants._form')
        <button type="submit">Create Restaurant</button>
    </form>
@endsection
