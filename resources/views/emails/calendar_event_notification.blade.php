<!DOCTYPE html>
<html>
<head>
    <title>Calendar Event Notification</title>
</head>
<body>
    <h1>New Calendar Event Notification</h1>
    <p>Details: {{ $data['summary'] }}</p>
    <p>Start: {{ $data['start']['dateTime'] }}</p>
    <p>End: {{ $data['end']['dateTime'] }}</p>
</body>
</html>
