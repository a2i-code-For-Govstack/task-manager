<?php

namespace App\Http\Controllers;

use App\Services\CalEventGuestServices;
use Illuminate\Http\Request;

class CalEventPreferredGuestController extends Controller
{
    public function searchPreferredUsers(Request $request, CalEventGuestServices $calEventGuestServices)
    {
        $data = [
            'office_id' => $request->office_id,
            'unit_id' => $request->unit_id,
            'search_key' => $request->search_key,
        ];

        $search_key = $request->search_key;

        $type = $request->type;
        $searched_users = $calEventGuestServices->searchGuestByEmailOrName($data);

//        dd($searched_users);

        return view('calendar_events.searched_users', compact('searched_users', 'search_key', 'type'));
    }

    public function loadOfficeLayerWise(Request $request)
    {
        $data['layer_levels'] = $request->office_layer_level;
        $offices =  $this->initHttpWithClientToken($this->getUsername() ?: '200000002962')->post(config('constants.api_url') . 'api/offices', $data)->json();
        $offices = $offices['status'] == 'success' ?  $offices['data'] : [];
        return view('office_search_component.select_office', compact('offices'));
    }

    public function loadCustomLayerLevelWise(Request $request)
    {
        $data['layer_levels'] = $request->office_layer_level;
        $custom_layers =  $this->initHttpWithClientToken($this->getUsername() ?: '200000002962')->post(config('constants.api_url') . 'api/custom-layer-level', $data)->json();
        $custom_layers = $custom_layers['status'] == 'success' ?  $custom_layers['data'] : [];
        return view('office_search_component.select_office_custom_layer', compact('custom_layers'));
    }

    public function loadOfficeCustomLayerWise(Request $request)
    {
        $data['custom_layer_ids'] = $request->custom_layer_id;
        $offices =  $this->initHttpWithClientToken($this->getUsername() ?: '200000002962')->post(config('constants.api_url') . 'api/offices', $data)->json();
        $offices = $offices['status'] == 'success' ?  $offices['data'] : [];
        return view('office_search_component.select_office', compact('offices'));
    }

    public function loadOfficeOriginLayerLevelWise(Request $request)
    {
        $data['layer_levels'] = $request->office_layer_level;
        $office_origins =  $this->initHttpWithClientToken($this->getUsername() ?: '200000002962')->post(config('constants.api_url') . 'api/office-origins', $data)->json();
        $office_origins = isset($office_origins['data']) ? $office_origins['data'] : [];
        return view('office_search_component.select_office_origin', compact('office_origins'));
    }

    public function loadOfficeOriginWise(Request $request)
    {
        $data['office_origin_ids'] = $request->office_origin_id;
        $offices =  $this->initHttpWithClientToken($this->getUsername() ?: '200000002962')->post(config('constants.api_url') . 'api/offices', $data)->json();
        $offices = $offices['status'] == 'success' ?  $offices['data'] : [];
        return view('office_search_component.select_office', compact('offices'));
    }

    public function loadUnitOfficeWise(Request $request)
    {
        $data['office_ids'] = $request->office_id;
        $units =  $this->initHttpWithClientToken($this->getUsername() ?: '200000002962')->post(config('constants.api_url') . 'api/office/units', $data)->json();
        $units = $units['status'] == 'success' ?  $units['data'][$request->office_id]['units'] : [];
        return view('office_search_component.select_unit', compact('units'));
    }

}
