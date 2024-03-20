<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

trait UserInfoCollector
{
    use ApiHeart;

    public function getUserDetails()
    {
        return session()->has('_ndoptor_loggedin_user_session') ? session('_ndoptor_loggedin_user_session')['user'] : null;
    }

    public function getDesignationRole()
    {
        $designation_role = $this->initDoptorHttp()->post(config('cag_doptor_api.designation_role'), ['designation_id' => $this->current_designation_id()])->json();
        if (isSuccess($designation_role)) {
            Session::put('_designation_role', json_encode($designation_role['data']));
        }
    }

    public function current_designation_id()
    {
        return session('_designation_id') ?: $this->getUserOffices()[0]['office_unit_organogram_id'];
    }

    public function getUserOffices()
    {
        return $this->checkLogin() ? session('_ndoptor_loggedin_user_session')['office_info'] : [];
    }

    public function checkLogin(): bool
    {
        $login_cookie = $_COOKIE['_ndoptor'] ?? null;
        if ($login_cookie) {
            $login_data_from_cookie = json_decode(base64_decode($login_cookie), true);
            if (!$login_data_from_cookie) {
                $login_data_from_cookie = json_decode(gzuncompress(base64_decode($login_cookie)), true);
                session()->put(['_ndoptor_loggedin_user_session' => $login_data_from_cookie['user_info']]);
                session()->save();
                return true;
            }
            $logged_in_user_session = session('_ndoptor_loggedin_user_session');
            if ($login_data_from_cookie && $login_data_from_cookie['status'] == 'success') {
                if (!$logged_in_user_session) {
                    $currentDesk = Http::withHeaders(['api-version' => 1])->withToken($login_data_from_cookie['token'])->post(config('doptor_api_config.doptor_api_url') . 'api/user/me', []);
                    if ($currentDesk->json() && $currentDesk->json()['status'] == 'success') {
                        $user_info = $currentDesk->json()['data'];
                        session()->put(['_ndoptor_loggedin_user_session' => $user_info]);
                        session()->save();
                    } else {
                        $_COOKIE['_ndoptor'] = null;
                        return false;
                    }
                }
                return true;
            } else {
                $_COOKIE['_ndoptor'] = null;
                return false;
            }
        } else if (Auth::check() && app()->environment('local')) {
            return true;
        }

        $_COOKIE['_ndoptor'] = null;
        return false;
    }

    public function loginIntoCagBee($data)
    {
        return session('_ndoptor_loggedin_user_session')($data);
    }

    public function current_office_domain()
    {
        return $this->current_office()['office_domain_url'];
    }

    public function current_office()
    {
        return session('_current_office') ?: $this->getUserOffices()[0];
    }

    public function employee_signature()
    {
        return session()->has('_ndoptor_loggedin_user_session') ? session('_ndoptor_loggedin_user_session')['signature'] : null;
    }

    public function forceLogout()
    {
        session()->forget('login');
        unset($_COOKIE['_ndoptor']);
        $return_url = url('/login');
        return redirect(config('jisf.logout_sso_url') . '?referer=' . base64_encode($return_url));
    }

    public function userPermittedModules()
    {
        if (!session()->has('_modules') || session('_modules') == null) {
            $modules = $this->initHttpWithToken()->post(config('amms_bee_routes.role-and-permissions.modules'), [
                'cdesk' => $this->current_desk_json(),
            ])->json();
            if (is_array($modules) && isset($modules['status']) && $modules['status'] == 'success') {
                session()->put('_modules', $modules['data']);
                session()->save();
                return session('_modules');
            }
        }
        return session('_modules');
    }

    public function current_desk_json()
    {
        return json_encode($this->current_desk(), JSON_UNESCAPED_UNICODE);
    }

    function current_desk(): array
    {
        return [
            'office_id' => $this->current_office_id(),
            'office_unit_id' => $this->current_office_unit_id(),
            'is_office_admin' => json_decode($this->current_designation_role())->is_office_admin ?? false,
            'is_office_head' => json_decode($this->current_designation_role())->is_office_head ?? false,
            'is_unit_head' => json_decode($this->current_designation_role())->is_unit_head ?? false,
            'is_unit_admin' => json_decode($this->current_designation_role())->is_unit_admin ?? false,
            'designation_id' => $this->current_designation_id(),
            'officer_id' => $this->getOfficerId(),
            'user_primary_id' => $this->getUserId(),
            'username' => $this->getUsername(),
            'office_name_en' => $this->current_office()['office_name_en'],
            'office_name_bn' => $this->current_office()['office_name_bn'],
            'office_unit_en' => $this->current_office()['unit_name_en'],
            'office_unit_bn' => $this->current_office()['unit_name_bn'],
            'designation_en' => $this->current_office()['designation_en'],
            'designation_bn' => $this->current_office()['designation'],
            'officer_en' => $this->getEmployeeInfo()['name_eng'],
            'officer_bn' => $this->getEmployeeInfo()['name_bng'],
            'designation_level' => $this->current_office()['designation_level'],
            'designation_sequence' => $this->current_office()['designation_sequence'],
            'officer_grade' => $this->getEmployeeInfo()['employee_grade'],
            'email' => $this->getEmployeeInfo()['personal_email'],
            'phone' => $this->getEmployeeInfo()['personal_mobile'],
        ];
    }

    public function current_office_id()
    {
        return session('_office_id') ?: $this->getUserOffices()[0]['office_id'];
    }

    public function current_office_unit_id()
    {
        return session('_office_unit_id') ?: $this->getUserOffices()[0]['office_unit_id'];
    }

    public function current_designation_role()
    {
        return session('_designation_role') ?: json_encode([]);
    }

    public function getOfficerId()
    {
        return $this->checkLogin() ? session('_ndoptor_loggedin_user_session')['user']['employee_record_id'] : null;
    }

    public function getUserId()
    {
        return $this->checkLogin() ? session('_ndoptor_loggedin_user_session')['user']['id'] : null;
    }

    public function getUsername()
    {
        return $this->checkLogin() ? session('_ndoptor_loggedin_user_session')['user']['username'] : null;
    }

    public function getEmployeeInfo()
    {
        return session()->has('_ndoptor_loggedin_user_session') ? session('_ndoptor_loggedin_user_session')['employee_info'] : null;
    }

    public function getPersonalEmail()
    {
        return $this->checkLogin() ? session('_ndoptor_loggedin_user_session')['employee_info']['personal_email'] : null;
    }

    public function getPersonalAlternativeEmail()
    {
        return $this->checkLogin() ? session('_ndoptor_loggedin_user_session')['employee_info']['alternative_email'] : null;
    }

    public function userPermittedOtherModules()
    {
        $other_modules = $this->initHttpWithToken()->post(config('amms_bee_routes.role-and-permissions.other-modules'), [
            'cdesk' => $this->current_desk_json(),
        ])->json();
        if (is_array($other_modules) && isset($other_modules['status']) && $other_modules['status'] == 'success') {
            session()->put('_other_modules', $other_modules);
            session()->save();
            return session('_other_modules');
        }
        return null;
    }

    public function userPermittedMenusByModule($module_link)
    {
        $menus = $this->initHttpWithToken()->post(config('amms_bee_routes.role-and-permissions.menus'), [
            'cdesk' => $this->current_desk_json(),
            'module_link' => $module_link,
        ])->json();
        if (is_array($menus) && isset($menus['status']) && $menus['status'] == 'success') {
            session()->put('_module_menus', $menus['data']);
            session()->save();
            return session('_module_menus');
        }
        return null;
    }

    function profile_picture()
    {
        if (!session()->has('_user_profile_image') || session('_user_profile_image') == null) {
            $url = config('cag_doptor_api.user_image');

            $sendRequest = $this->initDoptorHttp()->post($url, [
                'employee_record_ids' => $this->user->employee_record_id,
                'encode' => '2',
            ])->json();
            if (is_array($sendRequest) && isset($sendRequest['status']) && $sendRequest['status'] == 'success') {
                session()->put('_user_profile_image', $sendRequest['data'][0]['image']);
                session()->save();
                return session('_user_profile_image');
            }
        }
        return session('_user_profile_image');
    }

    function profile_picture_url()
    {
        $url = config('cag_doptor_api.user_image');
        $sendRequest = $this->initDoptorHttp()->post($url, [
            'employee_record_ids' => $this->getOfficerId(),
            'encode' => '2',
        ])->json();
        if (is_array($sendRequest) && isset($sendRequest['status']) && $sendRequest['status'] == 'success') {
            return $sendRequest['data'][0]['image'];
        } else {
            return 'assets/media/users/blank.png';
        }
    }
}
