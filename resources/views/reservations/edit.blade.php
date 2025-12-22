@extends('layouts.app')

@section('title', 'Edit Reservation')

@section('content')
    <h2>Edit Reservation</h2>

    <div class="reservation-container">
         <div class="hours">
            @include('restaurants._opening_hours')
        </div>

        <div class="form">
            <form method="POST" action="{{ route('reservations.update', $reservation->id) }}">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <label>Title (optional):</label>
                <input type="text" name="title" value="{{ old('title', $reservation->title) }}">

                <label>Description (optional):</label>
                <textarea name="description">{{ old('description', $reservation->description) }}</textarea>

                <label>Number of people:</label>
                <input type="number" name="number_of_people" value="{{ old('number_of_people', $reservation->number_of_people) }}" min="1">

                <label>Date:</label>
                <input type="date" name="date_of_visit" value="{{ old('date_of_visit', $reservation->date_of_visit) }}">

                <label>Time:</label>
                <input type="time" name="time_of_visit" value="{{ old('time_of_visit', $reservation->time_of_visit) }}">

                <button type="submit">Save changes</button>

                @if (session('error'))
                    <p>{{ session('error') }}</p>
                @endif
            </form>
        </div>
    </div>
@endsection
