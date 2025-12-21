@extends('layouts.app')

@section('title', 'Create Reservation')

@section('content')
    <h2>Create Reservation at {{ $restaurant->name }}</h2>

    <div class="reservation-container">
        <div class="hours">
            @include('restaurants._opening_hours')
        </div>

        <div class="form">
            <form action="{{ route('reservations.store', $restaurant->id) }}" method="POST">
                @csrf

                <div>
                    <label for="title">Title (optional):</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}">
                    @error('title')
                        <p>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description">Description (optional):</label>
                    <textarea id="description" name="description">{{ old('description') }}</textarea>
                    @error('description')
                        <p>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="number_of_people">Number of People:</label>
                    <input type="number" id="number_of_people" name="number_of_people" min="1" value="{{ old('number_of_people') }}" required>
                    @error('number_of_people')
                        <p>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_of_visit">Date of Visit:</label>
                    <input type="date" id="date_of_visit" name="date_of_visit" value="{{ old('date_of_visit') }}" required>
                    @error('date_of_visit')
                        <p>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="time_of_visit">Time of Visit:</label>
                    <input type="time" id="time_of_visit" name="time_of_visit" value="{{ old('time_of_visit') }}" required>
                    @error('time_of_visit')
                        <p>{{ $message }}</p>
                    @enderror
                </div>

                @if(session('error'))
                    <p class="alert alert-error">{{ session('error') }}</p>
                @endif

                <button type="submit">Create Reservation</button>
            </form>
        </div>
    </div>
@endsection