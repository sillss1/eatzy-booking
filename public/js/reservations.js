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

        fetch(reservationsIndexUrl + "?" + params.toString(), {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(r => r.json())
        .then(data => {
            const reservationList = document.querySelector('#reservation-list');
            if (reservationList) reservationList.innerHTML = data.html;
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
