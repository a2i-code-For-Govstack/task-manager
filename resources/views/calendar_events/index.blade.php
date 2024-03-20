@extends('layouts.full_width')

@section('styles')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1 class="h1 p-2 pl-5 mb-3 border-bottom">Events</h1>
        </div>
        <div class="col-md-3">
            <div class="calendar-container"></div>
        </div>
        <div class="col-md-9">
            <div class="card card-custom">
                <div class="card-header">
                    <h2 id='showing_calendar_of_title'></h2>
                    <div class="card-toolbar">
                        <button class="btn btn-flex btn-primary" data-kt-calendar="add"
                                onclick="EventCalendarContainer.createEventPanelShow($(this))">
                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                            <span class="svg-icon svg-icon-2"><i class="fa fa-plus text-white"></i></span>
                            Add Event
                        </button>
                    </div>
                </div>
                <div class="card-body" id="calendar_events">
                    <div id='loading'><i class="fa fa-spinner"></i></div>
                    <div id='script-warning'></div>
                    <div id="load_event_calendar_area"></div>
                </div>
            </div>

        </div>
    </div>
    <div id="create_calendar_modal_area">

    </div>
@endsection
@section('scripts')
    <script>
        var todayDate = moment().startOf('day');
        var YM = todayDate.format('YYYY-MM');
        var YESTERDAY = todayDate.clone().subtract(1, 'day').format('YYYY-MM-DD');
        var TODAY = todayDate.format('YYYY-MM-DD');
        var TOMORROW = todayDate.clone().add(1, 'day').format('YYYY-MM-DD');
        var calendar;

        var EventCalendarContainer = {
            calendarEl: document.getElementById('load_event_calendar_area'),

            loadCalendarEvents: function (event_extra_param = {}) {
                if (event_extra_param && event_extra_param['user_email']) {
                    url = '{{ route('cal-event.all.email') }}'
                } else {
                    url = '{{ route('cal-event.all.officer-id') }}'
                }
                $('#load_event_calendar_area').html('')
                $('.popover').popover('hide');
                calendar = new FullCalendar.Calendar(EventCalendarContainer.calendarEl, {
                    plugins: ['dayGrid', 'timeGrid', 'list', 'interaction'],
                    themeSystem: 'bootstrap',
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                    },
                    nowIndicator: true,
                    dayMaxEvents: true, // allow "more" link when too many events
                    selectable: true,
                    views: {
                        dayGridMonth: {
                            buttonText: 'Month'
                        },
                        timeGridWeek: {
                            buttonText: 'Week'
                        },
                        timeGridDay: {
                            buttonText: 'Day'
                        },
                        listWeek: {
                            buttonText: 'List'
                        }
                    },
                    defaultView: 'dayGridMonth',
                    editable: true,
                    eventLimit: true, // allow "more" link when too many events
                    navLinks: true,
                    eventStartEditable: false,
                    disableDragging: true,
                    events: {
                        url: url,
                        extraParams: event_extra_param,
                        failure: function (resp) {
                            console.log(resp)
                            document.getElementById('script-warning').style.display = 'block'
                        },
                        success: function (resp) {
                            // EventCalendarContainer.loadCalendars(event_extra_param);
                        }
                    },
                    eventTimeFormat: { // like '14:30:00'
                        hour: '2-digit',
                        minute: '2-digit',
                        meridiem: true
                    },
                    loading: function (bool) {
                        document.getElementById('loading').style.display =
                            bool ? 'block' : 'none';
                    },
                    select: function (e) {
                        EventCalendarContainer.createEventPanelShow(e);
                    },
                    eventClick: function (e) {
                        event_id = e.event.extendedProps.event_id
                        EventCalendarContainer.eventDescriptionShow(event_id)
                    },
                    eventMouseEnter: function (e) {
                        EventCalendarContainer.eventDescriptionPopOverShow(e)
                    },

                    eventMouseLeave: function (e) {
                        $('.popover').popover('hide');
                    },
                });
                calendar.render();
            },

            loadCalendars: function (event_extra_param = {}) {
                dates = EventCalendarContainer.getCalendarDateRange();
                start = new Date(dates.start).toISOString()
                end = new Date(dates.end).toISOString()
                url = '{{ route('cal-event.load-calendars') }}';
                ajaxCallAsyncCallbackAPI(url, {
                    start,
                    end,
                    event_extra_param
                }, 'post', function (response) {
                    if (response.status != 'error') {
                        $('.calendar-container').html(response)
                    } else {
                        toastr.error('Trouble in creating event');
                        console.log(response)
                    }
                })
            },

            eventDescriptionPopOverShow: function (e) {
                $('.popover').popover('hide');
                event_detail = {
                    id: e.event.extendedProps.event_id,
                    title: e.event.title,
                    description: e.event.extendedProps.description,
                    location: e.event.extendedProps.location,
                    startDate: e.event.start,
                    endDate: e.event.end,
                    allDay: e.event.allDay
                }
                console.log(event_detail)

                start = event_detail.allDay ? moment(event_detail.startDate).format("Do MMM, YYYY") : moment(
                    event_detail.startDate).format("Do MMM, YYYY - h:mm a");
                // end = event_detail.allDay ? moment(event_detail.endDate).format("Do MMM, YYYY") : moment(event_detail.endDate).format("Do MMM, YYYY - h:mm a");

                new bootstrap.Popover(e.el, {
                    container: 'body',
                    title: '',
                    html: true,
                    placement: 'right',
                    sanitize: false,
                    content: '<div id="event_popover_" class="fw-bolder mb-2">' + event_detail.title +
                        '</div><div class="fs-7"><span class="fw-bold">Time:</span> ' + start
                    // '</div><div class="fs-7 mb-4"><span class="fw-bold">End:</span> ' + end + '</div>'
                }).show()

                $('.popover-dismiss').click(function () {
                    $('.popover').popover('hide');
                })
            },

            createEventPanelShow: function (e) {
                $('.popover').popover('hide');
                url = '{{ route('cal-event.create-event') }}';
                data = {};
                ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                    if (response.status != 'error') {
                        $('.office-select-div').remove();
                        $('#m_panel_body').html(response)
                        if (e.startStr) {
                            $('#kt_calendar_datepicker_start_date').val(e.startStr)
                            $('#kt_calendar_datepicker_end_date').val(e.startStr)
                        } else {
                            $('#kt_calendar_datepicker_start_date').val(new Date().toISOString().split('T')[
                                0])
                            $('#kt_calendar_datepicker_end_date').val(new Date().toISOString().split('T')[
                                0])
                        }
                        $('.offcanvas-title').html('Add Event')
                        $('#m_panel_toggle').click(quickPanelToggler());
                    } else {
                        toastr.error('Trouble in creating event');
                        console.log(response)
                    }
                })
            },

            eventDescriptionShow: function (event_id) {
                $('.popover').popover('hide');
                url = '{{ route('cal-event.edit-event') }}';
                data = {
                    event_id
                }
                ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                    if (response.status != 'error') {
                        $('.office-select-div').remove();
                        $('#m_panel_body').html(response)
                        $('.offcanvas-title').html('View Event')
                        $('#m_panel_toggle').click(quickPanelToggler('60%'));
                    } else {
                        toastr.error('Trouble in viewing event');
                        console.log(response)
                    }
                })
            },

            dismissModal: function (selector) {
                $(selector).modal('hide')
            },

            storeEventCalendar: function (elem) {
                KTApp.block('#kt_quick_panel');
                data = $('#kt_modal_add_event_form').serializeArray();
                if ($('#kt_modal_add_event_form').find('#kt_calendar_datepicker_allday').is(':checked')) {
                    data.push({
                        name: 'all_day',
                        value: 1
                    });
                } else {
                    data.push({
                        name: 'all_day',
                        value: 0
                    });
                }

                users = {}
                notifications = {};
                $('.added-user-area').each(function (i, elem) {
                    user = JSON.parse($(elem).attr('data-added-user-info'))
                    user_type = $(elem).find('.user-permission-select').val()
                    user['user_type'] = user_type;
                    users[i] = user;
                });

                $('.event_notification').each(function (i, elem) {
                    medium = $(elem).find('.notification_medium').val()
                    interval = $(elem).find('.notification_time').val()
                    unit = $(elem).find('.notification_unit').val()
                    notification = {
                        medium,
                        interval,
                        unit,
                        is_dispatched: 0
                    }
                    if (medium) {
                        notifications[i] = notification
                    }
                })

                data.push({
                    name: 'invited_participants',
                    value: JSON.stringify(users)
                });
                data.push({
                    name: 'notifications',
                    value: JSON.stringify(notifications)
                });

                url = '{{ route('cal-event.store-event') }}';
                ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                    KTApp.unblock('#kt_quick_panel');
                    if (response.status === 'success') {
                        toastr.success('Successfully created event')
                        $('#m_panel_toggle').click(quickPanelToggler());
                        EventCalendarContainer.loadCalendarEvents();
                    } else {
                        toastr.error('Something went wrong')
                        console.log(response);
                    }
                });
                KTApp.unblock('#kt_quick_panel');
            },

            updateEventCalendar: function (elem) {
                KTApp.block('#kt_quick_panel');
                data = $('#kt_modal_add_event_form').serializeArray();
                tag_color = $('#tag_color').val() || '#88e5c7';
                data.push({
                    name: 'tag_color',
                    value: tag_color
                });

                if ($('#kt_modal_add_event_form').find('#kt_calendar_datepicker_allday').is(':checked')) {
                    data.push({
                        name: 'all_day',
                        value: 1
                    });
                } else {
                    data.push({
                        name: 'all_day',
                        value: 0
                    });
                }
                users = {}
                notifications = {};
                $('.added-user-area').each(function (i, elem) {
                    user = JSON.parse($(elem).attr('data-added-user-info'))
                    user_type = $(elem).find('.user-permission-select').val()
                    user['user_type'] = user_type;
                    users[i] = user;
                });

                $('.event_notification').each(function (i, elem) {
                    medium = $(elem).find('.notification_medium').val()
                    interval = $(elem).find('.notification_time').val()
                    unit = $(elem).find('.notification_unit').val()
                    notification = {
                        medium,
                        interval,
                        unit,
                        is_dispatched: 0
                    }
                    notifications[i] = notification
                })

                data.push({
                    name: 'invited_participants',
                    value: JSON.stringify(users)
                });
                data.push({
                    name: 'notifications',
                    value: JSON.stringify(notifications)
                });

                url = '{{ route('cal-event.update-event') }}';
                ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                    KTApp.unblock('#kt_quick_panel');
                    EventCalendarContainer.loadCalendars({})
                    if (response.status === 'success') {
                        toastr.success('Successfully created event')
                        $('#m_panel_toggle').click(quickPanelToggler());
                        EventCalendarContainer.loadCalendarEvents();
                    } else {
                        toastr.error('Something went wrong')
                        console.log(response);
                    }
                });
                KTApp.unblock('#kt_quick_panel');
            },

            showPreferredGuests: function (elem, type = 'addUser') {
                KTApp.block('#kt_content')
                url = '{{ route('guests.search-preferred-users') }}'
                data = {
                    office_id: elem.office_id,
                    unit_id: elem.unit_id,
                    search_key: elem.search_value,
                    type: type
                };
                ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                    KTApp.unblock('#kt_content')
                    if (type == 'view_other_calendar') {
                        $('.searched_users_area_for_others_calendar').html(response)
                    } else {
                        $('.searched_users_area').html(response)
                    }
                });
            },

            addUserInInviteList: function (elem) {
                inviting_user = $(elem).attr('data-user-info');
                inviting_user = JSON.parse(inviting_user)
                user_json = JSON.stringify(inviting_user).replace(/[\/\(\)\']/g, "&apos;");
                if ($(`[data-added-user-email-area="${inviting_user.user_email}"]`).length < 1) {
                    content = `<div class="py-4 border-bottom border-gray-300 border-bottom-dashed added-user-area" data-added-user-email-area='${inviting_user.user_email}' data-added-user-info='${user_json}'>
                                        <div class="d-flex align-items-center scroll scroll-x" style="width: 60%;">
                                            <div class="symbol symbol-25px symbol-circle">
                                                <img alt="Pic" src="{{ asset('assets/media/svg/avatars/blank.svg') }}"/>
                                            </div>
                                            <div class="ml-5">
                                                <a href="javascript:;" class="fs-5 fw-bolder text-gray-900 text-hover-dark mb-2">${inviting_user.user_name_bn}</a>
                                                <div class="fw-bold text-muted">${inviting_user.user_email}</div>
                                                <div class="fw-bold text-muted">${inviting_user.user_designation_name_bn}</div>
                                            </div>
                                        </div>
                                        <div class="ms-2" style="width:35%">
                                            <input class="user-permission-select" type="hidden" value="guest">
                                        </div>
                                    </div>`;
                    $('.added_users').append(content)

                    EventCalendarContainer.select2init()
                }
            },

            addNewNotification: function () {
                event_notification_length = $('.event_notification').length
                event_notification_length = !isNaN(event_notification_length) ? parseInt(
                    event_notification_length) : 0;
                event_notification_length += 1;
                content = `<div class="row event_notification w-100 mb-2" id="event_notification_${event_notification_length}">
                        <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3 d-flex align-items-center">
                            <i class="fa fa-bell mr-2 pr-2"></i>
                            <select class="select-select2 form-select form-select-solid notification_medium" data-notification-length="${event_notification_length}" id="">
                                <option value="email">Email</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3 d-flex align-items-center">
                            <input type="number" value="30" class="form-control form-control-solid notification_time" data-notification-length="${event_notification_length}">
                        </div>
                        <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3 d-flex align-items-center">
                            <i class="fa fa-null d-lg-none d-md-none d-sm-block mr-2 pr-2"></i>
                            <select class="select-select2 form-select form-select-solid notification_unit" data-notification-length="${event_notification_length}">
                                <option value="minutes">minutes</option>
                                <option value="hours">hours</option>
                                <option value="days">days</option>
                                <option value="weeks">weeks</option>
                            </select>
                            <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3 ml-2 pl-2 fa fa-trash remove_notification_btn text-danger text-right" title="Remove" onclick="EventCalendarContainer.removeNotification($(this))" data-notification-length="${event_notification_length}"></div>
                        </div>
                    </div>
                    <hr class="d-none d-lg-none d-md-none d-sm-block">`;
                $('.event_notification_area').append(content)

                EventCalendarContainer.select2init()
            },

            removeNotification: function (elem) {
                $('#event_notification_' + elem.attr('data-notification-length')).remove()
            },

            removeAddedUser: function (elem) {
                $('[data-added-user-email-area="' + $(elem).attr('data-added-user-email') + '"]').remove();
            },

            select2init: function (obj = {}) {
                $('.select-select2').select2({
                    minimumResultsForSearch: -1,
                });
            },

            getCalendarDateRange: function () {
                view = calendar.view;
                dates = {
                    start: view.activeStart,
                    end: view.activeEnd
                };
                return dates;
            },

            viewOtherCalendar: function (elem) {
                user_email = $(elem).attr('data-searched-email');
                EventCalendarContainer.loadCalendarEvents({
                    user_email
                });
                $('#showing_calendar_of_title').html('Showing Calendar of: ' + user_email +
                    '<i class="fa fa-times ml-3 text-danger" onclick="location.reload()"></i>')
            },
            deleteEvent: function (event_id) {
                url = '{{ route('cal-event.delete-event') }}'
                ajaxCallAsyncCallbackAPI(url, {
                    event_id
                }, 'post', function (response) {
                    if (response.status == 'success') {
                        toastr.success('Deleted Successfully')
                        $('#m_panel_toggle').click(quickPanelToggler());
                        EventCalendarContainer.loadCalendarEvents();
                    } else {
                        toastr.error('Error in deleting.')
                    }
                });
            },

            deleteRecurringEvent: function (event_id) {
                url = '{{ route('cal-event.delete-event') }}'
                ajaxCallAsyncCallbackAPI(url, {
                    event_id,
                    delete_type: 'recurring_all'
                }, 'post', function (response) {
                    if (response.status == 'success') {
                        toastr.success('Deleted Successfully')
                        $('#m_panel_toggle').click(quickPanelToggler());
                        EventCalendarContainer.loadCalendarEvents();
                    } else {
                        toastr.error('Error in deleting.')
                    }
                });
            },
            dismissPopover: function () {
                $('.popover').popover('hide');
            },
            unAssignUser: function (elem) {
                KTApp.block('#kt_content')
                url = '{{route('cal-event.unassign-event-user')}}';
                event_user_id = $(elem).attr('data-event-user-id');
                data = {event_user_id};
                ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                    KTApp.unblock('#kt_content');
                    if (response.status == 'success') {
                        $('#event_user_' + event_user_id).remove();
                        toastr.success('Successfully Done!');
                    }

                })
                KTApp.unblock('#kt_content')
            },
        };

        $(function () {
            EventCalendarContainer.loadCalendars({})
        });
        EventCalendarContainer.loadCalendarEvents();
    </script>
@endsection
