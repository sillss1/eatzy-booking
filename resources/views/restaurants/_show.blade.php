<div class="restaurant-card">
    <h2>{{ $restaurant->name }}</h2>
    <h3><strong>Address:</strong> {{ $restaurant->address }}</h3>
    <h3>{{ $restaurant->description }}</h3>
    @include('restaurants._opening_hours')
</div>