@if ($restaurants->isEmpty())
    <div class="empty-state">
        @auth
            @if(Auth::user()->isOwner())
                <h3>🏪 No Restaurants Yet</h3>
                <p>You haven't created any restaurants yet. Start building your culinary empire!</p>
                <a href="{{ route('restaurants.create') }}" class="button">+ Create Your First Restaurant</a>
            @else
                <h3>🔍 No Restaurants Found</h3>
                <p>Try adjusting your search or filters to find what you're looking for.</p>
            @endif
        @else
            <h3>🔍 No Restaurants Found</h3>
            <p>We couldn't find any restaurants matching your criteria. Try a different search.</p>
        @endauth
    </div>
@else
    <div class="restaurant-list">
        @foreach ($restaurants as $restaurant)
            <article class="restaurant-card">
                @auth
                    @if(auth()->user()->favouriteRestaurants()->where('restaurant_id', $restaurant->id)->exists())
                        <span class="badge badge-success" style="position: absolute; top: 1rem; right: 1rem;">❤️ Favourite</span>
                    @endif
                @endauth

                <h3>
                    <a href="{{ route('restaurants.show', $restaurant->id) }}" style="color: inherit; text-decoration: none;">
                        <strong>{{ $restaurant->name }}</strong>
                    </a>
                </h3>

                <p class="meta">📍 {{ $restaurant->address }}</p>

                <p>{{ \Illuminate\Support\Str::limit($restaurant->description, 120) }}</p>

                @include('restaurants._photos_small')

                <div class="actions">
                    <a href="{{ route('restaurants.show', $restaurant->id) }}" class="button">View Details</a>
                    @auth
                        @if(Auth::user()->isCustomer())
                            <a href="{{ route('reservations.create', $restaurant->id) }}" class="button"
                                style="background: linear-gradient(135deg, #6366f1, #4f46e5);">Make Reservation</a>
                        @endif
                    @endauth
                </div>
            </article>
        @endforeach
    </div>

    <div style="margin-top: 2rem;">
        {{ $restaurants->links() }}
    </div>
@endif