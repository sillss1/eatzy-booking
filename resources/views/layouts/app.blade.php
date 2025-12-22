<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <link rel="stylesheet" href="{{ asset('css/milligram.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')

    <script src="{{ asset('js/favourite.js') }}"></script>
</head>

<body>
    <main>
        <header>
            <h1><a href="{{ route('restaurants.index') }}">EatZy Booking</a></h1>

            <nav style="display: flex; gap: 1rem; align-items: center;">

                @guest
                    <a class="button" href="{{ route('restaurants.index') }}">Restaurants</a>
                @endguest
                
                @auth
                    @php
                        $unreadNotifications = 0;
                        try {
                            $unreadNotifications = Auth::user()->unreadNotifications()->count();
                        } catch (\Throwable $e) {
                            $unreadNotifications = 0;
                        }
                    @endphp

                    @if (!Auth::user()->isOwner())
                        <a class="button" href="{{ route('restaurants.index') }}">Restaurants</a>
                    @endif

                    @if (Auth::user()->isCustomer() || Auth::user()->isAdmin())
                        <a class="button" href="{{ route('reservations.index') }}">My Reservations</a>
                    @endif

                    @if (Auth::user()->isOwner())
                        <a class="button" href="{{ route('restaurants.create') }}">Add Restaurant</a>
                        <a class="button" href="{{ route('restaurants.index') }}">My Restaurants</a>
                        <a class="button" href="{{ route('reservations.index') }}">Reservations</a>
                    @endif
                @endauth

                <div class="dropdown">
                    <button class="button dropdown-toggle">Information</button>
                    <div class="dropdown-menu">
                        <a href="{{ route('about') }}">ℹ️ About</a>
                        <a href="{{ route('faq') }}">❓ FAQ</a>
                    </div>
                </div>

                    
                @guest
                    <a class="button" href="{{ route('login') }}">Login</a>
                    <a class="button" href="{{ route('register') }}">Register</a>
                @endguest

                @auth
                    <div class="dropdown">
                        <button class="button dropdown-toggle">Account</button>
                        <div class="dropdown-menu">
                            <a href="{{ route('account.me') }}">👤 Profile</a>
                            <a href="{{ route('notifications.index') }}">🔔 Notifications</a>
                            <a href="{{ route('2fa.setup') }}">🔐 2FA Setup</a>
                            <a href="{{ url('/logout') }}">⏻ Logout</a>
                        </div>
                    </div>

                    @if (Auth::user()->isAdmin())
                        <a class="button" style="background-color: #333; border-color: #333;" href="{{ route('admin.dashboard') }}">
                            Admin Panel
                        </a>
                    @endif
                @endauth
            </nav>
        </header>

        <section id="content">
            @yield('content')
        </section>

        <footer>
            <p>&copy; {{ date('Y') }} <a href="{{ route('restaurants.index') }}">EatZy Booking</a> — Modern Restaurant
                Reservations</p>
        </footer>
    </main>

    @stack('scripts')
</body>

</html>