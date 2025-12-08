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
                    <span>{{ \Illuminate\Support\Str::limit($restaurant->description, 180) }}</span><br>
                    <small>{{ $restaurant->address }}</small>
                 </div>

                @if($restaurant->photos->isNotEmpty())
                    <div style="display: flex; flex-direction: row; gap: 0.5rem;">
                        @foreach($restaurant->photos->take(5) as $photo)
                            <img src="{{ asset('storage/' . $photo->link) }}" 
                                 alt="{{ $photo->title }}" 
                                 style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px;">
                        @endforeach
                    </div>
                @endif

            </li>
        @endforeach
    </ul>
@endif
