<?php

namespace App\Http\Controllers;

use App\Traits\JwtTokenizable;
use App\Traits\UserInfoCollector;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, UserInfoCollector, JwtTokenizable;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($this->checkLogin()) {
                $this->viewSharer();
                //$this->checkPasswordValidity();
            }
            return $next($request);
        });
    }

    public function viewSharer()
    {
        //        $wizard = $this->wizard();
        view()->share('wizardData', '');
        $userDetails = $this->getUserDetails();
        view()->share('userDetails', $userDetails);

        $userOffices = $this->getUserOffices();
        view()->share('userOffices', $userOffices);

        $employeeInfo = $this->getEmployeeInfo();
        view()->share('employeeInfo', $employeeInfo);

        $current_office = $this->current_office();
        view()->share('current_office', $current_office);

    }
}
