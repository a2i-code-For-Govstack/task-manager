
<!-- 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Calendar Events</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            margin-top: 50px;
        }
        .title {
            text-align: center;
            font-size: 2.5em;
            margin-bottom: 30px;
            color: #343a40;
        }
        .event-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .event-item {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            max-width: 300px;
            text-align: center;
            transition: transform 0.3s ease-in-out;
        }
        .event-item:hover {
            transform: scale(1.05);
        }
        .event-title {
            font-size: 1.2em;
            color: #007bff;
            margin-bottom: 10px;
        }
        .event-time {
            color: #6c757d;
            font-size: 0.9em;
        }
        @media (max-width: 768px) {
            .title {
                font-size: 2em;
            }
            .event-item {
                width: 80%;
            }
        }
        @media (max-width: 576px) {
            .event-item {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="title">Google Calendar Events</h2>
        <div class="event-list">
            @foreach ($eventsArray as $event)
                <div class="event-item">
                    <div class="event-title"><strong>{{ $event->getSummary() }}</strong></div>
                    @php
                        $start = new \Carbon\Carbon($event->getStart()->getDateTime());
                        $end = new \Carbon\Carbon($event->getEnd()->getDateTime());
                    @endphp
                    <div class="event-time">
                        {{ $start->format('d M Y, h:i A') }} - {{ $end->format('d M Y, h:i A') }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
-->








<!-- 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Calendar Events</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            margin-top: 50px;
        }
        .title {
            text-align: center;
            font-size: 2.5em;
            margin-bottom: 30px;
            color: #343a40;
        }
        .event-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .event-item {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            max-width: 300px;
            text-align: center;
            transition: transform 0.3s ease-in-out;
        }
        .event-item:hover {
            transform: scale(1.05);
        }
        .event-title {
            font-size: 1.2em;
            color: #007bff;
            margin-bottom: 10px;
        }
        .event-time {
            color: #6c757d;
            font-size: 0.9em;
        }
        @media (max-width: 768px) {
            .title {
                font-size: 2em;
            }
            .event-item {
                width: 80%;
            }
        }
        @media (max-width: 576px) {
            .event-item {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="title">Google Calendar Events</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4">
            <h3>Create Event</h3>
            <form action="{{ route('google.createEvent') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="summary">Event Summary</label>
                    <input type="text" name="summary" id="summary" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="start_datetime">Start DateTime</label>
                    <input type="datetime-local" name="start_datetime" id="start_datetime" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="end_datetime">End DateTime</label>
                    <input type="datetime-local" name="end_datetime" id="end_datetime" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Create Event</button>
            </form>
        </div>

        <h3 class="mb-4">Events List</h3>
        <div class="event-list">
            @foreach ($eventsArray as $event)
                <div class="event-item">
                    <div class="event-title"><strong>{{ $event->getSummary() }}</strong></div>
                    @php
                        $start = new \Carbon\Carbon($event->getStart()->getDateTime());
                        $end = new \Carbon\Carbon($event->getEnd()->getDateTime());
                    @endphp
                    <div class="event-time">
                        {{ $start->format('d M Y, h:i A') }} - {{ $end->format('d M Y, h:i A') }}
                    </div>
                    <a href="{{ route('google.editEventForm', $event->getId()) }}" class="btn btn-warning mt-2">Edit</a>
                    <form action="{{ route('google.deleteEvent', $event->getId()) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger mt-2">Delete</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    @if(isset($eventToEdit))
    <div class="modal" id="editEventModal" tabindex="-1" role="dialog" style="display: block;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Event</h5>
                    <a href="{{ route('google.listEvents') }}" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></a>
                </div>
                <div class="modal-body">
                    <form action="{{ route('google.updateEvent', $eventToEdit->getId()) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="summary">Event Summary</label>
                            <input type="text" name="summary" id="summary" class="form-control" value="{{ $eventToEdit->getSummary() }}" required>
                        </div>
                        <div class="form-group">
                            <label for="start_datetime">Start DateTime</label>
                            <input type="datetime-local" name="start_datetime" id="start_datetime" class="form-control" value="{{ \Carbon\Carbon::parse($eventToEdit->getStart()->getDateTime())->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="end_datetime">End DateTime</label>
                            <input type="datetime-local" name="end_datetime" id="end_datetime" class="form-control" value="{{ \Carbon\Carbon::parse($eventToEdit->getEnd()->getDateTime())->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Event</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</body>
</html>

--> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Calendar Events</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            margin-top: 50px;
        }
        .title {
            text-align: center;
            font-size: 2.5em;
            margin-bottom: 30px;
            color: #343a40;
        }
        .event-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .event-item {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            max-width: 300px;
            text-align: center;
            transition: transform 0.3s ease-in-out;
        }
        .event-item:hover {
            transform: scale(1.05);
        }
        .event-title {
            font-size: 1.2em;
            color: #007bff;
            margin-bottom: 10px;
        }
        .event-time {
            color: #6c757d;
            font-size: 0.9em;
        }
        @media (max-width: 768px) {
            .title {
                font-size: 2em;
            }
            .event-item {
                width: 80%;
            }
        }
        @media (max-width: 576px) {
            .event-item {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="title">Google Calendar Events</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4">
            <h3>Create Event</h3>
            <form action="{{ route('google.createEvent') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="summary">Event Summary</label>
                    <input type="text" name="summary" id="summary" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="start_datetime">Start DateTime</label>
                    <input type="datetime-local" name="start_datetime" id="start_datetime" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="end_datetime">End DateTime</label>
                    <input type="datetime-local" name="end_datetime" id="end_datetime" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Create Event</button>
            </form>
        </div>

        <h3 class="mb-4">Events List</h3>
        <div class="event-list">
            @foreach ($eventsArray as $event)
                <div class="event-item">
                    <div class="event-title"><strong>{{ $event->getSummary() }}</strong></div>
                    @php
                        $start = new \Carbon\Carbon($event->getStart()->getDateTime());
                        $end = new \Carbon\Carbon($event->getEnd()->getDateTime());
                    @endphp
                    <div class="event-time">
                        {{ $start->format('d M Y, h:i A') }} - {{ $end->format('d M Y, h:i A') }}
                    </div>
                    <a href="{{ route('google.editEventForm', $event->getId()) }}" class="btn btn-warning mt-2">Edit</a>
                    <form action="{{ route('google.deleteEvent', $event->getId()) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger mt-2">Delete</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal for Edit Event -->
    @if(isset($eventToEdit))
    <div class="modal fade show" id="editEventModal" tabindex="-1" role="dialog" style="display: block;" aria-labelledby="editEventModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEventModalLabel">Edit Event</h5>
                    <a href="{{ route('google.listEvents') }}" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></a>
                </div>
                <div class="modal-body">
                    <form action="{{ route('google.updateEvent', $eventToEdit->getId()) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="summary">Event Summary</label>
                            <input type="text" name="summary" id="summary" class="form-control" value="{{ $eventToEdit->getSummary() }}" required>
                        </div>
                        <div class="form-group">
                            <label for="start_datetime">Start DateTime</label>
                            <input type="datetime-local" name="start_datetime" id="start_datetime" class="form-control" value="{{ \Carbon\Carbon::parse($eventToEdit->getStart()->getDateTime())->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="end_datetime">End DateTime</label>
                            <input type="datetime-local" name="end_datetime" id="end_datetime" class="form-control" value="{{ \Carbon\Carbon::parse($eventToEdit->getEnd()->getDateTime())->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Event</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</body>
</html>

