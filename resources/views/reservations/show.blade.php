@extends('layouts.app')

@section('title', 'Reservation Details')

@section('content')
<a href="{{ route('reservations.index') }}" class="button">Back to My Reservations</a>

<h2>Reservation Details</h2>

<div class="reservation-card">
    <h3>{{$reservation->title}}</h3>
    <p><strong>At:</strong> 
        <a href="{{ route('restaurants.show', $reservation->restaurant->id) }}">
            {{ $reservation->restaurant->name }}
        </a>
    </p>
    <p><strong>Date & Time:</strong> {{ $reservation->date_of_visit }} at {{ $reservation->time_of_visit }}</p>
    <p><strong>Number of People:</strong> {{ $reservation->number_of_people }}</p>
    <p><strong>Description:</strong> {{ $reservation->description ?? '-' }}</p>
    <p><strong>Status:</strong> {{ ucfirst($reservation->status) }}</p>
    <p><strong>Created At:</strong> {{ $reservation->created_at }}</p>
    @if ($reservation->edited_at)
    <p><strong>Edited At:</strong> {{ $reservation->edited_at }}</p>
    @endif
</div>

@if ((Auth::id() === $reservation->user_id || Auth::user()->isAdmin()) && $reservation->is_cancellable)
    <form action="{{ route('reservations.cancel', $reservation->id) }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" onclick="return confirm('Are you sure you want to cancel this reservation? This action cannot be reversed')">
            Cancel Reservation
        </button>
    </form>
@endif

@if (($reservation->user_id === Auth::id() || Auth::user()->isAdmin()) && $reservation->is_editable)
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

<h2>Restaurant Info</h2>
@include('restaurants._show', ['restaurant' => $reservation->restaurant])
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