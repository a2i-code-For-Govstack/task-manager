<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\CalEventServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CalEventController extends Controller
{
    public function index(Request $request, CalEventServices $calEventServices): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), ['officer_id' => 'required|integer']);
        $events = $calEventServices->loadEventsByOfficerId($request);
        return response()->json($events);
    }

    public function storeEvent(Request $request, CalEventServices $calEventServices): \Illuminate\Http\JsonResponse
    {
        $validated = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'invited_participants' => 'required',
            'notifications' => 'required',
            'tag_color' => 'required',
            'visibility' => 'required',
            'event_previous_link' => 'nullable',
            'location' => 'nullable',
        ])->validate();

        $store_event = $calEventServices->storeEvent($validated);

        if (isSuccess($store_event)) {
            $response = $store_event;
        } else {
            $response = $store_event;
        }

        return response()->json($response);
    }
}
