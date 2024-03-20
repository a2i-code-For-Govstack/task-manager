<!--begin::Toggle-->
<div class="topbar-item" data-toggle="dropdown" data-offset="10px,0px">
    <div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1">
        <img class="h-20px w-20px" src="{{asset('assets/media/svg/flags/226-united-states.svg')}}" alt=""/>
    </div>
</div>
<!--end::Toggle-->
<!--begin::Dropdown-->
<div class="dropdown-menu p-0 m-0 dropdown-menu-anim-up dropdown-menu-sm dropdown-menu-right">
    <!--begin::Nav-->
    <ul class="navi navi-hover py-4">
        <!--begin::Item-->
        <li class="navi-item">
            <a href="#" class="navi-link">
                        <span class="symbol symbol-20 mr-3">
                            <img class="h-20px w-20px rounded-0"
                                 src="{{asset('assets/media/svg/flags/226-united-states.svg')}}"
                                 alt=""/>
                        </span>
                <span class="navi-text">{{ __('ইংরেজি') }}</span>
            </a>
        </li>
        <!--end::Item-->
        <!--begin::Item-->
        <li class="navi-item active">
            <a href="#" class="navi-link">
                        <span class="symbol symbol-20 mr-3">
                            <img src="{{asset('assets/media/svg/flags/bangladesh.png')}}"/>
                        </span>
                <span class="navi-text">{{ __('বাংলা') }}</span>
            </a>
        </li>
        <!--end::Item-->
    </ul>
    <!--end::Nav-->
</div>
<!--end::Dropdown-->
