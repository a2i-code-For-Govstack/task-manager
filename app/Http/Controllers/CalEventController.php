<?php

namespace App\Http\Controllers;

use App\Models\CalEvent;
use App\Models\CalEventGuest;
use App\Models\CalEventNotification;
use App\Services\CalEventServices;
use Illuminate\Http\Request;

class CalEventController extends Controller
{

    public function index(Request $request)
    {
        return view('calendar_events.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadAllEventsByMail(Request $request, CalEventServices $calEventServices): \Illuminate\Http\JsonResponse
    {
        $events = $calEventServices->loadEventsByEmail($request);
        if (isSuccess($events)) {
            $events = $events['data'];
        }

        return response()->json($events);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadAllEventsByOfficerId(Request $request, CalEventServices $calEventServices): \Illuminate\Http\JsonResponse
    {
        $events = $calEventServices->loadEventsByOfficerId($request);
        $events = isSuccess($events) ? $events['data'] : [];
        return response()->json($events);
    }

    public function loadCalendars(Request $request)
    {
        $start_date = $request->start;
        $end_date = $request->end;
        $filter_office_ids = [];
        $user_email = '';
        if ($request->has('event_extra_param')) {
            if (\Arr::has($request->event_extra_param, 'filter_office_ids')) {
                $filter_office_ids = $request->event_extra_param['filter_office_ids'];
            }
            if (\Arr::has($request->event_extra_param, 'user_email')) {
                $user_email = $request->event_extra_param['user_email'];
            }
        }
        if ($user_email) {
            $shared_calendar_ids = CalEventGuest::where('user_email', $user_email);
        } else {
            $shared_calendar_ids = CalEventGuest::where('user_email', $this->getPersonalEmail());
        }
        $shared_calendar_ids = $shared_calendar_ids->whereHas('event', function ($query) use ($start_date, $end_date) {
            return $query->whereBetween('event_start_date', [$start_date, $end_date]);
        })->where('user_type', '!=', 'organizer')->pluck('event_id')->toArray();

        $shared_calendars = [];
        if (count($shared_calendar_ids) > 0) {
            $shared_calendars = CalEventGuest::whereIn('event_id', $shared_calendar_ids)
                ->where('user_type', 'organizer')
                ->where('user_office_id', '!=', $this->current_office_id())
                ->select('user_office_id', 'user_office_name_en', 'user_office_name_bn')
                ->get()->unique('user_office_id');
        }

        $self_calendar = [
            'user_office_id' => $this->current_office_id(),
            'user_office_name_en' => $this->current_office()['office_name_en'],
            'user_office_name_bn' => $this->current_office()['office_name_bn'],
        ];

        return view('calendar_events.calendar_list', compact('shared_calendars', 'self_calendar', 'filter_office_ids', 'user_email'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        $data['tag_colors'] = config('constants.tag_colors');
        $data['organizer'] = [
            'user_email' => $this->getPersonalEmail(),
            'user_name_en' => $this->getEmployeeInfo()['name_eng'],
            'user_name_bn' => $this->getEmployeeInfo()['name_bng'],
            'username' => $this->getUsername(),
            'user_phone' => $this->getEmployeeInfo()['personal_mobile'],
            'user_officer_id' => $this->getEmployeeInfo()['id'],
            'user_designation_id' => $this->current_designation_id(),
            'user_office_id' => $this->current_office_id(),
            'user_office_name_en' => $this->current_office()['office_name_en'],
            'user_office_name_bn' => $this->current_office()['office_name_bn'],
            'user_unit_id' => $this->current_office_unit_id(),
            'user_office_unit_name_en' => $this->current_office()['unit_name_en'],
            'user_office_unit_name_bn' => $this->current_office()['unit_name_bn'],
            'user_designation_name_en' => $this->current_office()['designation_en'],
            'user_designation_name_bn' => $this->current_office()['designation'],
            'user_type' => 'organizer',
        ];
        return view('calendar_events.create_calendar_event_panel')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, CalEventServices $calEventServices)
    {
        $validated = \Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'nullable',
            'start_date' => 'required',
            'end_date' => 'required',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'invited_participants' => 'required',
            'notifications' => 'required',
            'tag_color' => 'required',
            'visibility' => 'required',
            'event_previous_link' => 'nullable',
            'location' => 'required',
            'all_day' => 'nullable',
            'recurrence' => 'nullable',
        ])->validate();

        $store_event = $calEventServices->storeEvent($validated);

        if (isSuccess($store_event)) {
            $response = $store_event;
        } else {
            $response = $store_event;
        }

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\CalEvent $calEvent
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $event_details = CalEvent::with('event_guests')->find($request->event_id);
        $organizer = [];
        foreach ($event_details->event_guests as $event_guest) {
            if ($event_guest->user_type == 'organizer') {
                $organizer = $event_guest;
            }
        }
        $event_details = $event_details->toArray();
        return view('calendar_events.show_calendar_event_panel', compact('event_details', 'organizer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\CalEvent $calEvent
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $event_details = CalEvent::whereHas('event_guests',function ($q){
            $q->where('user_type','!=','unassigned');
        })->with('event_guests',function ($q){
            $q->where('user_type','!=','unassigned');
        })->find($request->event_id);

        $organizer = [];
        $organizer = $event_details->event_guests->where('user_type', 'organizer')->first();
        $event_details = $event_details->toArray();

        $event_notifications = CalEventNotification::where('event_id', $request->event_id)->where('user_officer_id', $this->getOfficerId())->get();
        $event_notifications = $event_notifications ? $event_notifications->toArray() : [];

        $event_user = CalEventGuest::where('event_id', $request->event_id)->where('user_officer_id', $this->getOfficerId())->first();

        $can_edit = false;
        if ($event_user && ($event_user['user_type'] == 'organizer' || $event_user['user_type'] == 'requester' || $event_user['user_type'] == 'can_edit')) {
            $can_edit = true;
        }

        if($can_edit){
            return view('calendar_events.edit_calendar_event_panel', compact('event_details', 'organizer', 'event_user', 'event_notifications'));
        }else{
            return view('calendar_events.show_calendar_event_panel', compact('event_details', 'organizer', 'event_user', 'event_notifications'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\CalEvent $calEvent
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, CalEventServices $calEventServices)
    {
        $validated = \Validator::make($request->all(), [
            'event_id' => 'required',
            'title' => 'required',
            'description' => 'nullable',
            'start_date' => 'required',
            'end_date' => 'required',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'invited_participants' => 'required',
            'notifications' => 'required',
            'tag_color' => 'required',
            'visibility' => 'required',
            'event_previous_link' => 'nullable',
            'location' => 'nullable',
            'all_day' => 'nullable',
        ])->validate();
        $update_event = $calEventServices->updateEvent($validated);
        return response()->json($update_event);
    }

    public function unAssignEventUser(Request $request)
    {
        $data = \Validator::make($request->all(), [
            'event_user_id' => 'required',
        ])->validate();

        try {
            CalEventGuest::find($data['event_user_id'])->update(['user_type' => 'unassigned']);

            return responseFormat('success', 'Successfully Un Assigned User');
        } catch (\Exception $exception) {
            return responseFormat('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\CalEvent $calEvent
     * @return array|\Illuminate\Http\Response|string[]
     */
    public function destroy(Request $request)
    {
        try {
            $event = CalEvent::where('id', $request->event_id)->first();
            if (!$event) {
                throw new \Exception('Event Not Found.');
            }

            if ($request->delete_type && $request->delete_type == 'recurring_all') {
                $cal_recurrent_id = $event->recurrent_cal_id;

                $event_id = $cal_recurrent_id ?: $request->event_id;

                $event_ids = CalEvent::where('id', $event_id)->orWhere('recurrent_cal_id', $event_id)->pluck('id');

                CalEvent::where('id', $event_id)->orWhere('recurrent_cal_id', $event_id)->forceDelete();
                CalEventGuest::whereIn('event_id', $event_ids)->forceDelete();
                CalEventNotification::whereIn('event_id', $event_ids)->delete();

                return responseFormat('success', 'Successfully Deleted');
            }

            CalEventGuest::where('event_id', $event->id)->forceDelete();
            CalEventNotification::where('event_id', $event->id)->forceDelete();
            CalEvent::where('id', $request->event_id)->delete();

            return responseFormat('success', 'Successfully Deleted');
        } catch (\Exception $exception) {
            return responseFormat('error', $exception->getMessage());
        }
    }
}
