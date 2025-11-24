<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/milligram.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    @stack('scripts')
</head>

<body>
    <main>
        <header>
            <h1><a href="{{ route('restaurants.index') }}">EatZy Booking</a></h1>

            <nav style="display: flex; gap: 1rem;">
                @guest
                    <a class="button" href="{{ route('login') }}">Login</a>
                    <a class="button" href="{{ route('register') }}">Register</a>
                    <a class="button" href="{{ route('about') }}">About</a>
                    <a class="button" href="{{ route('faq') }}">FAQ</a>
                @else
                    @if (Auth::user()->isCustomer() || Auth::user()->isAdmin())
                        <a class="button" href="{{ route('restaurants.index') }}">Restaurants</a>
                        <a class="button" href="{{ route('reservations.index') }}">My Reservations</a>
                    @endif
                    @if (Auth::user()->isOwner())
                        <a class="button" href="{{ route('restaurants.create') }}">Add Restaurant</a>
                        <a class="button" href="{{ route('restaurants.index') }}">My Restaurants</a>
                        <a class="button" href="{{ route('reservations.index') }}">Reservations</a>
                    @endif
                    <a class="button" href="{{ url('/logout') }}">Logout</a>
                    <a class="button" href="{{ route('account') }}">{{ Auth::user()->name }}</a>
                    <a class="button" href="{{ route('about') }}">About</a>
                    <a class="button" href="{{ route('faq') }}">FAQ</a>
                        @if (Auth::user()->isAdmin())
                            <a class="button" style="background-color: #333; border-color: #333;" href="{{ route('admin.dashboard') }}">Admin Panel</a>
                        @endif
                @endguest
            </nav>
        </header>

        <section id="content">
            @yield('content')
        </section>
    </main>
</body>

</html>
