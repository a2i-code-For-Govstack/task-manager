<div class="d-flex align-items-stretch" id="kt_header_nav">
    <!--begin::Menu wrapper-->
    <div class="header-menu align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_header_menu_mobile_toggle" data-kt-swapper="true"
         data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
        <!--begin::Menu-->
    @if(Auth::check() && Auth::user()->user_role == 1)
        @include('system-admin.admin_top_nav_menus')
    @elseif(!Auth::check() && isset($userDetails['user_role_id']) && $userDetails['user_role_id'] == 3)
        @include('system-admin.user_top_nav_menus')
    @endif
    <!--end::Menu-->
    </div>
    <!--end::Menu wrapper-->
</div>
