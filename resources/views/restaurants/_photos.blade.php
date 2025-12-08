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
