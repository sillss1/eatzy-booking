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
    @endif

    <div id="photo-popup" class="photo-popup">
        <div class="photo-popup-inner">
            <span class="photo-popup-close" onclick="closePhotoPopup()">&times;</span>
            <img id="popup-img" class="photo-popup-img" src="">
            <div id="popup-caption" class="photo-popup-caption"></div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/photo-popup.js') }}" defer></script>
    @endpush

