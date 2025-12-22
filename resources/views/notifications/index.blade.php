@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <h1>Notifications</h1>

    <form method="POST" action="{{ route('notifications.read_all') }}" style="margin-bottom: 1rem;">
        @csrf
        <button class="button button-outline" type="submit">Mark all as read</button>
    </form>

    @forelse ($notifications as $n)
        <div class="restaurant-card {{ $n->read_at ? 'notification-read' : 'notification-unread' }}"
            style="{{ $n->read_at ? 'opacity: 0.6;' : 'border-left: 4px solid #3b82f6;' }}">
            <p><strong>{{ $n->data['title'] ?? 'Notification' }}</strong></p>
            <p>{{ $n->data['message'] ?? '' }}</p>
            <small style="color: #666;">{{ $n->created_at->format('d M Y H:i') }}</small>

            @if (!$n->read_at)
                <div class="actions" style="margin-top: 0.75rem;">
                    <form method="POST" action="{{ route('notifications.read', $n->id) }}" style="display:inline;">
                        @csrf
                        <button class="button button-outline" type="submit">Mark as read</button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <p style="color: #666; text-align: center; padding: 2rem;">No notifications yet.</p>
    @endforelse

    <div style="margin-top: 1rem;">
        {{ $notifications->links() }}
    </div>
@endsection