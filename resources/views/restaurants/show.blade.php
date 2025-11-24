@extends('layouts.app')

@section('title', $restaurant->name)


@section('content')
    <h2>{{ $restaurant->name }}</h2>
    <p>{{ $restaurant->description }}</p>
    <p>{{ $restaurant->address }}</p>
    @include('restaurants._opening_hours')
    
    @auth
        @if(Auth::user()->isCustomer())
            <a href="{{ route('reservations.create', $restaurant->id) }}" class="button">
                Book a table
            </a>
        @endif
        @if(auth()->user()->isOwner() && auth()->user()->id === $restaurant->owner_id)
        <p>
            <a class="button" href="{{ route('restaurants.edit', $restaurant->id) }}">
                Edit Restaurant
            </a>
        </p>
        @endif
    @endauth

@endsection