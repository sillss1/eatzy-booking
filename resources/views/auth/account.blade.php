@extends('layouts.app')

@section('content')
<div class="container">
    <h1>My Account</h1>
    <p>Welcome, {{ Auth::user()->name }}!</p>
    <hr>
    <h3>Danger Zone</h3>
    <p>Once you delete your account, there is no going back. Please be certain.</p>
        @if($errors->any())
            <div style>
                {{ $errors->first() }}
            </div>
        @endif
        <form action="{{ route('user.delete') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete your account?');">
        @csrf
        @method('DELETE')
        <button type="submit" style="background-color: red; border-color: red;">Delete My Account</button>
    </form>
</div>
@endsection
