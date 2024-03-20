<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--begin::Head-->
<head>
    <meta charset="utf-8"/>
    <title>{{config('constants.app_name')}} @yield('title')</title>
    @include('layouts.partials.header')
    <link rel="shortcut icon" href="{{asset('assets/img/favicon.png')}}"/>
    @yield('styles')
</head>

<body id="kt_body" class="header-fixed header-mobile-fixed footer-fixed full-width-footer">

@include('layouts.partials.mobile_header')

<div class="d-flex flex-column flex-root">

    <div class="d-flex flex-row flex-column-fluid page">

        <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">

            <div id="kt_header" class="header header-fixed">

                <div class="container-fluid d-flex align-items-stretch justify-content-between">
                    <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
                        <div class="header-logo">
                            @include('layouts.partials.logo')
                        </div>
                        @include('layouts.partials.topbar_left')
                    </div>
                    @include('layouts.partials.topbar_right')
                </div>
            </div>
            <div class="content d-flex flex-column flex-column-fluid pt-0 pl-5 pr-5" id="kt_content">
                @yield('content')
            </div>
            @include('layouts.partials.footer', ['class' => 'position-relative'])
        </div>

    </div>
    <!--end::Page-->
</div>
<!--end::Main-->
<!--begin::Quick Panel-->
@include('layouts.partials._quick_panel')
<!--end::Quick Panel-->
<!--begin::Scrolltop-->
@include('layouts.partials.scroll_top')
<!--end::Scrolltop-->
@include('layouts.partials.footer_script')
@yield('scripts')
</html>
