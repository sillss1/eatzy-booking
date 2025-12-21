@extends('layouts.app')

@section('title', 'Reservation Details')

@section('content')
<a href="{{ route('reservations.index') }}" class="button">Back to My Reservations</a>

<h1>Reservation Details</h1>
<br>

<div class="reservation-card">
    <h2><strong>{{$reservation->title}}</strong> @if (Auth::user()->isOwner()) by    
        <a href="{{ route('account.view', ['id' => $reservation->user->id]) }}" title="View profile">
            {{ $reservation->user->username }}
        </a>
        @endif
    </h2> 

    <p><strong>At:</strong> 
        <a href="{{ route('restaurants.show', $reservation->restaurant->id) }}">
            {{ $reservation->restaurant->name }}
        </a>
    </p>
    <p><strong>Date & Time:</strong> {{ $reservation->date_of_visit }} at {{ $reservation->time_of_visit }}</p>
    <p><strong>Number of People:</strong> {{ $reservation->number_of_people }}</p>
    @if ($reservation->restaurant->owner_id == Auth::id())
        @php
            $key = $reservation->restaurant_id.'|'.$reservation->date_of_visit;
            $taken = $capacityMap[$key] ?? 0;
            $total = $reservation->restaurant->capacity;
            $left = max($total - $taken, 0);
        @endphp

    <p><strong>Current restaurant capacity: </strong>{{ $left }}/{{ $total }}</p>
    @endif
    <p><strong>Description:</strong> {{ $reservation->description ?? '-' }}</p>
    <p><strong>Status:</strong> {{ ucfirst($reservation->status) }}</p>
    <p><strong>Created At:</strong> {{ substr($reservation->created_at, 0, 16) }}</p>
    @if ($reservation->edited_at)
        <p><strong>Edited At:</strong> {{ substr($reservation->edited_at, 0, 16) }}</p>
    @endif
</div>

@if (Auth::id() === $reservation->user_id && $reservation->is_modifiable)
    <form action="{{ route('reservations.cancel', $reservation->id) }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" class="button" onclick="return confirm('Are you sure you want to cancel this reservation? This action cannot be reversed')">
            Cancel Reservation
        </button>
    </form>
@elseif($reservation->is_modifiable && (Auth::user()->isAdmin() || (Auth::user()->isOwner() && $reservation->restaurant->owner_id == Auth::id())))
    @if($reservation->status === 'pending')
        <form method="POST" action="{{ route('reservations.confirm', $reservation->id) }}" style="display:inline">
            @csrf
            <button type="submit" class="button" onclick="return confirm('Are you sure you want to confirm this reservation?')">
                Confirm
            </button>
        </form>
        <form method="POST" action="{{ route('reservations.cancel', $reservation->id) }}" style="display:inline">
            @csrf
            <button type="submit" class="button" onclick="return confirm('Are you sure you want to cancel this reservation?')">
                Refuse
            </button>
        </form>
    @elseif($reservation->status === 'confirmed')
        <form method="POST" action="{{ route('reservations.cancel', $reservation->id) }}" style="display:inline">
            @csrf
            <button type="submit"class="button" onclick="return confirm('Are you sure you want to cancel this reservation?')">
                Refuse
            </button>
        </form>
    @endif
@endif


@if (($reservation->user_id === Auth::id() || Auth::user()->isAdmin()) && $reservation->is_modifiable)
    <form action="{{ route('reservations.edit', $reservation->id) }}" method="GET" style="display:inline;">
        <button type="submit">Edit Reservation</button>
    </form>
@endif

@if ((Auth::id() === $reservation->user_id || Auth::user()->isAdmin()) && $reservation->is_deletable)
    <form action="{{ route('reservations.destroy', $reservation->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" onclick="return confirm('Are you sure you want to delete this reservation? This action cannot be reversed')">
            Delete Reservation
        </button>
    </form>
@endif

@if (Auth::id() === $reservation->user_id || Auth::user()->isAdmin())
<h2>Restaurant Info</h2>
<a href="{{ route('restaurants.show', $reservation->restaurant->id) }}" style="text-decoration: none; color: inherit;">
    @include('restaurants._show', ['restaurant' => $reservation->restaurant])
</a>
@endif

@endsection
@if ($errors->any())
    <div>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif