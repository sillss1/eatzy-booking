@if ($restaurants->isEmpty())
    <p>No restaurants found.</p>
@else
    <ul style="list-style: none; padding: 0;">
        @foreach ($restaurants as $restaurant)
             <li style="display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1.5rem; border-bottom: 1px solid #ddd; padding-bottom: 1rem;">
                
                <div style="flex: 1;">
                    <a href="{{ route('restaurants.show', $restaurant->id) }}">
                        <strong>{{ $restaurant->name }}</strong>
                    </a><br>
                    <span>{{ \Illuminate\Support\Str::limit($restaurant->description, ) }}</span><br>
                    <small>{{ $restaurant->address }}</small>
                 </div>
                
                @include('restaurants._photos_small')

            </li>
        @endforeach
    </ul>
@endif
