@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
    <div class="container">
        <h1>Manage Users</h1>
        <a href="{{ route('admin.users.create') }}" class="button" style="margin-bottom: 15px;">+ Create User</a>

        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('admin.users') }}"
            style="margin-bottom: 20px; padding: 15px; background: #f4f4f4;">
            <div class="row">
                <div class="column column-50">
                    <input type="text" name="search" placeholder="Search name, email..." value="{{ request('search') }}">
                </div>
                <div class="column column-25">
                    <select name="role">
                        <option value="">All Roles</option>
                        <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="owner" {{ request('role') == 'owner' ? 'selected' : '' }}>Owner</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="column column-25">
                    <button type="submit" class="button">Filter</button>
                    <a href="{{ route('admin.users') }}" class="button button-clear">Reset</a>
                </div>
            </div>
        </form>

        <!-- Users Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }} ({{ $user->username }})</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->isAdmin()) <span style="color:red; font-weight:bold;">Admin</span>
                            @elseif($user->isOwner()) <span style="color:blue;">Owner</span>
                            @else Customer
                            @endif
                            @if($user->is_blocked)
                                <span style="color: orange; font-weight: bold;"> [BLOCKED]</span>
                            @endif
                        </td>
                        <td style="display: flex; gap: 5px; flex-wrap: wrap;">
                            <!-- Edit Button -->
                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                class="button button-small button-outline">Edit</a>

                            <!-- Block/Unblock Button -->
                            @if($user->is_blocked)
                                <form action="{{ route('admin.users.unblock', $user->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="button button-small"
                                        style="background-color: green; border-color: green;">Unblock</button>
                                </form>
                            @else
                                <form action="{{ route('admin.users.block', $user->id) }}" method="POST"
                                    onsubmit="return confirm('Block this user?');" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="button button-small"
                                        style="background-color: orange; border-color: orange;">Block</button>
                                </form>
                            @endif

                            <!-- Delete Button -->
                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure? This will delete the user permanently.');"
                                style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button button-small button-outline"
                                    style="border-color: red; color: red;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        {{ $users->withQueryString()->links() }}
    </div>
@endsection