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
        <input type="number" name="price" id="edit-photo-price" min="0" step="0.01">

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
        <input type="number" name="price" min="0" step="0.01" value="{{ old('price') }}">
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
@endsection

@push('scripts')
<script>
    window.restaurantId = {{ $restaurant->id }};
    window.restaurantPhotos = @json($photos->keyBy('id'));
</script>
<script src="{{ asset('js/manage-photos.js') }}" defer></script>
@endpush