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

            @auth
                <div>
                {{-- Customer --}}
                    @if (Auth::user()->isCustomer() || Auth::user()->isAdmin())
                        <nav style="display: flex; gap: 1rem;">
                            <a class="button" href="{{ route('restaurants.index') }}">Restaurants</a>
                            <a class="button" href="{{ route('reservations.index') }}">My Reservations</a>
                        </nav>
                    @endif

                    {{-- Logout --}}
                    <span>{{ Auth::user()->name }}</span>   
                    <a class="button" href="{{ url('/logout') }}"> Logout </a>
                </div>
            @endauth
        </header>

        <section id="content">
            @yield('content')
        </section>
    </main>
</body>

</html>