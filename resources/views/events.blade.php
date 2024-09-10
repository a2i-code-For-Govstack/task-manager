
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
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" name="location" id="location" class="form-control">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Create Event</button>
            </form>
        </div>

        @if ($eventToEdit)
            <div class="mb-4">
                <h3>Edit Event</h3>
                <form action="{{ route('google.updateEvent', $eventToEdit->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="summary">Event Summary</label>
                        <input type="text" name="summary" id="summary" class="form-control" value="{{ $eventToEdit->getSummary() }}" required>
                    </div>
                    <div class="form-group">
                        <label for="start_datetime">Start DateTime</label>
                        <input type="datetime-local" name="start_datetime" id="start_datetime" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime($eventToEdit->getStart()->getDateTime())) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="end_datetime">End DateTime</label>
                        <input type="datetime-local" name="end_datetime" id="end_datetime" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime($eventToEdit->getEnd()->getDateTime())) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" name="location" id="location" class="form-control" value="{{ $eventToEdit->getLocation() }}">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control">{{ $eventToEdit->getDescription() }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Update Event</button>
                </form>
            </div>
        @endif

        <div class="event-list">
            @foreach ($eventsArray as $event)
                <div class="event-item">
                    <div class="event-title">{{ $event->getSummary() }}</div>
                    <div class="event-time">
                        <strong>Start:</strong> {{ date('d M Y, h:i A', strtotime($event->getStart()->getDateTime())) }}<br>
                        <strong>End:</strong> {{ date('d M Y, h:i A', strtotime($event->getEnd()->getDateTime())) }}
                    </div>
                    <div class="event-actions mt-2">
                        <a href="{{ route('google.listEvents', ['edit' => $event->getId()]) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('google.deleteEvent', $event->getId()) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
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
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3>Create Event</h3>
                </div>
                <div class="card-body">
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
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" name="location" id="location" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Event</button>
                    </form>
                </div>
            </div>
        </div>

        @if ($eventToEdit)
            <div class="mb-4">
                <h3>Edit Event</h3>
                <form action="{{ route('google.updateEvent', $eventToEdit->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="summary">Event Summary</label>
                        <input type="text" name="summary" id="summary" class="form-control" value="{{ $eventToEdit->getSummary() }}" required>
                    </div>
                    <div class="form-group">
                        <label for="start_datetime">Start DateTime</label>
                        <input type="datetime-local" name="start_datetime" id="start_datetime" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime($eventToEdit->getStart()->getDateTime())) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="end_datetime">End DateTime</label>
                        <input type="datetime-local" name="end_datetime" id="end_datetime" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime($eventToEdit->getEnd()->getDateTime())) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" name="location" id="location" class="form-control" value="{{ $eventToEdit->getLocation() }}">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control">{{ $eventToEdit->getDescription() }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Update Event</button>
                </form>
            </div>
        @endif

        <div class="event-list">
            @foreach ($eventsArray as $event)
                <div class="event-item">
                    <div class="event-title">{{ $event->getSummary() }}</div>
                    <div class="event-time">
                        <strong>Start:</strong> {{ date('d M Y, h:i A', strtotime($event->getStart()->getDateTime())) }}<br>
                        <strong>End:</strong> {{ date('d M Y, h:i A', strtotime($event->getEnd()->getDateTime())) }}
                    </div>
                    <div class="event-actions mt-2">
                        <a href="{{ route('google.listEvents', ['edit' => $event->getId()]) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('google.deleteEvent', $event->getId()) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@600&family=Roboto:wght@500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            margin-top: 50px;
        }
        .title {
            text-align: center;
            font-size: 2em;
            margin-bottom: 30px;
            color: #343a40;
        }
        .custom-heading {
            font-family: 'Open Sans', sans-serif;
            font-size: 1.5em;
            color: #4CAF50;
            margin-bottom: 20px;
        }
        .event-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            overflow-y: auto;
            max-height: 300px; /* Adjust this value as needed */
        }
        .event-item {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            width: 100%;
            transition: transform 0.1s ease-in-out;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .event-item:hover {
            transform: scale(1.02);
        }
        .event-title {
            font-size: 1em;
            color: #007bff;
        }
        .event-time {
            color: #6c757d;
            font-size: 0.9em;
        }
        .event-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .card {
            width: 100%;
            margin-bottom: 30px;
        }
        .card-body {
            max-height: 400px; /* Adjust this value as needed */
            overflow-y: auto;
        }
        .custom-button {
            display: flex;
            gap: 5px;
            align-items: center;
        }
        @media (max-width: 768px) {
            .title {
                font-size: 1.5em;
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

        <h3 class="custom-heading">Create Event</h3>
        <div class="mb-4">
            <div class="card">
                <div class="card-body">
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
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" name="location" id="location" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Event</button>
                    </form>
                </div>
            </div>
        </div>

        @if ($eventToEdit)
            <div class="mb-4">
                <h3 class="custom-heading">Edit Event</h3>
                <form action="{{ route('google.updateEvent', $eventToEdit->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="summary">Event Summary</label>
                        <input type="text" name="summary" id="summary" class="form-control" value="{{ $eventToEdit->getSummary() }}" required>
                    </div>
                    <div class="form-group">
                        <label for="start_datetime">Start DateTime</label>
                        <input type="datetime-local" name="start_datetime" id="start_datetime" class="form-control" value="{{ $eventToEdit->getStart() ? date('Y-m-d\TH:i', strtotime($eventToEdit->getStart()->getDateTime())) : '' }}" required>
                    </div>
                    <div class="form-group">
                        <label for="end_datetime">End DateTime</label>
                        <input type="datetime-local" name="end_datetime" id="end_datetime" class="form-control" value="{{ $eventToEdit->getEnd() ? date('Y-m-d\TH:i', strtotime($eventToEdit->getEnd()->getDateTime())) : '' }}" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" name="location" id="location" class="form-control" value="{{ $eventToEdit->getLocation() }}">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control">{{ $eventToEdit->getDescription() }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Update Event</button>
                </form>
            </div>
        @endif

        <h3 class="custom-heading">Events</h3>
        <div class="card">
            <div class="card-body event-list">
            @foreach ($eventsArray as $event)
    <div class="event-item">
        <div class="event-title"><strong>{{ $event->getSummary() }}</strong></div>

        @php
            // Check for datetime or date and handle nulls
            $startDateTime = $event->getStart() ? $event->getStart()->getDateTime() : null;
            $endDateTime = $event->getEnd() ? $event->getEnd()->getDateTime() : null;
            
            $startDate = $event->getStart() ? $event->getStart()->getDate() : null;
            $endDate = $event->getEnd() ? $event->getEnd()->getDate() : null;

            // Parse start and end using Carbon based on what is available
            $start = $startDateTime ? \Carbon\Carbon::parse($startDateTime) : ($startDate ? \Carbon\Carbon::parse($startDate) : null);
            $end = $endDateTime ? \Carbon\Carbon::parse($endDateTime) : ($endDate ? \Carbon\Carbon::parse($endDate) : null);
        @endphp

        <div class="event-time">
            @if($start && $end)
                {{ $start->format('d M Y, h:i A') }} - {{ $end->format('d M Y, h:i A') }}
            @elseif($start)
                {{ $start->format('d M Y, h:i A') }} - <em>No end time</em>
            @elseif($end)
                <em>No start time</em> - {{ $end->format('d M Y, h:i A') }}
            @else
                <em>Time not specified</em>
            @endif
        </div>
    </div>
@endforeach


            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@600&family=Roboto:wght@500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            margin-top: 50px;
        }
        .title {
            text-align: center;
            font-size: 2em;
            margin-bottom: 30px;
            color: #343a40;
        }
        .custom-heading {
            font-family: 'Open Sans', sans-serif;
            font-size: 1.5em;
            color: #4CAF50;
            margin-bottom: 20px;
        }
        .event-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            overflow-y: auto;
            max-height: 300px; /* Adjust this value as needed */
        }
        .event-item {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            width: 100%;
            transition: transform 0.1s ease-in-out;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .event-item:hover {
            transform: scale(1.02);
        }
        .event-title {
            font-size: 1em;
            color: #007bff;
        }
        .event-time {
            color: #6c757d;
            font-size: 0.9em;
        }
        .event-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .card {
            width: 100%;
            margin-bottom: 30px;
        }
        .card-body {
            max-height: 400px; /* Adjust this value as needed */
            overflow-y: auto;
        }
        .custom-button {
            display: flex;
            gap: 5px;
            align-items: center;
        }
        @media (max-width: 768px) {
            .title {
                font-size: 1.5em;
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

        <h3 class="custom-heading">Create Event</h3>
        <div class="mb-4">
            <div class="card">
                <div class="card-body">
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
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" name="location" id="location" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Event</button>
                    </form>
                </div>
            </div>
        </div>

        @if ($eventToEdit)
            <div class="mb-4">
                <h3 class="custom-heading">Edit Event</h3>
                <form action="{{ route('google.updateEvent', $eventToEdit->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="summary">Event Summary</label>
                        <input type="text" name="summary" id="summary" class="form-control" value="{{ $eventToEdit->getSummary() }}" required>
                    </div>
                    <div class="form-group">
                        <label for="start_datetime">Start DateTime</label>
                        <input type="datetime-local" name="start_datetime" id="start_datetime" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime($eventToEdit->getStart()->getDateTime())) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="end_datetime">End DateTime</label>
                        <input type="datetime-local" name="end_datetime" id="end_datetime" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime($eventToEdit->getEnd()->getDateTime())) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" name="location" id="location" class="form-control" value="{{ $eventToEdit->getLocation() }}">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control">{{ $eventToEdit->getDescription() }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Update Event</button>
                </form>
            </div>
        @endif

        <h3 class="custom-heading">Events</h3>
        <div class="card">
            <div class="card-body event-list">
                @foreach ($eventsArray as $event)
                    <div class="event-item">
                        <div>
                            <div class="event-title">{{ $event->getSummary() }}</div>
                            <div class="event-time">
                                <strong>Start:</strong> {{ date('d M Y, h:i A', strtotime($event->getStart()->getDateTime())) }}<br>
                                <strong>End:</strong> {{ date('d M Y, h:i A', strtotime($event->getEnd()->getDateTime())) }}
                            </div>
                        </div>
                        <div class="event-actions custom-button">
                            <a href="{{ route('google.listEvents', ['edit' => $event->getId()]) }}" class="btn btn-warning btn-sm" data-toggle="tooltip" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('google.deleteEvent', $event->getId()) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@600&family=Roboto:wght@500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            margin-top: 50px;
        }
        .title {
            text-align: center;
            font-size: 2em;
            margin-bottom: 30px;
            color: #343a40;
        }
        .custom-heading {
            font-family: 'Open Sans', sans-serif;
            font-size: 1.5em;
            color: #4CAF50;
            margin-bottom: 20px;
        }
        .event-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            overflow-y: auto;
            max-height: 300px; 
        }
        .event-item {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            width: 100%;
            transition: transform 0.1s ease-in-out;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .event-item:hover {
            transform: scale(1.02);
        }
        .event-title {
            font-size: 1em;
            color: #007bff;
        }
        .event-time {
            color: #6c757d;
            font-size: 0.9em;
        }
        .event-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .card {
            width: 100%;
            margin-bottom: 30px;
        }
        .custom-button {
            display: flex;
            gap: 5px;
            align-items: center;
        }
        @media (max-width: 768px) {
            .title {
                font-size: 1.5em;
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

        <h3 class="custom-heading">Create Event</h3>
        <div class="mb-4">
            <div class="card">
               
                <div class="card-body">
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
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" name="location" id="location" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Event</button>
                    </form>
                </div>
            </div>
        </div>

        @if ($eventToEdit)
            <div class="mb-4">
                <h3 class="custom-heading">Edit Event</h3>
                <form action="{{ route('google.updateEvent', $eventToEdit->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="summary">Event Summary</label>
                        <input type="text" name="summary" id="summary" class="form-control" value="{{ $eventToEdit->getSummary() }}" required>
                    </div>
                    <div class="form-group">
                        <label for="start_datetime">Start DateTime</label>
                        <input type="datetime-local" name="start_datetime" id="start_datetime" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime($eventToEdit->getStart()->getDateTime())) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="end_datetime">End DateTime</label>
                        <input type="datetime-local" name="end_datetime" id="end_datetime" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime($eventToEdit->getEnd()->getDateTime())) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" name="location" id="location" class="form-control" value="{{ $eventToEdit->getLocation() }}">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control">{{ $eventToEdit->getDescription() }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Update Event</button>
                </form>
            </div>
        @endif

        <h3 class="custom-heading">Events</h3>
        <div class="card">
            <div class="card-body event-list">
                @foreach ($eventsArray as $event)
                    <div class="event-item">
                        <div>
                            <div class="event-title">{{ $event->getSummary() }}</div>
                            <div class="event-time">
                                <strong>Start:</strong> {{ date('d M Y, h:i A', strtotime($event->getStart()->getDateTime())) }}<br>
                                <strong>End:</strong> {{ date('d M Y, h:i A', strtotime($event->getEnd()->getDateTime())) }}
                            </div>
                        </div>
                        <div class="event-actions custom-button">
                            <a href="{{ route('google.listEvents', ['edit' => $event->getId()]) }}" class="btn btn-warning btn-sm" data-toggle="tooltip" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('google.deleteEvent', $event->getId()) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
</body>
</html>





