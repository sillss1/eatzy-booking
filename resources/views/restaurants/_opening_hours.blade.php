    <div class="opening-hours">
        <h4>Opening Hours</h4>
        <ul>
            @foreach($restaurant->formatted_opening_hours as $day => $hours)
                <li><strong>{{ $day }}:</strong> {{ $hours }}</li>
            @endforeach
        </ul>
    </div>
