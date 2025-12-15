@extends('layouts.app')

@section('title', 'FAQ')

@section('content')
<div class="container">
    <h2>Frequently Asked Questions</h2>
    <ul>
        <li><strong>How do I register?</strong> Click on Register and fill in your details.</li>
        <li><strong>How do I book a restaurant?</strong> Browse restaurants and select your preferred option.</li>
        <li><strong>How do I delete my account?</strong> Go to your profile and choose 'Delete Account'.</li>
        <li><strong>Why has my reservation been cancelled?</strong> Restaurant owners have the right to deny reservations without giving a reason.</li>
        <li><strong>Is my name and surname visible to anybody?</strong> Yes, users who visit your profile can view your name and surname.
            They can access your profile from your reviews or from your reservations (only if they're an owner). You can set your name
            and surname as fake ones if you are afraid about your privacy.</li>
        <li><strong>Why can't I reply to the owner's response to my review?</strong> We want to avoid long threads of exchanges on the site, 
            which make the content less readable and might lead to arguments.</li>
    </ul>
</div>
@endsection
