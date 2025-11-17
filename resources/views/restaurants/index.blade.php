@extends('layouts.app')

@section('title', 'Restaurants')

@section('content')
    <h2>Restaurants</h2>

    @if($restaurants->isEmpty())
        <p>No restaurants found.</p>
    @else
        <ul>
            @foreach ($restaurants as $restaurant)
                <li style="margin-bottom: 1rem;">
                    {{-- Link para os detalhes (US05) --}}
                    <a href="{{ route('restaurants.show', $restaurant->id) }}">
                        <strong>{{ $restaurant->name }}</strong>
                    </a><br>

                    {{-- Descrição resumida --}}
                    <span>{{ \Illuminate\Support\Str::limit($restaurant->description, 120) }}</span><br>

                    <small>{{ $restaurant->address }}</small>
                </li>
            @endforeach
        </ul>

        
    @endif
@endsection
