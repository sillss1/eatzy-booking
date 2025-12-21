@extends('layouts.app')

@section('title', 'Edit Review')

@section('content')
    <div class="container">
        <h1>Edit Review</h1>

        @if($errors->any())
            <div style="color: red; margin-bottom: 15px;">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <p><strong>Restaurant:</strong> {{ $review->restaurant ? $review->restaurant->name : 'N/A' }}</p>
        <p><strong>User:</strong> {{ $review->user ? $review->user->name : '[Deleted User]' }}</p>

        <form method="POST" action="{{ route('admin.reviews.update', $review->id) }}">
            @csrf
            @method('PUT')

            <div>
                <label>Rating</label>
                <input type="number" name="rating" min="1" max="5" value="{{ old('rating', $review->rating) }}" required>
            </div>

            <div>
                <label>Content</label>
                <textarea name="content" required>{{ old('content', $review->content) }}</textarea>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="button">Save Changes</button>
                <a href="{{ route('admin.reviews') }}" class="button button-outline">Cancel</a>
            </div>
        </form>
    </div>
@endsection