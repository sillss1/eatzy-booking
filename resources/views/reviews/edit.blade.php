@extends('layouts.app')

@section('title', 'Edit Review')

@section('content')
    <h1>Edit Review</h1>

    <form method="POST" action="{{ route('reviews.update', $review->id) }}" class="card-form">
        @csrf
        @method('PUT')

        <label>Rating</label>
        <input
            type="number"
            name="rating"
            min="1"
            max="5"
            value="{{ old('rating', $review->rating) }}"
            required
        >
        @error('rating')
            <span class="error">{{ $message }}</span>
        @enderror

        <label>Comment</label>
        <textarea name="comment" required>{{ old('comment', $review->content) }}</textarea>
        @error('comment')
            <span class="error">{{ $message }}</span>
        @enderror

        <button type="submit" class="button">Save</button>
    </form>
@endsection
