@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="container">
        <h1>Admin Dashboard</h1>
        <div class="row" style="margin-bottom: 2rem;">
            <div class="column">
                <div style="border: 1px solid #ccc; padding: 20px; border-radius: 5px; text-align: center;">
                    <h3>{{ $stats['users'] }}</h3>
                    <p>Total Users</p>
                </div>
            </div>
            <div class="column">
                <div style="border: 1px solid #ccc; padding: 20px; border-radius: 5px; text-align: center;">
                    <h3>{{ $stats['restaurants'] }}</h3>
                    <p>Restaurants</p>
                </div>
            </div>
            <div class="column">
                <div style="border: 1px solid #ccc; padding: 20px; border-radius: 5px; text-align: center;">
                    <h3>{{ $stats['customers'] }}</h3>
                    <p>Customers</p>
                </div>
            </div>
            <div class="column">
                <div style="border: 1px solid #ccc; padding: 20px; border-radius: 5px; text-align: center;">
                    <h3>{{ $stats['owners'] }}</h3>
                    <p>Owners</p>
                </div>
            </div>
        </div>

        <a class="button" href="{{ route('admin.users') }}">Manage Users</a>
        <a class="button" href="{{ route('admin.resources') }}">Manage Resources</a>
    </div>
@endsection