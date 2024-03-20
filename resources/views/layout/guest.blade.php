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

        <div id="kt_header" class="header align-items-stretch">
            <!--begin::Container-->
            <div class="container-fluid d-flex align-items-stretch justify-content-between">

                <!--begin::Brand-->
                <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0 w-lg-225px mr-5">
                    <!--begin::Aside mobile toggle-->
                    <div class="btn btn-icon btn-active-icon-primary ms-n2 me-2 d-flex d-lg-none" id="kt_aside_toggle">
                        <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
                        <span class="svg-icon svg-icon-1">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
										<path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="black"/>
										<path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="black"/>
									</svg>
								</span>
                        <!--end::Svg Icon-->
                    </div>
                    <!--end::Aside mobile toggle-->
                    <!--begin::Logo-->
                    <a href="../../demo5/dist/index.html">
                        <img alt="Logo" src="assets/media/logos/logo-demo5.svg" class="d-none d-lg-inline h-30px"/>
                        <img alt="Logo" src="assets/media/logos/logo-demo5-mobile.svg" class="d-lg-none h-25px"/>
                    </a>
                    <!--end::Logo-->
                </div>
                <!--end::Brand-->

                <!--begin::Wrapper-->
                <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
                    <!--begin::Navbar-->
                <!--end::Navbar-->
                    <!--begin::Toolbar wrapper-->
                    <div class="d-flex align-items-stretch flex-shrink-0">
                        <!--begin::User-->
                        <div class="d-flex align-items-center ms-lg-5" id="kt_header_user_menu_toggle">
                            <!--begin::User info-->
                            <!--end::User account menu-->
                        </div>
                        <!--end::User -->
                        <!--begin::Header menu toggle-->
                        <div class="d-flex d-lg-none align-items-center me-n2" title="Show header menu">
                            <button class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px" id="kt_header_menu_mobile_toggle">
                                <!--begin::Svg Icon | path: icons/duotune/text/txt001.svg-->
                                <span class="svg-icon svg-icon-1">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
												<path d="M13 11H3C2.4 11 2 10.6 2 10V9C2 8.4 2.4 8 3 8H13C13.6 8 14 8.4 14 9V10C14 10.6 13.6 11 13 11ZM22 5V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4V5C2 5.6 2.4 6 3 6H21C21.6 6 22 5.6 22 5Z" fill="black"/>
												<path opacity="0.3" d="M21 16H3C2.4 16 2 15.6 2 15V14C2 13.4 2.4 13 3 13H21C21.6 13 22 13.4 22 14V15C22 15.6 21.6 16 21 16ZM14 20V19C14 18.4 13.6 18 13 18H3C2.4 18 2 18.4 2 19V20C2 20.6 2.4 21 3 21H13C13.6 21 14 20.6 14 20Z" fill="black"/>
											</svg>
										</span>
                                <!--end::Svg Icon-->
                            </button>
                        </div>
                        <!--end::Header menu toggle-->
                    </div>
                    <!--end::Toolbar wrapper-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Container-->
        </div>

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

@include('layout.partials.footer_scripts')
@stack('scripts')
</body>
</html>
