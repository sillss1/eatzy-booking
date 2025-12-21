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
             @include('restaurants._add_favourite', ['restaurant' => $restaurant])
        @endif

        @if(auth()->user()->isOwner() && auth()->user()->id === $restaurant->owner_id)
            <p>
                <a class="button" href="{{ route('restaurants.edit', $restaurant->id) }}">
                    Edit Restaurant Details
                </a>
            </p>
        @endif
    @endauth
    
    <div style="display: flex; align-items: center; gap: 0.5rem;">
        <h2 style="margin: 0;">Photos</h2>
        @auth
            @if(Auth::user()->isOwner())
                <div class="tooltip" style="display: flex; align-items: center;">
                    ⓘ
                    <span class="tooltip-text">These photos are visible to all EatZy Booking users</span>
                </div>
            @endif
            @if(!Auth::user()->isOwner())
                <div class="tooltip" style="display: flex; align-items: center;">
                    ⓘ
                    <span class="tooltip-text">These photos have been added to this restaurant's
                        description by its owner
                    </span>
                </div>
            @endif
        @endauth
        @guest
            <div class="tooltip" style="display: flex; align-items: center;">
                ⓘ
                <span class="tooltip-text">These photos have been added to this restaurant's
                    description by its owner</span>
            </div>
        @endguest
    </div>
    
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

