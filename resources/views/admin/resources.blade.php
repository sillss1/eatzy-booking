@extends('layouts.app')

@section('title', 'Manage Resources')

@section('content')
    <div class="container">
        <h1>Manage Resources</h1>

        @if(session('success'))
            <div style="color: green; margin-bottom: 15px;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="color: red; margin-bottom: 15px;">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Tabs -->
        <div style="margin-bottom: 25px; display: flex; gap: 10px;">
            <a href="{{ route('admin.resources') }}"
               class="button {{ $tab == 'restaurants' ? 'active-tab' : '' }}">
                Restaurants
            </a>

            <a href="{{ route('admin.reviews') }}"
               class="button {{ $tab == 'reviews' ? 'active-tab' : '' }}">
                Reviews
            </a>
        </div>

        @if($tab == 'restaurants')
            <div style="margin-bottom: 20px;">
                <a href="{{ route('admin.restaurants.create') }}" class="button">
                    + Create Restaurant
                </a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($restaurants as $restaurant)
                        <tr>
                            <td>{{ $restaurant->id }}</td>
                            <td>{{ $restaurant->name }}</td>
                            <td>{{ $restaurant->address }}</td>
                            <td>{{ $restaurant->capacity }}</td>
                            <td>
                                @if($restaurant->closed_at)
                                    <span style="color:red;">Closed</span>
                                @else
                                    <span style="color:green;">Active</span>
                                @endif
                            </td>
                            <td class="admin-actions">
                                <a href="{{ route('admin.restaurants.edit', $restaurant->id) }}"
                                   class="button edit">
                                    Edit
                                </a>

                                @if(!$restaurant->closed_at)
                                    <form action="{{ route('admin.restaurants.delete', $restaurant->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Remove this restaurant from platform?');"
                                          style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button delete">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No restaurants found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $restaurants->links() }}
        @endif

        @if($tab == 'reviews')
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Restaurant</th>
                        <th>Rating</th>
                        <th>Content</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr>
                            <td>{{ $review->id }}</td>
                            <td>{{ $review->user ? $review->user->name : '[Deleted User]' }}</td>
                            <td>{{ $review->restaurant ? $review->restaurant->name : '[Deleted Restaurant]' }}</td>
                            <td>{{ $review->rating }} ⭐</td>
                            <td>{{ Str::limit($review->content, 50) }}</td>
                            <td class="admin-actions">
                                <a href="{{ route('admin.reviews.edit', $review->id) }}"
                                   class="button edit">
                                    Edit
                                </a>

                                <form action="{{ route('admin.reviews.delete', $review->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete this review permanently?');"
                                      style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button delete">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No reviews found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $reviews->links() }}
        @endif
    </div>
@endsection
