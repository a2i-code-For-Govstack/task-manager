<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
<head>
    <base href="../">
    <title>{{config('constants.app_name')}}</title>
    @include('layout.partials.header_script')
    <link rel="shortcut icon" href="{{asset('assets/img/favicon.png')}}"/>
    @yield('styles')
</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed aside-enabled">
<!--begin::Main-->
<!--begin::Root-->
<div class="d-flex flex-column flex-root">
    <!--begin::Page-->
    <div class="page d-flex flex-column flex-column-fluid">

    @include('layout.partials.header')

    <!--begin::Container-->
        <div id="kt_content_container" class="d-flex flex-column-fluid align-items-stretch container-fluid">
        {{--        @include('layout.partials.aside')--}}
        <!--begin::Wrapper-->
            <div class="wrapper d-flex flex-column flex-row-fluid mt-1 mt-lg-5" id="kt_wrapper">
                <!--begin::Content-->
                <div class="content flex-column-fluid p-1" id="kt_content">
                    @yield('content')
                </div>
                <!--end::Content-->

                <!--begin::Footer-->
            @include('layout.partials.footer')
            <!--end::Footer-->

            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Page-->
</div>
<!--end::Root-->

@include('partials._quick_panel')
<!--begin::Javascript-->
@include('layout.partials.footer_scripts')
@include('scripts.global_push_notification')
@stack('scripts')
</body>
</html>
