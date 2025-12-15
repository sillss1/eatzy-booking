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

    <script>
        document.querySelector('.toggle-favourite').addEventListener('click', function () {
            let restaurantId = this.dataset.id;

            fetch(`/restaurants/${restaurantId}/favourite`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                this.textContent = data.favourite ? '❤️' : '🤍';
                this.title = data.favourite ? 'Remove from favourites' : 'Add to favourites';
            });
        });
    </script>

    @include('reviews._section')
@endsection

