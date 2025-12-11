@extends('layouts.app')

@section('title', 'Edit Reply')

@section('content')
    <h1>Edit Reply</h1>

    <form method="POST" action="{{ route('replies.update', $reply->id) }}" class="card-form">
        @csrf
        @method('PUT')

        <label>Reply</label>
        <textarea name="comment" required>{{ old('comment', $reply->content) }}</textarea>
        @error('comment')
            <span class="error">{{ $message }}</span>
        @enderror

        <button type="submit" class="button">Save</button>
    </form>
@endsection
