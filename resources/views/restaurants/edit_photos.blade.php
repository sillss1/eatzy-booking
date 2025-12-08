@extends('layouts.app')

@section('title', 'Manage Photos - ' . $restaurant->name)

@section('content')
<h2>Manage Photos for {{ $restaurant->name }}</h2>

<a href="{{ route('restaurants.show', $restaurant->id) }}" class="button" style="margin-bottom:10px;">&larr; Back to Restaurant</a>

<button class="button" onclick="openAddForm()" style="margin-bottom:20px;">+ Add New Photo</button>

<div class="menu-slider">
    @foreach($photos as $photo)
    <div class="menu-slide" data-id="{{ $photo->id }}">
        <img src="{{ asset('storage/' . $photo->link) }}" alt="{{ $photo->title }}" class="restaurant-photo" onclick="openEditForm({{ $photo->id }})">
        <div class="photo-title">{{ $photo->title }}</div>
        @if($photo->price)
            <div class="photo-price">{{ $photo->price }}€</div>
        @endif
    </div>
    @endforeach
</div>

<div id="edit-photo-form" style="display:none; margin-top:20px;">
    <h2>Edit Photo</h2>
    <form id="editPhotoForm" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label>Title:</label>
        <input type="text" name="title" id="edit-photo-title">

        <label>Price:</label>
        <input type="number" name="price" id="edit-photo-price" min="0">

        <label>Upload Image:</label>
        <input type="file" name="photo" accept="image/*">

        <label>Display Order:</label>
        <input type="number" name="display_order" id="edit-photo-order" min="1" max="{{ $photos->count() }}">

        <div style="margin-top:10px;">
            <button type="submit" class="button">Save</button>
            <button type="button" class="button" onclick="closeEditForm()">Cancel</button>
            <button type="button" class="button delete-button" onclick="deletePhoto()">Delete Photo</button>
        </div>
    </form>
</div>

<div id="add-photo-form" style="display:none; margin-top:20px;">
    <h2>Add New Photo</h2>
    <form id="addPhotoForm" action="{{ route('restaurants.photos.store', $restaurant->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label>Title:</label>
        <input type="text" name="title" value="{{ old('title') }}">
        @error('title')
            <div style="color:red;">{{ $message }}</div>
        @enderror

        <label>Price:</label>
        <input type="number" name="price" min="0" value="{{ old('price') }}">
        @error('price')
            <div style="color:red;">{{ $message }}</div>
        @enderror

        <label>Upload Image:</label>
        <input type="file" name="photo" accept="image/*" required>
        @error('photo')
            <div style="color:red;">{{ $message }}</div>
        @enderror

        @error('photos')
            <div style="color:red;">{{ $message }}</div>
        @enderror

        <label>Display Order:</label>
        <input type="number" name="display_order" min="1" max="{{ $photos->count() + 1 }}" value="{{ old('display_order', $photos->count() + 1) }}">
        @error('display_order')
            <div style="color:red;">{{ $message }}</div>
        @enderror

        <div style="margin-top:10px;">
            <button type="submit" class="button">Add Photo</button>
            <button type="button" class="button" onclick="closeAddForm()">Cancel</button>
        </div>
    </form>
</div>

<script>
let currentPhotoId = null;

function openEditForm(photoId) {
    const photo = @json($photos->keyBy('id'));
    const data = photo[photoId];

    currentPhotoId = photoId;

    document.getElementById('add-photo-form').style.display = 'none';

    document.getElementById('edit-photo-form').style.display = 'block';
    document.getElementById('edit-photo-title').value = data.title;
    document.getElementById('edit-photo-price').value = data.price ?? '';
    document.getElementById('edit-photo-order').value = data.display_order;

    document.getElementById('editPhotoForm').action = `/owner/restaurants/{{ $restaurant->id }}/photos/${photoId}`;
    document.getElementById('delete-button').style.display = 'inline-block';
}

function closeEditForm() {
    document.getElementById('edit-photo-form').style.display = 'none';
}

function deletePhoto() {
    if (!currentPhotoId) return;
    if (!confirm("Are you sure you want to delete this photo?")) return;

    const token = document.querySelector('#editPhotoForm input[name="_token"]').value;

    fetch(`/owner/restaurants/{{ $restaurant->id }}/photos/${currentPhotoId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': token }
    }).then(res => {
        if (res.ok) {
            const div = document.querySelector(`.menu-slide[data-id="${currentPhotoId}"]`);
            if(div) div.remove();
            closeEditForm();
        } else {
            alert("Failed to delete photo!");
        }
    });
}

function openAddForm() {
    document.getElementById('edit-photo-form').style.display = 'none';

    document.getElementById('add-photo-form').style.display = 'block';
}

function closeAddForm() {
    document.getElementById('add-photo-form').style.display = 'none';
}
</script>
@endsection
