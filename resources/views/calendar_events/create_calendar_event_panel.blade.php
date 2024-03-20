<style>
    textarea {
        max-height: 100px !important;
        height: 100px !important;
        overflow-y: scroll !important;
    }
</style>
<!--begin::Form-->
<form class="form" action="#" id="kt_modal_add_event_form">
    <div class="modal-body pt-1 pb-1">
        <div class="row">
            <div class="col-md-7">
                <!--begin::Input group-->
                <div class="fv-row mb-3">
                    <label for="event_title" class="font-weight-bolder">Event Title <span class="text-danger">(*)</span></label>
                    <input type="text" id="event_title" class="form-control form-control-solid"
                           placeholder="Event Title"
                           name="title"/>
                </div>
                <div class="fv-row mb-3">
                    <label for="event_description" class="font-weight-bolder">Event Description</label>
                    <textarea data-autogrow="false" name="description" placeholder="Description" id="event_description"
                              class="form-control form-control-solid" cols="10"
                              rows="3"></textarea>
                </div>
                <div class="fv-row mb-3">
                    <label for="event_location" class="font-weight-bolder">Event Location <span
                            class="text-danger">(*)</span></label>
                    <input id="event_location" type="text" class="form-control form-control-solid"
                           placeholder="Event Location"
                           name="location"/>
                </div>
                <!--end::Input group-->
                <div class="fv-row mb-3">
                    <label for="event_previous_link" class="font-weight-bolder">Meeting Link</label>

                    <input id="event_previous_link" type="text" class="form-control form-control-solid"
                           placeholder="Meeting link"
                           name="event_previous_link"/>
                </div>
                <div class="fv-row mb-3">
                    <label class="form-check form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" value=""
                               id="kt_calendar_datepicker_allday"/>
                        <span class="form-check-label fw-bold" for="kt_calendar_datepicker_allday">All Day</span>
                    </label>
                </div>
                <div class="fv-row mb-3">
                    <div class="form-group">
                        <label style="font-weight: bold">Recurrence</label>
                        <div class="radio-inline">
                            <label class="radio">
                                <input checked="checked" type="radio" value="none" name="recurrence"/>
                                <span></span>
                                None
                            </label>
                            <label class="radio">
                                <input type="radio" value="daily" name="recurrence"/>
                                <span></span>
                                Daily
                            </label>
                            <label class="radio">
                                <input type="radio" value="weekly" name="recurrence"/>
                                <span></span>
                                Weekly
                            </label>
                            <label class="radio">
                                <input type="radio" value="monthly" name="recurrence"/>
                                <span></span>
                                Monthly
                            </label>
                        </div>
                    </div>
                </div>
                <span class="pr-1" style="font-weight: bold">Select Date and Time</span>
                <div class="row row-cols-lg-3 g-10">
                    <div class="col">
                        <div class="fv-row mb-3">
                            <input class="form-control form-control-solid datepicker" name="start_date"
                                   placeholder="Pick start date" value="" id="kt_calendar_datepicker_start_date"/>
                        </div>
                    </div>
                    <div class="col timepicker_col" data-kt-calendar="timepicker">
                        <div class="fv-row mb-3">
                            <input class="form-control form-control-solid timepicker" name="start_time"
                                   placeholder="Pick start time" id="kt_calendar_datepicker_start_time"/>
                        </div>
                    </div>
                    <div class="col timepicker_col" data-kt-calendar="timepicker">
                        <div class="fv-row mb-3">
                            <input class="form-control form-control-solid timepicker" name="end_time"
                                   placeholder="Pick end time" id="kt_calendar_datepicker_end_time"/>
                        </div>
                    </div>
                    <div class="col end_date_picker_col d-none">
                        <div class="fv-row mb-9">
                            <input class="form-control form-control-solid datepicker" name="end_date"
                                   placeholder="Pick end date" id="kt_calendar_datepicker_end_date"/>
                        </div>
                    </div>
                </div>
                <!--end::Input group-->

                <div class="row d-flex align-items-center flex-center mb-3">
                    <div class="col-5 col-sm-12">
                        <p class="mb-0"><i class="la la-user-circle"></i> Organizer <span
                                class="fs-5 fw-bolder text-gray-900">{{ $organizer['user_name_bn'] }}</span></p>
                    </div>
                    <div class="col-md-2 col-sm-2 col-lg-2">
                        <input type="hidden" id="tag_color" name="tag_color" value="#da9898">
                        <div class="custom-dropdown" id="colorDropdown">
                            <div title="Select Preferable color for events" class="color-circle" id="selected_color" style="background-color: #da9898;"
                                 onclick="toggleColorOptions()"></div>
                            <ul class="custom-dropdown-options" id="colorOptions" style="display: none">
                                <li class="custom-dropdown-option" onclick="selectColor('#da9898')">
                                    <div class="color-circle" style="background-color: #da9898;"></div>
                                </li>
                                <li class="custom-dropdown-option" onclick="selectColor('#9be59b')">
                                    <div class="color-circle" style="background-color: #9be59b;"></div>
                                </li>
                                <li class="custom-dropdown-option" onclick="selectColor('#8c8cf9')">
                                    <div class="color-circle" style="background-color: #8c8cf9;"></div>
                                </li>
                                <li class="custom-dropdown-option" onclick="selectColor('#FFFF00FF')">
                                    <div class="color-circle" style="background-color: #FFFF00FF;"></div>
                                </li>
                                <li class="custom-dropdown-option" onclick="selectColor('#FFA500FF')">
                                    <div class="color-circle" style="background-color: #FFA500FF;"></div>
                                </li>
                                <!-- Add more color options as needed -->
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-10 col-5 d-flex align-items-center">
                        <span class="pr-1" style="font-weight: bold">Visibility</span>
                        <select class="select-select2 form-select form-select-solid" name="visibility" id="">
                            <option value="public">--Select--</option>
                            <option value="public">Public</option>
                            <option value="private">Private</option>
                        </select>
                    </div>
                </div>

                <div class="g-10 mb-3">
                    <div>
                        <p class="font-weight-bolder">Reminder Notification</p>
                    </div>

                    <div class="event_notification_area">
                        <div class="row event_notification w-100 mb-2" id="event_notification_1">
                            <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3 d-flex align-items-center">
                                <i class="fa fa-bell mr-2 pr-2"></i>
                                <select class="select-select2 form-select form-select-solid notification_medium"
                                        style="width: 100% !important;"
                                        data-notification-length="1" id="">
                                    <option value="">--Select--</option>
                                    <option value="email">Email</option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3">
                                <input type="number" value="30"
                                       class="form-control form-control-solid notification_time"
                                       data-notification-length="1">
                            </div>
                            <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3 d-flex align-items-center">
                                <i class="fa fa-null d-lg-none d-md-none d-sm-block mr-2 pr-2"></i>
                                <select class="select-select2 form-select form-select-solid notification_unit"
                                        style="width: 100% !important;"
                                        data-notification-length="1">
                                    <option value="minutes">minutes</option>
                                    <option value="hours">hours</option>
                                    <option value="days">days</option>
                                    <option value="weeks">weeks</option>
                                </select>
                                <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3 ml-2 pl-2 fa fa-trash remove_notification_btn text-danger text-right"
                                     title="Remove"
                                     onclick="EventCalendarContainer.removeNotification($(this))"
                                     data-notification-length="1"></div>
                            </div>
                        </div>
                    </div>
                    <div class="btn btn-light btn-hover-rise add_new_notification_btn mt-3"><i
                            class="fa fa-plus mr-2"></i>Add New Notification
                    </div>
                    <hr class="d-none d-lg-none d-md-none d-sm-block">
                </div>
            </div>
            <div class="col-md-5">
                <div class="search_box position-relative">
                    <x-office-select grid="12" unit="true"/>
                    <span
                        class="svg-icon svg-icon-2 svg-icon-lg-1 svg-icon-gray-500 position-absolute top-50 ml-5 translate-middle-y mt-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1"
                                  transform="rotate(45 17.0365 15.1223)" fill="black"></rect>
                            <path
                                d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                fill="black"></path>
                        </svg>
                    </span>

                    <input autocomplete="off" type="search" id="search_key"
                           class="form-control form-control-lg form-control-solid px-15"
                           name="search_key" value="" placeholder="username/name/email">

                    <br>
                    <button type="button"
                            id="guest_search_field" class="btn btn-primary rounded-0">
                        <span class="indicator-label">Search</span>
                    </button>
                    <button type="button" class="btn btn-danger" onclick="resetGuestSearchedArea()">Reset</button>
                </div>
                <br>
                <div class="searched_users_area max-h-200px bg-light p-2"
                     style="overflow-y: scroll;overflow-x: hidden;"
                     onmouseenter="customScrollInit('searched_users_area', 'class')"></div>
                <br>
                <div class="max-h-300px added_users" style="overflow-y: auto">
                    <div class="py-4 border-bottom border-gray-300 border-bottom-dashed added-user-area"
                         data-added-user-email-area='{{ $organizer['user_email'] }}'
                         data-added-user-info='{{ json_encode_escaped($organizer) }}'>
                        <div class="d-flex align-items-center scroll scroll-x" style="width: 60%;">
                            <div class="symbol symbol-25px symbol-circle">
                                <img alt="Pic" src="{{ asset('assets/media/svg/avatars/blank.svg') }}"/>
                            </div>
                            <div class="ml-5">
                                <a href="javascript:;"
                                   class="fs-5 fw-bolder text-gray-900 text-hover-dark mb-2">{{ $organizer['user_name_bn'] }}</a>
                                <div class="fw-bold text-muted">{{ $organizer['user_email'] }}</div>
                                <div class="fw-bold text-muted">{{ $organizer['user_designation_name_bn'] }}</div>
                                <span class="badge badge-primary">Organizer</span>
                                <input class="user-permission-select" type="hidden" value="organizer">
                            </div>
                        </div>
                        {{--                        <div class="ms-2">--}}
                        {{--                            <select--}}
                        {{--                                class="select-select2 form-select form-select-solid form-select-sm user-permission-select"--}}
                        {{--                                readonly="true">--}}
                        {{--                                <option value="organizer" selected="selected">Organizer</option>--}}
                        {{--                            </select>--}}
                        {{--                        </div>--}}
                    </div>

                </div>

            </div>
        </div>
    </div>
    <div class="card-footer py-2 flex-right" id="m_panel_footer">
        <button onclick="EventCalendarContainer.storeEventCalendar($(this))" type="button"
                id="kt_modal_add_event_submit" class="btn btn-primary rounded-0">
            <span class="indicator-label">Save</span>

        </button>
    </div>
</form>
<script>
    r = document.querySelector("#kt_calendar_datepicker_allday");
    r.addEventListener("click", (e => {
        if (e.target.checked) {
            $('.end_date_picker_col').removeClass('d-none')
            $('.timepicker_col').addClass('d-none')
        } else {
            $('.end_date_picker_col').addClass('d-none')
            $('.timepicker_col').removeClass('d-none')
        }
    }));

    var arrows;
    if (KTUtil.isRTL()) {
        arrows = {
            leftArrow: '<i class="la la-angle-right"></i>',
            rightArrow: '<i class="la la-angle-left"></i>'
        }
    } else {
        arrows = {
            leftArrow: '<i class="la la-angle-left"></i>',
            rightArrow: '<i class="la la-angle-right"></i>'
        }
    }

    $('.datepicker').datepicker({
        todayHighlight: true,
        setDate: new Date(),
        templates: arrows,
        autoclose: true,
        format: 'dd-mm-yyyy',
        orientation: "bottom"
    });

    $('.timepicker').timepicker({
        'step': function (i) {
            return (i % 2) ? 15 : 45;
        },
        'scrollDefault': 'now',
        'setTime': new Date(),
    });

    $('.add_new_notification_btn').click(function () {
        EventCalendarContainer.addNewNotification();
    });

    EventCalendarContainer.select2init()

    $('#guest_search_field').on('click', function () {
        serach = {};
        serach = {
            office_id: $('#office_id').val(),
            unit_id: $('#office_unit_id').val(),
            search_value: $('#search_key').val(),
        }
        EventCalendarContainer.showPreferredGuests(serach)
    });

    $('#kt_quick_panel_close').on('click', function () {
        EventCalendarContainer.loadCalendars({});
    });

</script>
