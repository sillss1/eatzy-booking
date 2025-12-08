@if($restaurant->photos->isNotEmpty())
    <div style="display: flex; flex-direction: row; gap: 0.5rem;">
        @foreach($restaurant->photos->take(5) as $photo)
            <img src="{{ asset('storage/' . $photo->link) }}" 
                alt="{{ $photo->title }}" 
                style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px;">
        @endforeach
    </div>
@endif