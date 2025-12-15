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

    </div>

    <div id="reservation-container" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1.5rem;">
        <div id="reservation-list" style="flex: 3;">
            @include('reservations._list')
        </div>

        <div class="date-filter" style="flex: 1; display: flex; flex-direction: column; align-items: flex-start; gap: 0.5rem; margin-right: 250px;">
            <label for="filter-date">Filter by date:</label>
            <input type="date" id="filter-date" value="{{ request('date') }}">
            <button id="clear-date">Clear the date</button>
        </div>
    </div>

</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    const dateInput = document.getElementById('filter-date');
    const clearButton = document.getElementById('clear-date');

    function loadReservations() {
        const params = new URLSearchParams();
        const fields = ['restaurant_id', 'search', 'sort', 'direction', 'status'];

        fields.forEach(id => {
            const el = document.getElementById(id);
            if (el) params.append(id, el.value);
        });

        if (dateInput && dateInput.value) {
            params.append('date', dateInput.value);
        }

        fetch("{{ route('reservations.index') }}?" + params.toString(), {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(r => r.json())
        .then(data => {
            document.querySelector('#reservation-list').innerHTML = data.html;
        });
    }
    
    if (dateInput) {
        dateInput.addEventListener('change', loadReservations);
    }

    if (clearButton) {
        clearButton.addEventListener('click', () => {
            dateInput.value = '';
            loadReservations();
        });
    }

    document.querySelectorAll('.filters select').forEach(select => {
        select.addEventListener('change', loadReservations);
    });

    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(loadReservations, 225));
    }

    function debounce(fn, delay) {
        let timeout;
        return function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => fn.apply(this, arguments), delay);
        }
    }

    loadReservations();
});
</script>
@endpush
