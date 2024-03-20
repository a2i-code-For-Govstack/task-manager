<?php

namespace App\Http\Controllers\API;

use App\Services\CalEventGuestServices;
use Illuminate\Http\Request;

class DoptorController
{
    public function searchUser(Request $request, CalEventGuestServices $calEventGuestServices)
    {
        \Validator::make($request->all(), [
            'search_key' => 'required'
        ],
        [
            'search_key.required' => 'Search key is required'
        ])->validate();

        $search_key = $request->search_key;
        $searched_users = $calEventGuestServices->searchGuestByEmailOrName($search_key);
        return response()->json($searched_users);
    }
}
