<?php
/*
namespace App\Http\Controllers;

use Google\Client;
use Google\Service\Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Google_Client;
use Illuminate\Support\Facades\Session;

class GoogleController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path('app/google-client-secret.json'));
        $this->client->addScope(Calendar::CALENDAR);
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

        return redirect('/')->with('success', 'Google Calendar connected successfully.');
    }

    public function listEvents()
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

        $service = new Calendar($this->client);
        $calendarId = 'primary';
        $events = $service->events->listEvents($calendarId);
        $eventsArray = $events->getItems();

        return view('events', compact('eventsArray'));
    }

    public function authenticate()
    {
        try {
            $accessToken = $this->getAccessToken();
            if (is_null($accessToken)) {
                return redirect()->route('google.redirect')->with('error', 'Please authenticate with Google.');
            }

            $this->client->setAccessToken($accessToken);

            if ($this->client->isAccessTokenExpired()) {
                $refreshTokenSaved = $this->client->getRefreshToken();
                $newAccessToken = $this->client->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
                $newAccessToken['refresh_token'] = $refreshTokenSaved;
                Storage::disk('local')->put('google/token.json', json_encode($newAccessToken));
                $this->client->setAccessToken($newAccessToken);
            }

            return redirect('/events')->with('success', 'Authenticated with Google successfully.');
        } catch (\Exception $e) {
            return redirect()->route('google.redirect')->with('error', 'Failed to authenticate with Google: ' . $e->getMessage());
        }
    }

    private function getAccessToken()
    {
        if (Storage::disk('local')->exists('google/token.json')) {
            $tokenPath = Storage::disk('local')->get('google/token.json');
            return json_decode($tokenPath, true);
        }

        return null;
    }
    public function logout(Request $request)
    {
        // Revoke the access token
        $client = new Google_Client();
        $client->setAccessToken(Session::get('google_access_token'));
        $client->revokeToken();

        // Clear the session data
        Session::forget('google_access_token');
        Session::forget('google_user');

        // Redirect to the login page or home page
        return redirect('/login')->with('success', 'Logged out successfully. You can now login with a different account.');
    }
}

*/



/*
namespace App\Http\Controllers;

use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\Event as GoogleEvent;
use Google\Service\Calendar\EventDateTime as GoogleEventDateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

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

    public function createEvent(Request $request)
    {
        // Validate input data
        $request->validate([
            'summary' => 'required|string',
            'start_datetime' => 'required|date_format:Y-m-d\TH:i:sP',
            'end_datetime' => 'required|date_format:Y-m-d\TH:i:sP',
        ]);
    
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
        $event = new GoogleEvent([
            'summary' => $request->input('summary'),
            'start' => ['dateTime' => $request->input('start_datetime')],
            'end' => ['dateTime' => $request->input('end_datetime')],
        ]);
    
        try {
            $service->events->insert('primary', $event);
            return redirect()->route('google.listEvents')->with('success', 'Event created successfully.');
        } catch (Google_Service_Exception $e) {
            $error = json_decode($e->getMessage(), true);
            return redirect()->route('google.listEvents')->with('error', 'Failed to create event: ' . $error['error']['message']);
        }
    }
    

    public function updateEvent(Request $request, $eventId)
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
        $event = $service->events->get('primary', $eventId);
        $event->setSummary($request->input('summary'));
        $event->setStart(new GoogleEventDateTime(['dateTime' => $request->input('start_datetime')]));
        $event->setEnd(new GoogleEventDateTime(['dateTime' => $request->input('end_datetime')]));

        $service->events->update('primary', $event->getId(), $event);

        return redirect()->route('google.listEvents')->with('success', 'Event updated successfully.');
    }

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
        $service->events->delete('primary', $eventId);

        return redirect()->route('google.listEvents')->with('success', 'Event deleted successfully.');
    }

    protected function getAccessToken()
    {
        $tokenPath = storage_path('app/google/token.json');
        if (!file_exists($tokenPath)) {
            return redirect()->route('google.redirect');
        }

        return json_decode(file_get_contents($tokenPath), true);
    }

    public function authenticate()
    {
        $authUrl = $this->client->createAuthUrl();
        return redirect($authUrl);
    }

    public function editEventForm($id)
    {
        // Check if the access token is available in the session
        $accessToken = Session::get('google_access_token');
        if (!$accessToken) {
            return redirect()->route('google.authenticate');
        }

        // Set the access token to the Google client
        $this->client->setAccessToken($accessToken);

        // Create a new Google Calendar service
        $service = new GoogleCalendar($this->client);

        // Get the event by its ID
        $event = $service->events->get('primary', $id);

        // Return the view with the event data
        return view('editEvent', ['event' => $event]);
    }
}

*/
///////////////////////////////////////////////////////////////////////////////////////////// important wala hai ye niche ka ///////////////////////////////////////////////////////////////////////////////


/* 

namespace App\Http\Controllers;

use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\Event as GoogleEvent;
use Google\Service\Calendar\EventDateTime as GoogleEventDateTime;
use Google\Service\Exception as GoogleServiceException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

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

    public function createEvent(Request $request)
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
        $event = new GoogleEvent([
            'summary' => $request->input('summary'),
            'start' => ['dateTime' => $request->input('start_datetime')],
            'end' => ['dateTime' => $request->input('end_datetime')],
        ]);

        try {
            $service->events->insert('primary', $event);
            return redirect()->route('google.listEvents')->with('success', 'Event created successfully.');
        } catch (GoogleServiceException $e) {
            return redirect()->route('google.listEvents')->with('error', 'Failed to create event: ' . $e->getMessage());
        }
    }

    public function updateEvent(Request $request, $eventId)
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
        $event = $service->events->get('primary', $eventId);
        $event->setSummary($request->input('summary'));
        $event->setStart(new GoogleEventDateTime(['dateTime' => $request->input('start_datetime')]));
        $event->setEnd(new GoogleEventDateTime(['dateTime' => $request->input('end_datetime')]));

        try {
            $service->events->update('primary', $event->getId(), $event);
            return redirect()->route('google.listEvents')->with('success', 'Event updated successfully.');
        } catch (GoogleServiceException $e) {
            return redirect()->route('google.listEvents')->with('error', 'Failed to update event: ' . $e->getMessage());
        }
    }

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

    private function getAccessToken()
    {
        if (Storage::disk('local')->exists('google/token.json')) {
            return json_decode(Storage::disk('local')->get('google/token.json'), true);
        }

        return null;
    }


    public function authenticate()
    {
        // Simply redirect to the Google OAuth consent screen
        return $this->redirectToGoogle();
    }


    

   
}


*/ 
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



/*

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

    public function createEvent(Request $request)
    {
        $client = new GoogleClient();
        $client->setAuthConfig(storage_path('app/google-client-secret.json'));
        $client->setAccessToken(session('google_access_token'));

        if ($client->isAccessTokenExpired()) {
            return redirect()->route('google.redirect');
        }

        $service = new GoogleCalendar($client);

        $event = new GoogleEvent([
            'summary' => $request->input('summary'),
            'location' => $request->input('location'),
            'description' => $request->input('description'),
            'start' => [
                'dateTime' => $request->input('start_date_time'),
                'timeZone' => 'America/Los_Angeles',
            ],
            'end' => [
                'dateTime' => $request->input('end_date_time'),
                'timeZone' => 'America/Los_Angeles',
            ],
        ]);

        $event = $service->events->insert('primary', $event);

        return redirect()->route('events.list')->with('success', 'Event created successfully.');
    }

    public function updateEvent(Request $request, $id)
    {
        $client = new GoogleClient();
        $client->setAuthConfig(storage_path('app/google-client-secret.json'));
        $client->setAccessToken(session('google_access_token'));

        if ($client->isAccessTokenExpired()) {
            return redirect()->route('google.redirect');
        }

        $service = new GoogleCalendar($client);

        $event = $service->events->get('primary', $id);
        $event->setSummary($request->input('summary'));
        $event->setLocation($request->input('location'));
        $event->setDescription($request->input('description'));
        $event->setStart(new GoogleEventDateTime(['dateTime' => $request->input('start_date_time')]));
        $event->setEnd(new GoogleEventDateTime(['dateTime' => $request->input('end_date_time')]));

        $updatedEvent = $service->events->update('primary', $event->getId(), $event);

        return redirect()->route('events.list')->with('success', 'Event updated successfully.');
    }

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
}


*/

/*

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

    // Log the validated inputs
    Log::info('Validated inputs:', $validated);

    try {
        $event = new GoogleEvent([
            'summary' => $request->input('summary'),
            'location' => $request->input('location'),
            'description' => $request->input('description'),
            'start' => [
                'dateTime' => $request->input('start_datetime') . ':00', // Ensure seconds are included
                'timeZone' => 'America/Los_Angeles',
            ],
            'end' => [
                'dateTime' => $request->input('end_datetime') . ':00', // Ensure seconds are included
                'timeZone' => 'America/Los_Angeles',
            ],
        ]);

        // Convert the event object to an array before logging
        Log::info('Event object before insertion:', (array) $event->toSimpleObject());

        $event = $service->events->insert('primary', $event);

        // Convert the created event to an array before logging
        Log::info('Event created successfully:', (array) $event->toSimpleObject());

        return redirect()->route('google.listEvents')->with('success', 'Event created successfully.');
    } catch (GoogleServiceException $e) {
        // Log the error
        Log::error('Failed to create event:', ['error' => $e->getMessage()]);

        return redirect()->route('google.listEvents')->with('error', 'Failed to create event: ' . $e->getMessage());
    }
}


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

    // Log the validated inputs
    Log::info('Validated inputs:', $validated);

    // Log start and end date-times
    Log::info('Start date-time: ' . $request->input('start_datetime') . ':00');
    Log::info('End date-time: ' . $request->input('end_datetime') . ':00');

    try {
        $event = new GoogleEvent([
            'summary' => $request->input('summary'),
            'location' => $request->input('location'),
            'description' => $request->input('description'),
            'start' => [
                'dateTime' => $request->input('start_datetime') . ':00', // Ensure seconds are included
                'timeZone' => 'America/Los_Angeles',
            ],
            'end' => [
                'dateTime' => $request->input('end_datetime') . ':00', // Ensure seconds are included
                'timeZone' => 'America/Los_Angeles',
            ],
        ]);

        // Convert the event object to an array before logging
        Log::info('Event object before insertion:', (array) $event->toSimpleObject());

        $event = $service->events->insert('primary', $event);

        // Convert the created event to an array before logging
        Log::info('Event created successfully:', (array) $event->toSimpleObject());

        return redirect()->route('google.listEvents')->with('success', 'Event created successfully.');
    } catch (GoogleServiceException $e) {
        // Log the error
        Log::error('Failed to create event:', ['error' => $e->getMessage()]);

        return redirect()->route('google.listEvents')->with('error', 'Failed to create event: ' . $e->getMessage());
    }
}



    public function updateEvent(Request $request, $id)
    {
        $client = new GoogleClient();
        $client->setAuthConfig(storage_path('app/google-client-secret.json'));
        $client->setAccessToken(session('google_access_token'));

        if ($client->isAccessTokenExpired()) {
            return redirect()->route('google.redirect');
        }

        $service = new GoogleCalendar($client);

        $event = $service->events->get('primary', $id);
        $event->setSummary($request->input('summary'));
        $event->setLocation($request->input('location'));
        $event->setDescription($request->input('description'));
        $event->setStart(new GoogleEventDateTime(['dateTime' => $request->input('start_datetime')]));
        $event->setEnd(new GoogleEventDateTime(['dateTime' => $request->input('end_datetime')]));

        $updatedEvent = $service->events->update('primary', $event->getId(), $event);

        return redirect()->route('google.listEvents')->with('success', 'Event updated successfully.');
    }



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
        $startDateTime->setTimeZone('America/Los_Angeles');
        $event->setStart($startDateTime);
        
        $endDateTime = new GoogleEventDateTime();
        $endDateTime->setDateTime($request->input('end_datetime') . ':00');
        $endDateTime->setTimeZone('America/Los_Angeles');
        $event->setEnd($endDateTime);

        $updatedEvent = $service->events->update('primary', $event->getId(), $event);

        return redirect()->route('google.listEvents')->with('success', 'Event updated successfully.');
    } catch (GoogleServiceException $e) {
        return redirect()->route('google.listEvents')->with('error', 'Failed to update event: ' . $e->getMessage());
    } catch (\Exception $e) {
        return redirect()->route('google.listEvents')->with('error', 'An unexpected error occurred: ' . $e->getMessage());
    }
}


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
}


*/
///////////////////////////////////////////
///////////////////////////////////////////
//////////////////////////////////////////
//////////////////////////////////////////
////////////////////////////////////////////
////////////////////////////////////////////////
//////////////////////////////////////////////////

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

    // Log the validated inputs
    Log::info('Validated inputs:', $validated);

    // Log start and end date-times
    Log::info('Start date-time: ' . $request->input('start_datetime') . ':00');
    Log::info('End date-time: ' . $request->input('end_datetime') . ':00');

    try {
        $event = new GoogleEvent([
            'summary' => $request->input('summary'),
            'location' => $request->input('location'),
            'description' => $request->input('description'),
            'start' => [
                'dateTime' => $request->input('start_datetime') . ':00', // Ensure seconds are included
                'timeZone' => 'America/Los_Angeles',
            ],
            'end' => [
                'dateTime' => $request->input('end_datetime') . ':00', // Ensure seconds are included
                'timeZone' => 'America/Los_Angeles',
            ],
        ]);

        // Convert the event object to an array before logging
        Log::info('Event object before insertion:', (array) $event->toSimpleObject());

        $event = $service->events->insert('primary', $event);

        // Convert the created event to an array before logging
        Log::info('Event created successfully:', (array) $event->toSimpleObject());

        return redirect()->route('google.listEvents')->with('success', 'Event created successfully.');
    } catch (GoogleServiceException $e) {
        // Log the error
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

    // Log the validated inputs
    Log::info('Validated inputs:', $validated);

    // Log start and end date-times
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

        // Convert the event object to an array before logging
        Log::info('Event object before insertion:', (array) $event->toSimpleObject());

        $event = $service->events->insert('primary', $event);

        // Convert the created event to an array before logging
        Log::info('Event created successfully:', (array) $event->toSimpleObject());

        return redirect()->route('google.listEvents')->with('success', 'Event created successfully.');
    } catch (GoogleServiceException $e) {
        // Log the error
        Log::error('Failed to create event:', ['error' => $e->getMessage()]);

        return redirect()->route('google.listEvents')->with('error', 'Failed to create event: ' . $e->getMessage());
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
        $startDateTime->setTimeZone('America/Los_Angeles');
        $event->setStart($startDateTime);
        
        $endDateTime = new GoogleEventDateTime();
        $endDateTime->setDateTime($request->input('end_datetime') . ':00');
        $endDateTime->setTimeZone('America/Los_Angeles');
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

        return redirect()->route('google.listEvents')->with('success', 'Event updated successfully.');
    } catch (GoogleServiceException $e) {
        return redirect()->route('google.listEvents')->with('error', 'Failed to update event: ' . $e->getMessage());
    } catch (\Exception $e) {
        return redirect()->route('google.listEvents')->with('error', 'An unexpected error occurred: ' . $e->getMessage());
    }
}


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
}
