@extends('layouts.app')

@section('title', $restaurant->name)


@section('content')
    <h2>{{ $restaurant->name }}</h2>
    <p>{{ $restaurant->description }}</p>
    <p>{{ $restaurant->address }}</p>

    @auth
        @if(Auth::user()->isCustomer())
            <a href="{{ route('reservations.create', $restaurant->id) }}" class="button">
                Book a table
            </a>
        @endif
    @endauth

@endsection