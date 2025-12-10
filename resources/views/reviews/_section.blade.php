<h2>Reviews</h2>

@auth
    @if (Auth::user()->isCustomer())
        <form action="{{ route('reviews.store', $restaurant->id) }}" method="POST" class="card-form">
            @csrf
            <label>Rating (1–5)</label>
            <input type="number" name="rating" min="1" max="5" required>

            <label>Comment</label>
            <textarea name="comment" required></textarea>

            <button type="submit" class="button">Submit Review</button>
        </form>
    @endif
@endauth

@foreach ($restaurant->reviews()->with('customer')->orderByDesc('created_at')->get() as $review)
    <div class="restaurant-card">
        <p><strong>{{ $review->customer->name }}</strong> — rating {{ $review->rating }}/5</p>
        <p>{{ $review->comment }}</p>

        @if (Auth::id() === $review->customer_id)
            <a class="button button-outline" href="{{ route('reviews.edit', $review->id) }}">Edit</a>

            <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button class="button button-outline">Delete</button>
            </form>
        @endif
    </div>
@endforeach
