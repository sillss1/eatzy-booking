@extends('layouts.app')

@section('title', $restaurant->name)


@section('content')
    <h2>{{ $restaurant->name }}</h2>
    <p>{{ $restaurant->description }}</p>
    <p>{{ $restaurant->address }}</p>
    @include('restaurants._opening_hours')
    
    @auth
        @if(Auth::user()->isCustomer())
            <a href="{{ route('reservations.create', $restaurant->id) }}" class="button">
                Book a table
            </a>
        @endif
        @if(auth()->user()->isOwner() && auth()->user()->id === $restaurant->owner_id)
        <p>
            <a class="button" href="{{ route('restaurants.edit', $restaurant->id) }}">
                Edit Restaurant Details
            </a>
        </p>
        @endif
    @endauth

    <h3>Photos</h3>

    @if($restaurant->photos->isEmpty())
        <p>No photos available.</p>
    @else
        <div class="menu-slider">
            @foreach($restaurant->photos as $photo)
                <div class="menu-slide">
                    <img src="{{ asset('storage/' . $photo->link) }}" 
                        alt="{{ $photo->title }}" 
                        onclick="openPhotoPopup('{{ asset('storage/' . $photo->link) }}', '{{ $photo->title }}')">
                    <div class="photo-title">{{ $photo->title }}</div>
                    @if($photo->price)
                        <div class="photo-price">{{ $photo->price }}€</div>
                    @endif
                </div>
            @endforeach
        </div>

        <button class="prev-slide">&laquo;</button>
        <button class="next-slide">&raquo;</button>
    @endif

    <div id="photo-popup" style="
        display:none; 
        position:fixed; 
        top:0; left:0; 
        width:100%; height:100%; 
        background: rgba(0,0,0,0.8); 
        z-index:1000;
        justify-content:center; 
        align-items:center;">
        <div style="position:relative; display:flex; flex-direction:column; align-items:center;">
            <span style="position:absolute; top:-20px; right:-30px; font-size:40px; color:white; cursor:pointer;"
                onclick="closePhotoPopup()">&times;</span>
            <img id="popup-img" src="" style="max-width:90%; max-height:80%;">
            <div id="popup-caption" style="color:white; margin-top:10px; text-align:center;"></div>
        </div>
    </div>

    <script>
    function openPhotoPopup(src, title) {
        document.getElementById('popup-img').src = src;
        document.getElementById('popup-caption').innerText = title || '';
        document.getElementById('photo-popup').style.display = 'flex';
    }

    function closePhotoPopup() {
        document.getElementById('photo-popup').style.display = 'none';
    }
    </script>

    @auth
        @if(auth()->user()->isOwner() && auth()->user()->id === $restaurant->owner_id)
            <div>Manage Photos</div>
            <a href="{{ route('restaurants.photos.edit', $restaurant->id) }}" class="button">
                Edit Photos
            </a>
        @endif
    @endauth


@endsection