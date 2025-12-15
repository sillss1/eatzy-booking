@php
    $isFavourite = auth()->user()
        ->favouriteRestaurants()
        ->where('restaurant_id', $restaurant->id)
        ->exists();
@endphp

<button type="button"
        class="toggle-favourite"
        data-id="{{ $restaurant->id }}"
        title="{{ $isFavourite ? 'Remove from favourites' : 'Add to favourites' }}"
        style="background:none; border:none; cursor:pointer; font-size:2rem; line-height:1;">
    {{ $isFavourite ? '❤️' : '🤍' }}
</button>
