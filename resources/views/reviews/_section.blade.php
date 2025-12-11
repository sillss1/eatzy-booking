@if ($errors->has('review'))
    <div class="alert alert-error">
        {{ $errors->first('review') }}
    </div>
@endif

@auth
    @if (Auth::user()->isCustomer())
        <form action="{{ route('reviews.store', $restaurant->id) }}" method="POST" class="card-form">
            @csrf

            <label>Rating (1–5)</label>
            <input
                type="number"
                name="rating"
                min="1"
                max="5"
                value="{{ old('rating', 5) }}"
                required
            >
            @error('rating')
                <span class="error">{{ $message }}</span>
            @enderror

            <label>Comment</label>
            <textarea name="comment" required>{{ old('comment') }}</textarea>
            @error('comment')
                <span class="error">{{ $message }}</span>
            @enderror

            <button type="submit" class="button">Submit Review</button>
        </form>
    @endif
@endauth

@foreach (
    $restaurant->reviews()
        ->with(['user', 'reply'])
        ->orderByDesc('created_at')
        ->get() as $review
)
    <div class="restaurant-card">
        <p>
            <strong>{{ $review->user->name ?? 'Customer' }}</strong>
            — rating {{ $review->rating }}/5
        </p>
        <p>{{ $review->content }}</p>

        @if (Auth::check() && Auth::id() === $review->user_id)
            <a class="button button-outline" href="{{ route('reviews.edit', $review->id) }}">Edit</a>

            <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button class="button button-outline">Delete</button>
            </form>
        @endif

        @if ($review->reply)
            <div class="meta" style="margin-top:0.5rem;">
                <strong>Owner reply:</strong> {{ $review->reply->content }}

                @if (
                    Auth::check() &&
                    Auth::user()->isOwner() &&
                    Auth::id() === $restaurant->owner_id
                )
                    <div class="actions" style="margin-top: 0.5rem;">
                        <a class="button button-outline"
                           href="{{ route('replies.edit', $review->reply->id) }}">
                            Edit reply
                        </a>

                        <form action="{{ route('replies.destroy', $review->reply->id) }}"
                              method="POST"
                              style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="button button-outline">Delete reply</button>
                        </form>
                    </div>
                @endif
            </div>
        @else
            @if (
                Auth::check() &&
                Auth::user()->isOwner() &&
                Auth::id() === $restaurant->owner_id
            )
                <form action="{{ route('replies.store', $review->id) }}"
                      method="POST"
                      class="card-form"
                      style="margin-top: 0.75rem;">
                    @csrf
                    <label>Reply to this review</label>
                    <textarea name="comment" required>{{ old('comment') }}</textarea>
                    @error('comment')
                        <span class="error">{{ $message }}</span>
                    @enderror
                    <div class="form-actions">
                        <button type="submit" class="button">Reply</button>
                    </div>
                </form>
            @endif
        @endif
    </div>
@endforeach
