<div class="topbar">
    <!--begin::Cart-->
    <div class="topbar-item">
        {!! $wizardData !!}
    </div>
    <!--end::Cart-->

    <!--begin::Languages-->
    <div class="dropdown">
        {{--        @include('layouts.partials.topbar._language')--}}
    </div>
    <!--end::Languages-->

    <div class="dropdown">
        <div id="alertsDropdown" class="topbar-item" data-toggle="dropdown" data-offset="10px,0px"
             aria-expanded="false">
            <div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1 pulse pulse-primary">
                <span class="fa fa-bell svg-icon svg-icon-xl svg-icon-primary"></span> <span class="pulse-ring"></span>
            </div>
        </div>
        <div class="dropdown-menu p-0 m-0 dropdown-menu-right dropdown-menu-anim-up dropdown-menu-lg"
             aria-labelledby="alertsDropdown" id="alertsDropdownArea">
            <h6 class="dropdown-header">
                Alerts Center
            </h6>
        </div>
    </div>

    <!--begin::User-->
    <div class="dropdown">
        @include('layouts.partials.topbar._profile')
    </div>
    <!--end::User-->
</div>
