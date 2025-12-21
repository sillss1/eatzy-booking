@extends('layouts.app')

@section('title', 'My Reservations')

@section('content')
    <h2>My Reservations</h2>

    @if(Auth::user()->isOwner() && isset($restaurants) && $restaurants->count() > 1)
        <label for="restaurant_id">Select restaurant:</label>
        <select name="restaurant_id" id="restaurant_id">
            @foreach($restaurants as $r)
                <option value="{{ $r->id }}" {{ $selectedRestaurant == $r->id ? 'selected' : '' }}>
                    {{ $r->name }}
                </option>
            @endforeach
        </select>
    @endif

    <label for="search">Search:</label>
    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search reservations">
    
    <div class="filters filters-small">

        <label for="sort">Sort by:</label>
        <select name="sort" id="sort">
            <option value="">Default</option>
            <option value="restaurant_name" {{ request('sort') == 'restaurant_name' ? 'selected' : '' }}>Restaurant Name</option>
            <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Reservation Name</option>
            <option value="date" {{ request('sort') == 'date' ? 'selected' : '' }}>Reservation Date</option>
            <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>Status</option>
            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Creation Date</option>
        </select>

        <select name="direction" id="direction">
            <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
            <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descending</option>
        </select>

        <label for="status">Reservation status:</label>
        <select name="status" id="status">
            <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>All</option>
            <option value="current" {{ request('status', 'current') == 'current' ? 'selected' : '' }}>Current</option>
            <option value="past" {{ request('status') == 'past' ? 'selected' : '' }}>Past</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
        </select>
        <span class="tooltip" style="margin-left: 4px;">ⓘ
            <span class="tooltip-text">Statuses:
                - All - all reserervation statuses,
                - Current - reservations which are confirmed or pending,
                - Past - reservations which are cancelled or completed,
                - Pending - reservations which are awaiting restaurant's confirmation,
                - Confirmed - reservations which have been confirmed by the restaurant
                and are awaiting the reservation date,
                - Completed - reservations past reservation date,
                - Cancelled - reservations which have been cancelled (either by the 
                user or the restaurant).
            </span>
        </span>

    </div>

    <div id="reservation-container" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1.5rem;">
        <div id="reservation-list" style="flex: 3;">
            @include('reservations._list')
        </div>

        <div class="date-filter" style="flex: 1; display: flex; flex-direction: column; align-items: flex-start; gap: 0.5rem; margin-right: 250px;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <label for="filter-date" style="margin: 0;">Filter by date:</label> 
                <div class="tooltip" style="display: flex; align-items: center;">
                    ⓘ
                    <span class="tooltip-text">Show only reservations, 
                        which are book at a concrete date</span>
                </div>
            </div>
            <input type="date" id="filter-date" value="{{ request('date') }}">
            <button id="clear-date">Clear the date</button>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const reservationsIndexUrl = "{{ route('reservations.index') }}";
</script>
<script src="{{ asset('js/reservations.js') }}" defer></script>
@endpush

