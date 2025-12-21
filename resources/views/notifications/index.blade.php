@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <h1>Notifications</h1>

    <form method="POST" action="{{ route('notifications.read_all') }}" style="margin-bottom: 1rem;">
        @csrf
        <button class="button button-outline" type="submit">Mark all as read</button>
    </form>

    @foreach ($notifications as $n)
        <div class="restaurant-card" style="{{ $n->read_at ? 'opacity:0.8;' : 'border-color:#93c5fd;' }}">
            <p><strong>{{ $n->data['title'] ?? 'Notification' }}</strong></p>
            <p>{{ $n->data['message'] ?? '' }}</p>

            <div class="actions" style="margin-top:0.75rem;">
                <form method="POST" action="{{ route('notifications.read', $n->id) }}" style="display:inline;">
                    @csrf
                    <button class="button button-outline" type="submit">
                        {{ $n->read_at ? 'Open' : 'Mark as read' }}
                    </button>
                </form>
            </div>
        </div>
    @endforeach

    <div style="margin-top: 1rem;">
        {{ $notifications->links() }}
    </div>
@endsection
