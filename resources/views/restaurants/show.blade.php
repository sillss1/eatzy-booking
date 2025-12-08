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
                Edit Restaurant Details
            </a>
        </p>
        @endif
    @endauth

    <h3>Photos</h3>

    @if($restaurant->photos->isEmpty())
        <p>No photos available.</p>
    @else
        <div class="photo-gallery">
            @foreach ($restaurant->photos as $photo)
                <img src="{{ asset('storage/' . $photo->link) }}" alt="{{ $restaurant->name }}" class="restaurant-photo">
            @endforeach
        </div>
    @endif

    @auth
        @if(auth()->user()->isOwner() && auth()->user()->id === $restaurant->owner_id)
            <h3>Upload photos</h3>

            <form action="{{ route('restaurants.photos.store', $restaurant->id) }}" 
                method="POST" 
                enctype="multipart/form-data">
                @csrf
                
                <input type="file" name="photos[]" multiple accept="image/*">
                
                <button type="submit" class="button">Upload</button>
            </form>
        @endif
    @endauth

@endsection