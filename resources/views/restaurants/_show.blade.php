<div class="restaurant-card">
    <div>
        <h2>
            @auth
                @if(auth()->user()->favouriteRestaurants()->where('restaurant_id', $restaurant->id)->exists())
                    <span title="In your favourites">❤️</span>
                @endif
            @endauth
            {{ $restaurant->name }}
        </h2>
        <p><strong>Address:</strong> {{ $restaurant->address }}</p>
        <p>{{ $restaurant->description }}</p>
    </div>
    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 2rem;">
        <div style="flex: 1;"> @include('restaurants._opening_hours') </div>
        
        <div style="flex: 1;"> 
            <h4>Restaurant's photos</h4>
            @include('restaurants._photos_small') </div>
    </div>
</div>