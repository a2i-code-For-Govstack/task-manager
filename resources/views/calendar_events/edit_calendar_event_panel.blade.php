<style>
    textarea {
        max-height: 100px !important;
        height: 100px !important;
        overflow-y: scroll !important;
    }
</style>

@php
    $can_edit = false;
    if ($event_user && ($event_user['user_type'] == 'organizer' || $event_user['user_type'] == 'requester' || $event_user['user_type'] == 'can_edit')) {
        $can_edit = true;
    }
@endphp
<!--begin::Form-->
<form class="form" action="#" id="kt_modal_add_event_form">
    <div class="modal-body pt-1 pb-1">
        <div class="row">
            <div class="col-md-7">
                <!--begin::Input group-->
                <div class="fv-row mb-3">
                    <label for="event_title" class="font-weight-bolder">Event Title <span class="text-danger">(*)</span></label>

                    <input id="event_title" type="text" class="form-control form-control-solid"
                           {{ $can_edit ? '' : 'readonly' }}
                           value="{{ $event_details['event_title_en'] }}" placeholder="Add Title And Time"
                           name="title"/>
                </div>
                <div class="fv-row mb-3">
                    <label for="event_description" class="font-weight-bolder">Event Description</label>

                    <textarea name="description" placeholder="Description" id="event_description"
                              class="form-control form-control-solid"
                              cols="10"
                              {{ $can_edit ? '' : 'readonly' }}
                              rows="3">{{ $event_details['event_description'] }}</textarea>
                </div>
                <div class="fv-row mb-3">
                    <label for="event_location" class="font-weight-bolder">Event Location <span
                            class="text-danger">(*)</span></label>
                    <input id="event_location" type="text" class="form-control form-control-solid"
                           placeholder="Event Location"
                           name="location" {{ $can_edit ? '' : 'readonly' }}
                           value="{{ $event_details['event_location'] }}"/>
                </div>
                <div class="fv-row mb-3">
                    <label class="form-check form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" value="" {{ $can_edit ? '' : 'readonly' }}
                        id="kt_calendar_datepicker_allday"/>
                        <span class="form-check-label fw-bold" for="kt_calendar_datepicker_allday">All Day</span>
                    </label>
                </div>
                <span class="pr-1" style="font-weight: bold">Select Date and Time</span>

                <div class="row row-cols-lg-3 g-10">
                    <div class="col">
                        <div class="fv-row mb-3">
                            <input class="form-control form-control-solid datepicker" name="start_date"
                                   placeholder="Pick start date" id="kt_calendar_datepicker_start_date"
                                   {{ $can_edit ? '' : 'readonly' }}
                                   value="{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event_details['event_start_date_time'])->format('d-m-Y') }}"/>
                        </div>
                    </div>
                    <div class="col timepicker_col" data-kt-calendar="timepicker">
                        <div class="fv-row mb-3">
                            <input class="form-control form-control-solid timepicker" name="start_time"
                                   placeholder="Pick start time" id="kt_calendar_datepicker_start_time"
                                   {{ $can_edit ? '' : 'readonly' }}
                                   value="{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event_details['event_start_date_time'])->format('H:i A') }}"/>
                        </div>
                    </div>
                    <div class="col timepicker_col" data-kt-calendar="timepicker">
                        <div class="fv-row mb-3">
                            <input class="form-control form-control-solid timepicker" name="end_time"
                                   placeholder="Pick end time" id="kt_calendar_datepicker_end_time"
                                   {{ $can_edit ? '' : 'readonly' }}
                                   value="{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event_details['event_end_date_time'])->format('H:i A') }}"/>
                        </div>
                    </div>
                    <div class="col end_date_picker_col d-none">
                        <div class="fv-row mb-9">
                            <input class="form-control form-control-solid datepicker" name="end_date"
                                   placeholder="Pick end date" id="kt_calendar_datepicker_end_date"
                                   {{ $can_edit ? '' : 'readonly' }}
                                   value="{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event_details['event_end_date_time'])->format('d-m-Y') }}"/>
                        </div>
                    </div>
                </div>
                <!--end::Input group-->
                <div class="row row-col-lg-2 mb-3">
                    <div class="col">
                        <input type="text" class="form-control form-control-solid" placeholder="Meeting link"
                               name="event_previous_link" value="{{ $event_details['event_previous_link'] }}"
                            {{ $can_edit ? '' : 'readonly' }} />
                    </div>
                </div>
                <div class="row d-flex align-items-center flex-center mb-3" style="width: 85%">
                    <div class="col-6">
                        <p class="mb-0"><i class="la la-user-circle"></i> Organizer <span
                                class="fs-5 fw-bolder text-gray-900">{{ Arr::has($organizer, 'user_name_bn') ? $organizer['user_name_bn'] : '' }}</span>
                        </p>
                    </div>
                    <div class="col">
                        <input type="hidden" id="tag_color" name="tag_color" value="#da9898">
                        <div class="custom-dropdown" id="colorDropdown">
                            <div title="Select Preferable color for events" class="color-circle" id="selected_color"
                                 style="background-color: #da9898;"
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
                    <div class="col d-flex align-items-center">
                        <span class="pr-1" style="font-weight: bold">Visibility</span>
                        <select class="select-select2 form-select form-select-solid" name="visibility" id="">
                            <option value="public"
                                {{ $event_details['event_visibility'] == 'public' ? 'selected' : '' }}>Public
                            </option>
                            <option value="private"
                                {{ $event_details['event_visibility'] == 'private' ? 'selected' : '' }}>Private
                            </option>
                        </select>
                    </div>
                </div>
                @if ($event_user)
                    <div>
                        <p class="font-weight-bold">Reminder Notification</p>
                    </div>
                    <div class="g-10 mb-3">
                        <div class="event_notification_area">

                            @foreach ($event_notifications as $event_notification)
                                <div class="row event_notification w-100 mb-2"
                                     id="event_notification_{{ $event_notification['id'] }}">
                                    <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3 d-flex align-items-center">
                                        <i class="fa fa-bell mr-2 pr-2"></i>
                                        <select class="select-select2 form-select form-select-solid notification_medium"
                                                data-notification-length="{{ $event_notification['id'] }}" id="">
                                            <option value="email"
                                                {{ $event_notification['notification_medium'] == 'email' ? 'selected' : '' }}>
                                                Email
                                            </option>
                                            <option value="notification"
                                                {{ $event_notification['notification_medium'] == 'notification' ? 'selected' : '' }}>
                                                Notification
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3">
                                        <input type="number" value="{{ $event_notification['interval'] }}"
                                               class="form-control form-control-solid notification_time"
                                               data-notification-length="{{ $event_notification['id'] }}">
                                    </div>
                                    <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3 d-flex align-items-center">
                                        <i class="fa fa-null d-lg-none d-md-none d-sm-block mr-2 pr-2"></i>
                                        <select class="select-select2 form-select form-select-solid notification_unit"
                                                data-notification-length="{{ $event_notification['id'] }}">
                                            <option value="minutes"
                                                {{ $event_notification['unit'] == 'minutes' ? 'selected' : '' }}>
                                                minutes
                                            </option>
                                            <option value="hours"
                                                {{ $event_notification['unit'] == 'hours' ? 'selected' : '' }}>hours
                                            </option>
                                            <option value="days"
                                                {{ $event_notification['unit'] == 'days' ? 'selected' : '' }}>days
                                            </option>
                                            <option value="weeks"
                                                {{ $event_notification['unit'] == 'weeks' ? 'selected' : '' }}>week
                                            </option>
                                        </select>
                                        <div
                                            class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3 ml-2 pl-2 fa fa-trash remove_notification_btn text-danger text-right"
                                            onclick="EventCalendarContainer.removeNotification($(this))"
                                            title="Remove"
                                            data-notification-length="{{ $event_notification['id'] }}"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="btn btn-light btn-hover-rise add_new_notification_btn mt-3"><i
                                class="fa fa-plus mr-2"></i>Add New Notification
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-md-5">
                @if ($event_user)
                    <div class="search_box position-relative">
                        <x-office-select grid="12" unit="true"/>
                        <span
                            class="svg-icon svg-icon-2 svg-icon-lg-1 svg-icon-gray-500 position-absolute top-50 ml-5 translate-middle-y mt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none">
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
                        <br>
                    </div>
                @endif
                <div class="searched_users_area max-h-200px bg-light p-2" style="overflow-y: auto"
                     onmouseenter="customScrollInit('searched_users_area', 'class')"></div>
                <div class="max-h-300px added_users" style="overflow-y: auto">
                    @foreach ($event_details['event_guests'] as $event_guest)
                        <div id="event_user_{{$event_guest['id']}}"
                             class="cursor-pointer d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed added-user-area"
                             data-added-user-email-area='{{ $event_guest['user_email'] }}'
                             data-added-user-info='{{ json_encode_escaped($event_guest) }}'>

                            <div class="d-flex align-items-center" style="width: 80%;">
                                <div class="symbol symbol-25px symbol-circle">
                                    <img alt="Pic" src="{{ asset('assets/media/svg/avatars/blank.svg') }}"/>
                                </div>
                                <div class="ml-5">
                                    <a href="javascript:;"
                                       class="fs-5 fw-bolder text-gray-900 text-hover-dark mb-2">{{ $event_guest['user_name_bn'] }}
                                        <span
                                            class="ml-2 badge badge-{{$event_guest['user_type'] == 'organizer' ? 'primary':'info'}}">{{ $event_guest['user_type'] }}</span></a>
                                    <div class="fw-bold text-muted">{{ $event_guest['user_email'] }}</div>
                                </div>
                            </div>
                            {{--                            <div class="ms-2">--}}
                            {{--                                <select--}}
                            {{--                                    class="select-select2 form-select form-select-solid form-select-sm user-permission-select"--}}
                            {{--                                    readonly="true">--}}
                            {{--                                    <option value="organizer" selected="selected">{{ $event_guest['user_type'] }}--}}
                            {{--                                    </option>--}}
                            {{--                                </select>--}}
                            {{--                            </div>--}}
                            @if($event_guest['user_type'] != 'organizer')
                                <div style="width:20%">
                                    <div class="ml-2 pl-2 fa fa-times"
                                         onclick="EventCalendarContainer.unAssignUser($(this))"
                                         data-event-user-id='{{$event_guest['id']}}'></div>
                                </div>
                            @endif
                        </div>

                    @endforeach
                </div>
            </div>
            <input type="hidden" value="{{ $event_details['id'] }}" name="event_id">
            <input type="hidden" value="{{ json_encode($event_user) }}" name="event_user">
        </div>
        <div class="card-footer py-2 flex-right" id="m_panel_footer">
            @if ($event_user)
                <button onclick="EventCalendarContainer.updateEventCalendar($(this))" type="button"
                        id="kt_modal_add_event_submit" class="btn btn-primary rounded-0">
                    <span class="indicator-label">Update</span>
                </button>
            @endif
            @if ($event_user && $event_user['user_type'] == 'organizer')
                <button onclick="EventCalendarContainer.deleteEvent({{ $event_details['id'] }})" type="button"
                        id="delete_event" class="btn btn-danger rounded-0">
                    <span class="indicator-label">Delete</span>
                </button>
                @if($event_user && $event_user['user_type'] == 'organizer' && ($event_details['recurrence'] !='none' || $event_details['recurrent_cal_id']))
                    <button onclick="EventCalendarContainer.deleteRecurringEvent({{ $event_details['id'] }})"
                            type="button"
                            id="delete_event" class="btn btn-danger rounded-0">
                        <span class="indicator-label">Delete Recurring Events</span>
                    </button>
                @endif
            @endif
        </div>
    </div>
</form>
<script>
    $(document).ready(function () {
        @if ($event_details['all_day'] == 1)
        $('#kt_calendar_datepicker_allday').click()
        @endif
        @if ($event_user)
        $('#tag_color').val('{{ $event_user['tag_color'] }}').trigger('change');
        @endif
    });
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

    $('.datepicker').datepicker({
        todayHighlight: true,
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
