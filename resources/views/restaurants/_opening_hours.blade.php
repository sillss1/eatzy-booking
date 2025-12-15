    <div class="opening-hours">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <h4 style="margin: 0;">Opening Hours</h4>
            <div class="tooltip" style="display: flex; align-items: center;">
                ⓘ
                <span class="tooltip-text">You can only book a table during the restaurant's opening hours</span>
            </div>
        </div>
        <ul>
            @foreach($restaurant->formatted_opening_hours as $day => $hours)
                <li> <strong>{{ $day }}:</strong> {{ $hours }}</li>
            @endforeach
        </ul>
    </div>
