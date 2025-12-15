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
    const searchInput = document.querySelector('#search');
    const restaurantList = document.querySelector('#restaurant-list');
    const sortSelect = document.querySelector('#sort');
    const directionSelect = document.querySelector('#direction');
    const favouritesCheckbox = document.querySelector('#only-favourites');

    function loadRestaurants() {
        const params = new URLSearchParams({
            search: searchInput.value,
            sort: sortSelect?.value,
            direction: directionSelect?.value,
            only_favourites: favouritesCheckbox?.checked ? 1 : 0
        });

        fetch("{{ route('restaurants.index') }}?" + params, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(r => r.json())
        .then(data => {
            restaurantList.innerHTML = data.html;
        });
    }

    searchInput.addEventListener('input', loadRestaurants);
    sortSelect.addEventListener('change', loadRestaurants);
    directionSelect.addEventListener('change', loadRestaurants);
    favouritesCheckbox.addEventListener('change', loadRestaurants);
</script>
@endpush