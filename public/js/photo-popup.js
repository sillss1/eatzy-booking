document.addEventListener('DOMContentLoaded', function () {
    window.openPhotoPopup = function(src, title) {
        const popup = document.getElementById('photo-popup');
        const img = document.getElementById('popup-img');
        const caption = document.getElementById('popup-caption');

        if (!popup || !img || !caption) return;

        img.src = src;
        caption.innerText = title || '';
        popup.style.display = 'flex';
    };

    window.closePhotoPopup = function() {
        const popup = document.getElementById('photo-popup');
        if (!popup) return;
        popup.style.display = 'none';
    };
});
