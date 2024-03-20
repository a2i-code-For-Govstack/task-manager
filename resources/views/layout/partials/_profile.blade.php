<div class="btn btn-active-light d-flex align-items-center bg-hover-light py-2 px-2 px-md-3" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
    <!--begin::Name-->
    <div class="symbol symbol-30px symbol-md-40px symbol-circle" style="margin-right: 0.3em">
        <img src="{{ asset('assets/media/avatars/blank.png') }}" class="img-responsive" alt="">
    </div>
    <div class="d-none d-md-flex flex-column align-items-end justify-content-center me-2">
                <span class="font-weight-normal font-size-base d-none d-md-inline text-dark fs-base fw-bolder lh-1" style="color: black">
                    @if(Auth::check() && Auth::user()->user_role)
                        {{__('Superman')}}
                    @elseif(!Auth::check() && isset($userDetails['user_role_id']) && $userDetails['user_role_id'] == 3)
                        {{ $employeeInfo['name_bng'] ?? 'User Name' }}
                    @endif
        </span>
    </div>
    <!--end::Name-->
</div>
<!--end::User info-->
<!--begin::User account menu-->
<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold dropdown-menu-xl w-lg-400px w-md-350px pt-3 fs-6 pb-0" data-kt-menu="true">
    @if(isset($userDetails['user_role_id']) && $userDetails['user_role_id'] == 3)
        <h4 class="ant-typography px-2 bg-white py-2 border-bottom">{{ __('পদবি নির্বাচন করুন') }}</h4>
        <ul class="pl-0" role="menu">
            <li class="d-flex align-items-start overflow-hidden " role="menuitem" style="padding-left: 10px;">
                    <span class="pr-2 pt-1">
                        <i class="fas fa-id-card fa-1x a2i-color-purple"></i>
                    </span>
                @if(isset($userOffices))
                    @forelse($userOffices as $office)
                        <a href="{{route('change.office', [$office['id'], $office['office_id'], $office['office_unit_id'], $office['office_unit_organogram_id']])}}"
                           class="btn-switch-designation flex-fill overflow-hidden">
                            <span>{{ $office['designation'] }}, </span>
                            <span class="test text-truncate">{{ $office['unit_name_bn'] }}</span>
                            <span class="test text-truncate">{{ $office['office_name_bn'] }}</span>
                        </a>
                    @empty
                        <a href="javascript:;" class="btn-switch-designation flex-fill overflow-hidden">
                            <span></span>
                        </a>
                    @endforelse
                @endif
            </li>
        </ul>
    @endif
    <div class="btn-group w-100 d-flex justify-content-between" role="group" aria-label="User Profile Management">
        <button onclick="javascript:;" data-toggle="popover" data-placement="bottom" data-content="{{ __('প্রোফাইল') }}"
                class="btn-sm btn-icon btn btn-primary font-weight-bold text-white btn-profile btn-square">
            <i class="fa fa-user"></i><span class="ml-2">{{ __('প্রোফাইল') }} </span>
        </button>
        <button data-content="{{ __('হেল্প ডেস্ক') }}" data-toggle="popover" data-placement="bottom"
                class="btn-sm btn-icon btn btn-success font-weight-bold text-white btn-square">
            <i class="fad fa-user-headset"></i><span class="ml-2">{{ __('হেল্প ডেস্ক') }}</span>
        </button>
        <a onclick="event.preventDefault();document.getElementById('logout-form').submit();"
           class="menu-item btn-sm btn-icon btn btn-danger font-weight-bold text-white btn-square" data-toggle="popover"
           data-placement="bottom" data-content="{{ __('লগ আউট') }}" data-original-title="" title="">
            <i class="fas fa-sign-out-alt"></i><span class="ml-2">{{ __('লগ আউট') }} </span>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </a>
    </div>
</div>

<!--begin::User-->
{{--<div class="topbar-item" data-toggle="dropdown" data-offset="10px,0px">--}}
{{--    <div class="btn btn-dropdown w-auto btn-clean d-flex align-items-center btn-square px-2 h-100">--}}
{{--        <div class="symbol symbol-20 symbol-lg-30 symbol-circle mr-2">--}}
{{--            <img src="{{ asset('assets/media/avatars/blank.png') }}" class="img-responsive" alt="">--}}
{{--        </div>--}}
{{--        <span class="font-weight-normal font-size-base d-none d-md-inline mr-3 text-violate" style="color: black">--}}
{{--            @if(isset($userDetails['user_role_id']) && $userDetails['user_role_id'] == 1)--}}
{{--                {{__('Superman')}}--}}
{{--            @elseif(isset($userDetails['user_role_id']) && $userDetails['user_role_id'] == 3)--}}
{{--                {{ $employeeInfo['name_bng'] ?? 'User Name' }}--}}
{{--            @endif--}}
{{--        </span>--}}
{{--        <span><i class="fa fa-chevron-down"></i></span>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">--}}
{{--    <!--begin: Head -->--}}
{{--    <div class="shadow">--}}
{{--        @if(isset($userDetails['user_role_id']) && $userDetails['user_role_id'] == 3)--}}
{{--            <h4 class="ant-typography px-2 bg-white py-2 border-bottom">{{ __('পদবি নির্বাচন করুন') }}</h4>--}}
{{--            <ul class="pl-0" role="menu">--}}
{{--                <li class="d-flex align-items-start overflow-hidden " role="menuitem" style="padding-left: 10px;">--}}
{{--                <span class="pr-3 pt-1">--}}
{{--                    <i class="fas fa-id-card fa-1x a2i-color-purple"></i>--}}
{{--                </span>--}}
{{--                    @if(isset($userOffices))--}}
{{--                        @forelse($userOffices as $office)--}}
{{--                            <a href="{{route('change.office', [$office['id'], $office['office_id'], $office['office_unit_id'], $office['office_unit_organogram_id']])}}"--}}
{{--                               class="btn-switch-designation flex-fill overflow-hidden">--}}
{{--                                <span>{{ $office['designation'] }}, </span>--}}
{{--                                <span class="test text-truncate">{{ $office['unit_name_bn'] }}</span>--}}
{{--                                <span class="test text-truncate">{{ $office['office_name_bn'] }}</span>--}}
{{--                            </a>--}}
{{--                        @empty--}}
{{--                            <a href="javascript:;" class="btn-switch-designation flex-fill overflow-hidden">--}}
{{--                                <span></span>--}}
{{--                            </a>--}}
{{--                        @endforelse--}}
{{--                    @endif--}}
{{--                </li>--}}
{{--            </ul>--}}
{{--        @endif--}}
{{--        <div class="btn-group w-100 d-flex justify-content-between" role="group"--}}
{{--             aria-label="User Profile Management">--}}
{{--            <button onclick="Generic_Container.show_user_profile()" data-toggle="popover" data-placement="bottom" data-content="{{ __('প্রোফাইল') }}"--}}
{{--                    class="btn btn-primary font-weight-bold text-white btn-profile btn-square">--}}
{{--                <i class="fa fa-user"></i><span class="ml-2">{{ __('প্রোফাইল') }} </span>--}}
{{--            </button>--}}
{{--            <button data-content="{{ __('হেল্প ডেস্ক') }}" data-toggle="popover" data-placement="bottom"--}}
{{--                    class="btn btn-success font-weight-bold text-white btn-square">--}}
{{--                <i class="fad fa-user-headset"></i><span class="ml-2">{{ __('হেল্প ডেস্ক') }}</span>--}}
{{--            </button>--}}
{{--            <a onclick="event.preventDefault();document.getElementById('logout-form').submit();"--}}
{{--               class="btn btn-danger font-weight-bold text-white btn-square" data-toggle="popover"--}}
{{--               data-placement="bottom" data-content="{{ __('লগ আউট') }}" data-original-title="" title="">--}}
{{--                <i class="fas fa-sign-out-alt"></i><span class="ml-2">{{ __('লগ আউট') }} </span>--}}
{{--                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">--}}
{{--                    @csrf--}}
{{--                </form>--}}
{{--            </a>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <!--end: Navigation -->--}}
{{--</div>--}}
