document.addEventListener('DOMContentLoaded', function () {
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

        fetch(window.restaurantIndexUrl + "?" + params, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(r => r.json())
        .then(data => {
            restaurantList.innerHTML = data.html;
        })
        .catch(err => console.error(err));
    }

    searchInput.addEventListener('input', loadRestaurants);
    sortSelect.addEventListener('change', loadRestaurants);
    directionSelect.addEventListener('change', loadRestaurants);
    favouritesCheckbox.addEventListener('change', loadRestaurants);
});
