@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
    <div class="container">
        <h1>Manage Users</h1>

        <a href="{{ route('admin.users.create') }}" class="button" style="margin-bottom: 15px;">
            + Create User
        </a>

        <!-- Search & Filter -->
        <form method="GET" action="{{ route('admin.users') }}"
              style="margin-bottom: 20px; padding: 15px; background: #f4f4f4;">
            <div class="row">
                <div class="column column-50">
                    <input type="text" name="search"
                           placeholder="Search name, email..."
                           value="{{ request('search') }}">
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
                    <a href="{{ route('admin.users') }}" class="button reset">Reset</a>
                </div>
            </div>
        </form>

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
                            @if($user->isAdmin())
                                <strong style="color:red;">Admin</strong>
                            @elseif($user->isOwner())
                                <span style="color:blue;">Owner</span>
                            @else
                                Customer
                            @endif

                            @if($user->is_blocked)
                                <span style="color:orange; font-weight:bold;"> [BLOCKED]</span>
                            @endif
                        </td>
                        <td class="admin-actions">
                            <a href="{{ route('admin.users.edit', $user->id) }}"
                               class="button edit">
                                Edit
                            </a>

                            @if($user->is_blocked)
                                <form action="{{ route('admin.users.unblock', $user->id) }}"
                                      method="POST"
                                      style="display:inline;">
                                    @csrf
                                    <button type="submit" class="button unblock">
                                        Unblock
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.users.block', $user->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Block this user?');"
                                      style="display:inline;">
                                    @csrf
                                    <button type="submit" class="button block">
                                        Block
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('admin.users.delete', $user->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Are you sure? This will delete the user permanently.');"
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
                        <td colspan="5">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $users->withQueryString()->links() }}
    </div>
@endsection
