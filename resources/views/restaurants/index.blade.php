@extends('layouts.app')

@section('title', 'Restaurants')

@section('content')
    <h2>Restaurants</h2>

    <label for="search">Search Restaurants:</label>
    <input type="text" id="search" placeholder="Search by name, description or address">

    <div class="filters filters-small">
        <label for="sort">Sort by:</label>
        <select id="sort">
            <option value="name">Name</option>
            <option value="address">Address</option>
            <option value="description">Description</option>
            
            @auth
                @if(Auth::user()->isAdmin())
                    <option value="capacity">Capacity</option>
                    <option value="created_at">Creation Date</option>
                @endif
            @endauth
        </select>

        <select id="direction">
            <option value="asc">Ascending</option>
            <option value="desc">Descending</option>
        </select>

        @auth
            @if(Auth::user()->isCustomer())
            <label>
                <input type="checkbox" id="only-favourites" {{ request('only_favourites') ? 'checked' : '' }}>
                Show only favourites
            </label>
            @endif
        @endauth
    </div>

    <div id="restaurant-list">
        @include('restaurants._list', ['restaurants' => $restaurants])
    </div>
@endsection

@push('scripts')
<script>
    window.restaurantIndexUrl = "{{ route('restaurants.index') }}";
</script>
<script src="{{ asset('js/restaurant-filters.js') }}" defer></script>
@endpush