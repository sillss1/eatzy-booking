document.addEventListener('DOMContentLoaded', function () {
    let currentPhotoId = null;
    const photos = window.restaurantPhotos || {};
    const MAX_FILE_SIZE = 2 * 1024 * 1024;

    const addPhotoForm = document.getElementById('addPhotoForm');
    if (addPhotoForm) {
        const addPhotoInput = addPhotoForm.querySelector('input[name="photo"]');
        addPhotoForm.addEventListener('submit', function(e) {
            const file = addPhotoInput.files[0];
            if (file && file.size > MAX_FILE_SIZE) {
                e.preventDefault();
                alert('The selected file is too large. Maximum allowed size is 2 MB.');
            }
        });
    }

    const editPhotoForm = document.getElementById('editPhotoForm');
    if (editPhotoForm) {
        const editPhotoInput = editPhotoForm.querySelector('input[name="photo"]');
        editPhotoForm.addEventListener('submit', function(e) {
            const file = editPhotoInput.files[0];
            if (file && file.size > MAX_FILE_SIZE) {
                e.preventDefault();
                alert('The selected file is too large. Maximum allowed size is 2 MB.');
            }
        });
    }

    function openEditForm(photoId) {
        const data = photos[photoId];
        if (!data) return;

        currentPhotoId = photoId;
        document.getElementById('add-photo-form').style.display = 'none';
        document.getElementById('edit-photo-form').style.display = 'block';
        document.getElementById('edit-photo-title').value = data.title;
        document.getElementById('edit-photo-price').value = data.price ?? '';
        document.getElementById('edit-photo-order').value = data.display_order;
        document.getElementById('editPhotoForm').action = `/owner/restaurants/${window.restaurantId}/photos/${photoId}`;
        document.querySelector('.delete-button').style.display = 'inline-block';
    }

    function closeEditForm() { document.getElementById('edit-photo-form').style.display = 'none'; }
    function deletePhoto() {
        if (!currentPhotoId) return;
        if (!confirm("Are you sure you want to delete this photo?")) return;
        const token = document.querySelector('#editPhotoForm input[name="_token"]').value;
        fetch(`/owner/restaurants/${window.restaurantId}/photos/${currentPhotoId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': token }
        }).then(res => {
            if (res.ok) {
                const div = document.querySelector(`.menu-slide[data-id="${currentPhotoId}"]`);
                if(div) div.remove();
                closeEditForm();
            } else { alert("Failed to delete photo!"); }
        });
    }

    function openAddForm() { document.getElementById('edit-photo-form').style.display = 'none'; document.getElementById('add-photo-form').style.display = 'block'; }
    function closeAddForm() { document.getElementById('add-photo-form').style.display = 'none'; }

    window.openEditForm = openEditForm;
    window.closeEditForm = closeEditForm;
    window.deletePhoto = deletePhoto;
    window.openAddForm = openAddForm;
    window.closeAddForm = closeAddForm;
});
