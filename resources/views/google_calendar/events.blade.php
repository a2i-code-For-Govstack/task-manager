<!DOCTYPE html>
<html>
<head>
    <title>Google Calendar Events</title>
</head>
<body>
    <h1>Upcoming Events</h1>
    @if (count($events) == 0)
        <p>No upcoming events found.</p>
    @else
        <ul>
            @foreach ($events as $event)
                <li>{{ $event->getSummary() }} ({{ $event->getStart()->getDateTime() }})</li>
            @endforeach
        </ul>
    @endif
</body>
</html>
