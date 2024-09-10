<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\Event as GoogleEvent;
use Google\Service\Calendar\EventDateTime as GoogleEventDateTime;
use Google\Service\Exception as GoogleServiceException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; 


use App\Mail\EventNotificationEmail;
use Illuminate\Support\Facades\Mail;

use App\Notifications\EventNotification;
use Illuminate\Support\Facades\Notification;


use Google\Service\Calendar;
use Google\Service\Calendar\Channel;




class GoogleController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setAuthConfig(storage_path('app/google-client-secret.json'));
        $this->client->addScope(GoogleCalendar::CALENDAR);
        $this->client->setRedirectUri(route('google.callback'));
    }

    public function redirectToGoogle()
    {
        $authUrl = $this->client->createAuthUrl();
        return redirect($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        $code = $request->get('code');
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
        $this->client->setAccessToken($accessToken);

        Storage::disk('local')->put('google/token.json', json_encode($accessToken));
        return redirect('/events')->with('success', 'Google Calendar connected successfully.');
    }

    public function listEvents(Request $request)
    {
        $accessToken = $this->getAccessToken();
        $this->client->setAccessToken($accessToken);

        if ($this->client->isAccessTokenExpired()) {
            $refreshTokenSaved = $this->client->getRefreshToken();
            $this->client->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
            $accessToken = $this->client->getAccessToken();
            $accessToken['refresh_token'] = $refreshTokenSaved;
            Storage::disk('local')->put('google/token.json', json_encode($accessToken));
        }

        $service = new GoogleCalendar($this->client);
        $calendarId = 'primary';
        $events = $service->events->listEvents($calendarId);
        $eventsArray = $events->getItems();

        $eventToEdit = null;
        if ($request->has('edit')) {
            $eventToEdit = $service->events->get('primary', $request->query('edit'));
        }

        return view('events', compact('eventsArray', 'eventToEdit'));
    }
 
/*
public function createEvent(Request $request)
{
    Log::info('Creating event with request data:', $request->all());

    $accessToken = $this->getAccessToken();
    
    if (!$accessToken) {
        Log::error('Access token not found.');
        return redirect()->route('google.redirect')->with('error', 'Access token not found, please authenticate again.');
    }
    
    $this->client->setAccessToken($accessToken);

    if ($this->client->isAccessTokenExpired()) {
        $refreshTokenSaved = $this->client->getRefreshToken();
        $this->client->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
        $accessToken = $this->client->getAccessToken();
        $accessToken['refresh_token'] = $refreshTokenSaved;
        Storage::disk('local')->put('google/token.json', json_encode($accessToken));
    }

    $service = new GoogleCalendar($this->client);

    $validated = $request->validate([
        'summary' => 'required|string|max:255',
        'start_datetime' => 'required|date_format:Y-m-d\TH:i',
        'end_datetime' => 'required|date_format:Y-m-d\TH:i|after:start_datetime',
        'location' => 'nullable|string|max:255',
        'description' => 'nullable|string',
    ]);

    Log::info('Validated inputs:', $validated);

    Log::info('Start date-time: ' . $request->input('start_datetime') . ':00');
    Log::info('End date-time: ' . $request->input('end_datetime') . ':00');

    try {
        $event = new GoogleEvent([
            'summary' => $request->input('summary'),
            'location' => $request->input('location'),
            'description' => $request->input('description'),
            'start' => [
                'dateTime' => $request->input('start_datetime') . ':00', // Ensure seconds are included
                'timeZone' => 'Asia/Kolkata',
            ],
            'end' => [
                'dateTime' => $request->input('end_datetime') . ':00', // Ensure seconds are included
                'timeZone' => 'Asia/Kolkata',
            ],
        ]);

        Log::info('Event object before insertion:', (array) $event->toSimpleObject());

        $event = $service->events->insert('primary', $event);

        Log::info('Event created successfully:', (array) $event->toSimpleObject());

        return redirect()->route('google.listEvents')->with('success', 'Event created successfully.');
    } catch (GoogleServiceException $e) {
        Log::error('Failed to create event:', ['error' => $e->getMessage()]);

        return redirect()->route('google.listEvents')->with('error', 'Failed to create event: ' . $e->getMessage());
    }

}
*/


public function createEvent(Request $request)
{
    // Log the request data
    Log::info('Creating event with request data:', $request->all());

    // Retrieve the access token from storage instead of the session
    $accessToken = $this->getAccessToken();
    
    if (!$accessToken) {
        Log::error('Access token not found.');
        return redirect()->route('google.redirect')->with('error', 'Access token not found, please authenticate again.');
    }
    
    $this->client->setAccessToken($accessToken);

    if ($this->client->isAccessTokenExpired()) {
        $refreshTokenSaved = $this->client->getRefreshToken();
        $this->client->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
        $accessToken = $this->client->getAccessToken();
        $accessToken['refresh_token'] = $refreshTokenSaved;
        Storage::disk('local')->put('google/token.json', json_encode($accessToken));
    }

    $service = new GoogleCalendar($this->client);

    // Validate the request inputs
    $validated = $request->validate([
        'summary' => 'required|string|max:255',
        'start_datetime' => 'required|date_format:Y-m-d\TH:i',
        'end_datetime' => 'required|date_format:Y-m-d\TH:i|after:start_datetime',
        'location' => 'nullable|string|max:255',
        'description' => 'nullable|string',
    ]);

    try {
        $event = new GoogleEvent([
            'summary' => $request->input('summary'),
            'location' => $request->input('location'),
            'description' => $request->input('description'),
            'start' => [
                'dateTime' => $request->input('start_datetime') . ':00',
                'timeZone' => 'Asia/Kolkata',
            ],
            'end' => [
                'dateTime' => $request->input('end_datetime') . ':00',
                'timeZone' => 'Asia/Kolkata',
            ],
        ]);

        $event = $service->events->insert('primary', $event);

        // Notification
        $eventDetails = [
            'id' => $event->getId(),
            'summary' => $event->getSummary(),
            'location' => $event->getLocation(),
            'start' => $event->getStart()->getDateTime(),
            'end' => $event->getEnd()->getDateTime(),
        ];

        Notification::route('mail', 'samserajas@gmail.com')->notify(new EventNotification($eventDetails));

        return redirect('/events')->with('success', 'Event created and notification sent successfully.');
    } catch (GoogleServiceException $e) {
        Log::error('Error creating event: ' . $e->getMessage());
        return redirect('/events')->with('error', 'Failed to create event.');
    }
}



/*
public function updateEvent(Request $request, $id)
{
    // Retrieve the access token from storage instead of the session
    $accessToken = $this->getAccessToken();

    if (!$accessToken) {
        return redirect()->route('google.redirect')->with('error', 'Access token not found, please authenticate again.');
    }

    $this->client->setAccessToken($accessToken);

    if ($this->client->isAccessTokenExpired()) {
        $refreshTokenSaved = $this->client->getRefreshToken();
        $this->client->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
        $accessToken = $this->client->getAccessToken();
        $accessToken['refresh_token'] = $refreshTokenSaved;
        Storage::disk('local')->put('google/token.json', json_encode($accessToken));
    }

    $service = new GoogleCalendar($this->client);

    try {
        $event = $service->events->get('primary', $id);
        $event->setSummary($request->input('summary'));
        $event->setLocation($request->input('location'));
        $event->setDescription($request->input('description'));
        
        $startDateTime = new GoogleEventDateTime();
        $startDateTime->setDateTime($request->input('start_datetime') . ':00');
        $startDateTime->setTimeZone('Asia/Kolkata');
        $event->setStart($startDateTime);
        
        $endDateTime = new GoogleEventDateTime();
        $endDateTime->setDateTime($request->input('end_datetime') . ':00');
        $endDateTime->setTimeZone('Asia/Kolkata');
        $event->setEnd($endDateTime);

        $updatedEvent = $service->events->update('primary', $event->getId(), $event);

        return redirect()->route('google.listEvents')->with('success', 'Event updated successfully.');
    } catch (GoogleServiceException $e) {
        return redirect()->route('google.listEvents')->with('error', 'Failed to update event: ' . $e->getMessage());
    } catch (\Exception $e) {
        return redirect()->route('google.listEvents')->with('error', 'An unexpected error occurred: ' . $e->getMessage());
    }

}
*/   

public function updateEvent(Request $request, $id)
{
    // Retrieve the access token from storage instead of the session
    $accessToken = $this->getAccessToken();

    if (!$accessToken) {
        return redirect()->route('google.redirect')->with('error', 'Access token not found, please authenticate again.');
    }

    $this->client->setAccessToken($accessToken);

    if ($this->client->isAccessTokenExpired()) {
        $refreshTokenSaved = $this->client->getRefreshToken();
        $this->client->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
        $accessToken = $this->client->getAccessToken();
        $accessToken['refresh_token'] = $refreshTokenSaved;
        Storage::disk('local')->put('google/token.json', json_encode($accessToken));
    }

    $service = new GoogleCalendar($this->client);

    try {
        $event = $service->events->get('primary', $id);
        $event->setSummary($request->input('summary'));
        $event->setLocation($request->input('location'));
        $event->setDescription($request->input('description'));
        
        $startDateTime = new GoogleEventDateTime();
        $startDateTime->setDateTime($request->input('start_datetime') . ':00');
        $startDateTime->setTimeZone('Asia/Kolkata');
        $event->setStart($startDateTime);
        
        $endDateTime = new GoogleEventDateTime();
        $endDateTime->setDateTime($request->input('end_datetime') . ':00');
        $endDateTime->setTimeZone('Asia/Kolkata');
        $event->setEnd($endDateTime);

        $updatedEvent = $service->events->update('primary', $event->getId(), $event);

        // Notification
        $eventDetails = [
            'id' => $updatedEvent->getId(),
            'summary' => $updatedEvent->getSummary(),
            'location' => $updatedEvent->getLocation(),
            'start' => $updatedEvent->getStart()->getDateTime(),
            'end' => $updatedEvent->getEnd()->getDateTime(),
        ];

        Notification::route('mail', 'samserajas@gmail.com')->notify(new EventNotification($eventDetails));

        return redirect()->route('google.listEvents')->with('success', 'Event updated and notification sent successfully.');
    } catch (GoogleServiceException $e) {
        return redirect()->route('google.listEvents')->with('error', 'Failed to update event: ' . $e->getMessage());
    } catch (\Exception $e) {
        return redirect()->route('google.listEvents')->with('error', 'An unexpected error occurred: ' . $e->getMessage());
    }
}


/*
    public function deleteEvent($eventId)
    {
        $accessToken = $this->getAccessToken();
        $this->client->setAccessToken($accessToken);

        if ($this->client->isAccessTokenExpired()) {
            $refreshTokenSaved = $this->client->getRefreshToken();
            $this->client->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
            $accessToken = $this->client->getAccessToken();
            $accessToken['refresh_token'] = $refreshTokenSaved;
            Storage::disk('local')->put('google/token.json', json_encode($accessToken));
        }

        $service = new GoogleCalendar($this->client);

        try {
            $service->events->delete('primary', $eventId);
            return redirect()->route('google.listEvents')->with('success', 'Event deleted successfully.');
        } catch (GoogleServiceException $e) {
            return redirect()->route('google.listEvents')->with('error', 'Failed to delete event: ' . $e->getMessage());
        }


        
    }
*/  

public function deleteEvent($eventId)
{
    $accessToken = $this->getAccessToken();
    $this->client->setAccessToken($accessToken);

    if ($this->client->isAccessTokenExpired()) {
        $refreshTokenSaved = $this->client->getRefreshToken();
        $this->client->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
        $accessToken = $this->client->getAccessToken();
        $accessToken['refresh_token'] = $refreshTokenSaved;
        Storage::disk('local')->put('google/token.json', json_encode($accessToken));
    }

    $service = new GoogleCalendar($this->client);

    try {
        // Fetch event details before deletion for the notification
        $event = $service->events->get('primary', $eventId);

        // Delete the event
        $service->events->delete('primary', $eventId);

        // Notification
        $eventDetails = [
            'id' => $eventId,
            'summary' => $event->getSummary(),
            'location' => $event->getLocation(),
            'start' => $event->getStart()->getDateTime(),
            'end' => $event->getEnd()->getDateTime(),
        ];

        Notification::route('mail', 'samserajas@gmail.com')->notify(new EventNotification($eventDetails));

        return redirect()->route('google.listEvents')->with('success', 'Event deleted and notification sent successfully.');
    } catch (GoogleServiceException $e) {
        return redirect()->route('google.listEvents')->with('error', 'Failed to delete event: ' . $e->getMessage());
    }
}

    private function getAccessToken()
    {
        if (Storage::disk('local')->exists('google/token.json')) {
            return json_decode(Storage::disk('local')->get('google/token.json'), true);
        }

        return null;
    }

    public function authenticate()
    {
        return $this->redirectToGoogle();
    }

    public function editEventForm($id)
    {
        $client = new GoogleClient();
        $client->setAuthConfig(storage_path('app/google-client-secret.json'));
        $client->setAccessToken(session('google_access_token'));

        if ($client->isAccessTokenExpired()) {
            return redirect()->route('google.redirect');
        }

        $service = new GoogleCalendar($client);
        $event = $service->events->get('primary', $id);

        return view('events.edit', ['event' => $event]);
    }



    public function watchCalendar()
{
    $client = $this->getClient();
    $service = new GoogleCalendar($client);

    $channel = new Channel();
    $channel->setId(uniqid('calendar-watch-')); // Unique identifier for your channel
    $channel->setType('web_hook'); // Set channel type to web_hook
    $channel->setAddress(route('google.notifications')); // Your webhook endpoint

    try {
        $service->events->watch('primary', $channel);
        return response()->json(['message' => 'Watching calendar for changes.']);
    } catch (GoogleServiceException $e) {
        return response()->json(['error' => $e->getMessage()], 400);
    }
}

    


public function handleNotifications(Request $request)
{
    // Retrieve headers from the request
    $channelId = $request->header('X-Goog-Channel-ID');
    $resourceId = $request->header('X-Goog-Resource-ID');
    $resourceState = $request->header('X-Goog-Resource-State');
    $messageNumber = $request->header('X-Goog-Message-Number');

    // Log headers for debugging purposes
    Log::debug('Received notification from Google Calendar', [
        'channel_id' => $channelId,
        'resource_id' => $resourceId,
        'resource_state' => $resourceState,
        'message_number' => $messageNumber
    ]);

    // Verify that the request contains the necessary headers
    if (!$channelId || !$resourceId) {
        Log::warning('Invalid request received: Missing headers');
        return response('Invalid request', 400);
    }

    // Optionally, you can check the resourceId against a stored value to ensure it matches
    // Example: if ($resourceId !== $expectedResourceId) { return response('Unauthorized', 401); }

    // Handle different resource states
    switch ($resourceState) {
        case 'exists':
            Log::info("Resource exists: Calendar event created or updated. Resource ID: $resourceId");
            break;
        case 'not_exists':
            Log::info("Resource not exists: Calendar event deleted. Resource ID: $resourceId");
            break;
        case 'sync':
            Log::info("Resource sync: Calendar resource synced. Resource ID: $resourceId");
            break;
        default:
            Log::info("Unknown resource state: $resourceState. Resource ID: $resourceId");
            break;
    }

    // Return a 200 OK response to acknowledge receipt of the notification
    return response('OK', 200);
}


}
