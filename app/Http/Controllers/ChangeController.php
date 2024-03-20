<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class ChangeController extends Controller
{
    public function changeDesignation($id, $office_id, $office_unit_id, $designation_id): RedirectResponse
    {
        Session::put('_office_id', $office_id);
        Session::put('_office_unit_id', $office_unit_id);
        Session::put('_designation_id', $designation_id);
        foreach ($this->getUserOffices() as $office) {
            if ($office['office_id'] == $office_id) {
                Session::put('_current_office', $office);
            }
        }
        // $designation_role = $this->initDoptorHttp()->post(config('cag_doptor_api.designation_role'), ['designation_id' => $designation_id])->json();
        // if (isSuccess($designation_role)) {
        //     Session::put('_designation_role', json_encode($designation_role['data']));
        // }
        return redirect()->route('home.dashboard');
    }

    public function changeLocale($locale): RedirectResponse
    {
        Session::put('locale', $locale);
        app()->setLocale($locale);
        return redirect()->back();
    }
}
