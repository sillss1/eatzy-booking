@extends('layouts.app')

@section('title', $restaurant->name)

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 2rem;">
        <div style="flex: 1;">
            <h2>{{ $restaurant->name }}</h2>
            Restaurant description
            <p>{{ $restaurant->description }}</p>
            
            Restaurant addres: <p>{{ $restaurant->address }}</p>
        </div>

        <div style="flex: 1;">
            @include('restaurants._opening_hours')
        </div>
    </div>
    
    @auth
        @if(Auth::user()->isCustomer())
            <a href="{{ route('reservations.create', $restaurant->id) }}" class="button">
                Book a table
            </a>
        @endif
        @if(auth()->user()->isOwner() && auth()->user()->id === $restaurant->owner_id)
            <p>
                <a class="button" href="{{ route('restaurants.edit', $restaurant->id) }}">
                    Edit Restaurant Details
                </a>
            </p>
        @endif
    @endauth

    <h2>Photos</h2>
    
    @include('restaurants._photos', ['photos' => $restaurant->photos])

    @auth
        @if(auth()->user()->isOwner() && auth()->user()->id === $restaurant->owner_id)
            <a href="{{ route('restaurants.photos.edit', $restaurant->id) }}" class="button">
                Edit Photos
            </a>
        @endif
    @endauth

    <h2>Reviews</h2>

    @include('reviews._section')
@endsection
