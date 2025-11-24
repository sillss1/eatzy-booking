@if ($restaurants->isEmpty())
    <p>No restaurants found.</p>
@else
    <ul>
        @foreach ($restaurants as $restaurant)
            <li style="margin-bottom: 1rem;">
                <a href="{{ route('restaurants.show', $restaurant->id) }}">
                    <strong>{{ $restaurant->name }}</strong>
                </a><br>
                <span>{{ \Illuminate\Support\Str::limit($restaurant->description, 120) }}</span><br>
                <small>{{ $restaurant->address }}</small>
            </li>
        @endforeach
    </ul>
@endif
