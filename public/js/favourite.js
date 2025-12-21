document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.toggle-favourite');
    buttons.forEach(function(button) {
        button.addEventListener('click', function () {
            const restaurantId = this.dataset.id;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            fetch(`/restaurants/${restaurantId}/favourite`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.textContent = data.favourite ? '❤️' : '🤍';
                this.title = data.favourite
                    ? 'Remove from favourites'
                    : 'Add to favourites';
            })
            .catch(err => console.error(err));
        });
    });
});
