<?php

namespace App\Http\Controllers;

use App\Models\XSsoSet;
use Illuminate\Http\Request;

class SystemAdminController extends Controller
{
    public function index(Request $request)
    {
        return view('system-admin.index');
    }

    public function ssoConfigurationLists(Request $request)
    {
        $sso_lists = XSsoSet::all();
        return view('system-admin.configuration.sso.index', compact('sso_lists'));
    }

    public function createSSOConfiguration(Request $request)
    {
        return view('system-admin.configuration.sso.create_sso');
    }

    public function storeSSOConfiguration(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = \Validator::make($request->all(), [
            'sso_name' => 'required',
            'sso_login_url' => 'required',
            'sso_logout_url' => 'required',
            'sso_api_url' => 'required',
            'is_active' => 'required',
            'is_custom' => 'required',
        ])->validate();

        $sso_store = XSsoSet::create($data);

        if ($sso_store) {
            $response = ['status' => 'success', 'data' => 'Successfully Created Configuration'];
        } else {
            $response = ['status' => 'error', 'data' => $sso_store];
        }
        return response()->json($response);
    }

    public function setSSO(Request $request)
    {
        \Validator::make($request->all(), ['sso_id' => 'required|integer'])->validate();
        try {
            $sso = XSsoSet::findOrFail($request->sso_id);
            self::changeEnvironmentVariable('SSO_LOGIN_URL', $sso->sso_login_url);
            self::changeEnvironmentVariable('SSO_LOGOUT_URL', $sso->sso_logout_url);
            return ['status' => 'success', 'data' => 'Successful.'];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public static function changeEnvironmentVariable($key, $value)
    {
        file_put_contents(app()->environmentFilePath(), str_replace(
            $key . '=' . env($key),
            $key . '=' . $value,
            file_get_contents(app()->environmentFilePath())
        ));
    }


}
