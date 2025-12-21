@if ($reservations->isEmpty())
        <p>You have no reservations.</p>
    @else
        <ul>
            @foreach ($reservations as $reservation)
                <li style="margin-bottom: 1rem;">
                        @if(Auth::user()->isOwner() && $reservation->restaurant->owner_id == Auth::id())
                            <a href="{{ route('reservations.show', $reservation->id) }}">
                                <strong>{{ $reservation->title }}</strong>
                            </a>
                            <strong> by </strong>
                            <a href="{{ route('account.view', ['id' => $reservation->user->id]) }}">
                                <strong>{{ $reservation->user->username }}</strong><br>
                            </a>
                            <small> Name: {{ $reservation->user->name }} {{ $reservation->user->surname }}</small><br>
                        @else
                            <a href="{{ route('reservations.show', $reservation->id) }}">
                                <strong>{{ $reservation->title }}</strong><br>
                            </a>
                        @endif

                    @if (Auth::user()->isCustomer())
                    <span>
                        At: 
                        @if(Auth::user()->favouriteRestaurants()->where('restaurant_id', $reservation->restaurant->id)->exists())
                            <span title="In your favourites">❤️</span>
                        @endif
                        <a href="{{ route('restaurants.show', $reservation->restaurant->id) }}">
                            {{ $reservation->restaurant->name }}
                        </a>
                    </span><br>
                    @endif

                    <small>
                        {{ $reservation->date_of_visit }} at {{ $reservation->time_of_visit }} —
                        {{ $reservation->number_of_people }} people
                    </small><br>
                    
                    @if ($reservation->restaurant->owner_id == Auth::id())
                    @php
                        $key = $reservation->restaurant_id.'|'.$reservation->date_of_visit;
                        $taken = $capacityMap[$key] ?? 0;
                        $total = $reservation->restaurant->capacity;
                        $left = max($total - $taken, 0);
                    @endphp

                    <small>
                        Current restaurant capacity: {{ $left }} / {{ $total }}
                    </small><br>
                    @endif

                    @if(Auth::user()->isOwner() && $reservation->restaurant->owner_id == Auth::id() && $reservation->description)
                        <div>
                            Description: {{ \Illuminate\Support\Str::limit($reservation->description, 100) }}
                        </div>
                    @endif

                    <small>
                        Status: {{ ucfirst($reservation->status) }}
                    </small><br>

                    <small>
                        Created at: {{ substr($reservation->created_at, 0, 16) }}
                    </small><br>

                    @if(Auth::user()->isOwner() && $reservation->restaurant->owner_id == Auth::id())
                        @if($reservation->status === 'pending')
                            <form method="POST" action="{{ route('reservations.confirm', $reservation->id) }}" style="display:inline">
                                @csrf
                                <button type="submit" onclick="return confirm('Are you sure you want to confirm this reservation?')">Confirm</button>
                            </form>
                            <form method="POST" action="{{ route('reservations.cancel', $reservation->id) }}" style="display:inline">
                                @csrf
                                <button type="submit" onclick="return confirm('Are you sure you want to refuse this reservation?')">Refuse</button>
                            </form>
                        @elseif($reservation->status === 'confirmed')
                            <form method="POST" action="{{ route('reservations.cancel', $reservation->id) }}" style="display:inline">
                                @csrf
                                <button type="submit" onclick="return confirm('Are you sure you want to refuse this reservation?')">Refuse</button>
                            </form>
                        @endif
                    @endif

                </li>
            @endforeach
        </ul>
    @endif