<?php

namespace App\Services;

use App\Helpers\ApiHelper;
use App\Models\CalEventPreferredGuest;
use App\Traits\ApiHeart;
use App\Traits\UserInfoCollector;

class CalEventGuestServices
{
    use UserInfoCollector, ApiHeart;

    public function searchGuestByEmailOrName($search_key)
    {
        return $this->searchFromApiClient($search_key);
    }

    public function searchPreferredGuests($search_key)
    {
        $user_email = $this->getPersonalEmail();
        return CalEventPreferredGuest::where('user_email', $user_email)->where('preferred_email', 'like', '%' . $search_key . '%')->orWhere('preferred_name_en', 'like', '%' . $search_key . '%')->orWhere('preferred_name_bn', 'like', '%' . $search_key . '%')->paginate(5)->toArray();
    }

    public function searchFromApiClient($search_key)
    {
        return $this->initHttpWithClientToken($this->getUsername() ?: '200000002962')->post(config('constants.api_url') . 'api/search/officer', $search_key)->json();
    }


}
