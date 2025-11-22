@extends('layouts.app')

@section('title', 'My Reservations')

@section('content')
    <h2>My Reservations</h2>

    <form method="GET" action="{{ route('reservations.index') }}">
    <label for="sort">Sort by:</label>
    <select name="sort" id="sort" onchange="this.form.submit()">
        <option value="">Default</option>
        <option value="restaurant_name" {{ request('sort') == 'restaurant_name' ? 'selected' : '' }}>Restaurant Name</option>
        <option value="date" {{ request('sort') == 'date' ? 'selected' : '' }}>Reservation Date</option>
        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Reservation Name</option>
        <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>Status</option>
        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Creation Date</option>
    </select>

    <select name="direction" id="direction" onchange="this.form.submit()">
        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descending</option>
    </select>

    <label>
        <input type="checkbox" name="include_past" value="1"
            onchange="this.form.submit()"
            {{ request('include_past') ? 'checked' : '' }}>
        Include past reservations
    </label>

    </form>

    @if ($reservations->isEmpty())
        <p>You have no reservations.</p>
    @else
        <ul>
            @foreach ($reservations as $reservation)
                <li style="margin-bottom: 1rem;">

                    <a href="{{ route('reservations.show', $reservation->id) }}">
                        <strong>{{ $reservation->title ?? 'Reservation' }}</strong>
                    </a><br>

                    <span>
                        At:
                        <a href="{{ route('restaurants.show', $reservation->restaurant->id) }}">
                            {{ $reservation->restaurant->name }}
                        </a>
                    </span><br>

                    <small>
                        {{ $reservation->date_of_visit }} at {{ $reservation->time_of_visit }} —
                        {{ $reservation->number_of_people }} people
                    </small><br>

                    <small>
                        Status: {{ ucfirst($reservation->status) }}
                    </small><br>

                    <small>
                        Created at: {{ $reservation->created_at }} 
                    </small>
                </li>
            @endforeach
        </ul>
    @endif
@endsection