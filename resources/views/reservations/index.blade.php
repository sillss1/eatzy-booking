@extends('layouts.app')

@section('title', 'My Reservations')

@section('content')
    <h2>My Reservations</h2>

    <form method="GET" action="{{ route('reservations.index') }}">
    @if(Auth::user()->isOwner() && isset($restaurants) && $restaurants->count() > 1)
        <label for="restaurant_id">Select restaurant:</label>
        <select name="restaurant_id" id="restaurant_id" onchange="this.form.submit()">
            @foreach($restaurants as $r)
                <option value="{{ $r->id }}" {{ $selectedRestaurant == $r->id ? 'selected' : '' }}>
                    {{ $r->name }}
                </option>
            @endforeach
        </select>
    @endif

    <label for="search">Search:</label>
    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search reservations">

    <label for="sort">Sort by:</label>
    <select name="sort" id="sort" onchange="this.form.submit()">
        <option value="">Default</option>
        <option value="restaurant_name" {{ request('sort') == 'restaurant_name' ? 'selected' : '' }}>Restaurant Name</option>
        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Reservation Name</option>
        <option value="date" {{ request('sort') == 'date' ? 'selected' : '' }}>Reservation Date</option>
        <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>Status</option>
        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Creation Date</option>
    </select>
    <select name="direction" id="direction" onchange="this.form.submit()">
        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descending</option>
    </select>
    <label for="status">Reservation status:</label>
    <select name="status" id="status" onchange="this.form.submit()">
        <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>All</option>
        <option value="current" {{ request('status', 'current') == 'current' ? 'selected' : '' }}>Current</option>
        <option value="past" {{ request('status') == 'past' ? 'selected' : '' }}>Past</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
    </select>

    <button type="submit">Search</button>
    </form>

    @if ($reservations->isEmpty())
        <p>You have no reservations.</p>
    @else
        <ul>
            @foreach ($reservations as $reservation)
                <li style="margin-bottom: 1rem;">
                    <a href="{{ route('reservations.show', $reservation->id) }}">
                        @if(Auth::user()->isOwner() && $reservation->restaurant->owner_id == Auth::id())
                            <strong>Reservation by {{ $reservation->user->username }} </strong><br>
                            <small> Name: {{ $reservation->user->name }} {{ $reservation->user->surname }}</small><br>
                        @else
                            <strong>{{ $reservation->title }}</strong><br>
                        @endif
                    </a>

                    @if (Auth::user()->isCustomer())
                    <span>
                        At:
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
                        Created at: {{ $reservation->created_at }} 
                    </small>

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
@endsection