@extends('layouts.app')

@section('title', 'Restaurants')

@section('content')
    <h2>Restaurants</h2>

    <label for="search">Search Restaurants:</label>
    <input type="text" id="search" placeholder="Search by name, description or address">

    <div id="restaurant-list">
        @include('restaurants._list', ['restaurants' => $restaurants])
    </div>
@endsection

@push('scripts')
<script>
const searchInput = document.querySelector('#search');
const restaurantList = document.querySelector('#restaurant-list');

let timeout = null;

searchInput.addEventListener('input', function() {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        const query = new URLSearchParams({ search: searchInput.value });
        fetch("{{ route('restaurants.index') }}?" + query, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(r => r.json())
        .then(data => {
            restaurantList.innerHTML = data.html;
        });}, 225); 
});
</script>
@endpush