<div class="row gy-3 g-xl-8">
    <div class="col-md-12">
        <h1 class="h1 p-2 pl-5 mb-3 border-bottom">Dashboard</h1>
    </div>
</div>

<div style="background: white" class="ml-3 mr-5 table-search-header-wrapper mb-4 pt-3 pb-2 shadow-sm">
    <div class="row gy-5 g-xl-8">
        <div class="col-md-7 mt-2">
            <h3 class="p-0 pl-5 mb-3">Tasks</h3>
        </div>

        <div class="col-md-5">
            <div class="d-flex justify-content-md-end pr-3">
                <a onclick="Generic_Container.addTask($(this))" class="btn btn-sm btn-light-info btn-square mr-1"
                    href="javascript:;">
                    <i class="fas fa-plus-circle mr-1"></i>
                    Add New Task
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row gy-5 g-xl-8" style="max-height: 80vh; overflow-y: scroll">
    <div class="col-xl-4">
        <!--begin::List Widget 3-->
        <div class="card card-custom ml-3 mb-xl-8">
            <!--begin::Header-->
            <div class="card-header border-0 p-3 py-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label font-weight-bolder text-dark">My Tasks</span>
                </h3>
                <div class="card-toolbar"></div>
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body p-3" id="pending_task_area">
                @if (Arr::has($pending_tasks, 'data') && count($pending_tasks['data']) > 0)
                    @foreach ($pending_tasks['data'] as $pending_task)
                        @if ($pending_task['task_user'])
                            <div class="mb-8 task_item_div_{{ $pending_task['id'] }}">
                                <div class="d-flex align-items-center" id="task_item_area_{{ $pending_task['id'] }}">
                                    <span class="bullet bullet-vertical h-40px bg-warning mr-2"></span>
                                    <div class="flex-grow-1 mr-1">
                                        {{-- <a href="{{route('tasks.index')}}?task_setup=edit_{{$pending_task['id']}}_page_{{$pending_tasks['current_page']}}" class="text-gray-800 text-hover-primary fw-bolder fs-6">{{$pending_task['title_en']}} --}}
                                        <a href="javascript:;"
                                            class="text-gray-800 text-hover-primary fw-bolder fs-6">{{ $pending_task['title_en'] }}
                                            <span
                                                class="{{ $pending_task['has_event'] == 1 ? 'ml-2 text-danger fa fa-calendar' : '' }}"></span></a>
                                        @if ($pending_task['meta_data'])
                                            @php
                                                $meta_data = json_decode(base64_decode($pending_task['meta_data']), true);
                                            @endphp
                                        @else
                                            @php
                                                $meta_data = [];
                                            @endphp
                                        @endif
                                        <div class="d-flex">
                                            {{ \Carbon\Carbon::create($pending_task['task_start_date_time'])->format('d M, Y H:i A') }}
                                            -
                                            {{ \Carbon\Carbon::create($pending_task['task_end_date_time'])->format('d M, Y H:i A') }}
                                        </div>
                                        <div class="d-flex">
                                            {{ $pending_task['task_user']['comments'] }}
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="mt-4 ml-4">
                                        @if (isset($meta_data) && is_array($meta_data) && Arr::has($meta_data, 'type') && $pending_task['task_user']['user_type'] == 'organizer')
                                            <a href="{{ $meta_data['return_url'] }}" target="_blank"
                                                class="text-gray-800 text-hover-primary fw-bolder fs-6">
                                                <span
                                                    class="text-uppercase badge badge-info">{{ $meta_data['type'] }}</span>
                                            </a>
                                        @endif
                                        <span
                                            class="label label-lg label-inline font-weight-bold {{ $pending_task['task_user']['user_type'] == 'organizer' ? 'label-light-primary' : 'label-light-info' }} text-capitalize">{{ $pending_task['task_user']['user_type'] == 'organizer' ? 'Self' : $pending_task['task_organizer']['user_name_bn'] }}</span>
                                        <span
                                            class="label label-light-warning label-lg label-inline font-weight-bold text-capitalize">{{ Carbon\Carbon::now()->lt(\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $pending_task['task_end_date_time'])) ? 'Todo' : 'Pending' }}</span>
                                        <a href="javascript:;"
                                            class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-1 ml-1"
                                            data-toggle="tooltip" data-placement="top" title="Mark Completed"
                                            onclick="Generic_Container.updateTaskStatus($(this))"
                                            data-task-id="{{ $pending_task['id'] }}">
                                            <!--begin::Svg Icon | path: icons/duotune/art/art005.svg-->
                                            <span class="svg-icon svg-icon-3">
                                                <i class="fad fa-check-circle"></i>
                                                {{-- <i class="fa fa-check"></i> --}}
                                            </span>
                                        </a>
                                        <a href="javascript:;"
                                            class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-1 ml-1"
                                            onclick="Generic_Container.taskCommentPanel($(this))" data-toggle="tooltip"
                                            data-placement="top" title="Add Comment"
                                            data-task-id="{{ $pending_task['id'] }}">
                                            <span class="svg-icon svg-icon-3">
                                                <i class="fad fa-comment-alt"></i>
                                            </span>
                                        </a>
                                    </div>

                                </div>
                            </div>
                        @endif
                    @endforeach
                    @if ($pending_tasks['total'] >= $pending_tasks['to'])
                        <table style="width: 100%">
                            <thead>
                                <tr>
                                    <td style="width: 65%; text-align: right">
                                        {{ $pending_tasks['from'] }} - {{ $pending_tasks['to'] }} of
                                        {{ $pending_tasks['total'] }}
                                    </td>
                                    <td style="width: 35%; text-align: right">
                                        <button onclick="DailyDashboardContainer.paginate($(this))"
                                            data-area="pending_task_area" data-search-key=""
                                            data-paginate-page="{{ $pending_tasks['current_page'] - 1 }}"
                                            data-paginate-url="{{ route('tasks.pending') }}"
                                            class="pagination__button"
                                            {{ $pending_tasks['from'] <= 1 ? 'disabled' : '' }}>
                                            <i class="fa fa-chevron-left"></i></button>
                                        <button onclick="DailyDashboardContainer.paginate($(this))"
                                            data-area="pending_task_area" data-search-key=""
                                            data-paginate-page="{{ $pending_tasks['current_page'] + 1 }}"
                                            data-paginate-url="{{ route('tasks.pending') }}"
                                            class="pagination__button"
                                            {{ $pending_tasks['to'] >= $pending_tasks['total'] ? 'disabled' : '' }}>
                                            <i class="fa fa-chevron-right"></i>
                                        </button>
                                    </td>
                                </tr>
                            </thead>
                        </table>
                    @endif
                @else
                    <div class="h4 text-center mt-2">No task found.</div>
                @endif

            </div>
            <!--end::Body-->
        </div>
        <!--end:List Widget 3-->
    </div>
    <div class="col-xl-8">
        <!--begin::Tables Widget 9-->
        <div class="card card-custom mb-5 mb-xl-8">
            <!--begin::Header-->
            <div class="card-header border-0 py-5">
                <h3 class="mt-3 card-title align-items-start flex-column">
                    <span class="card-label font-weight-bolder text-dark">Assigned Tasks to Others</span>
                </h3>
                <div class="card-toolbar"></div>
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body py-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class='input-group'>
                            <input type="text" id="assigned_search_by_user_info" class="form-control form-control-solid"
                                title="Search by Name/Designation/Unit" placeholder="Name/Designation/Unit">
                            <div class="input-group-append">
                                <button class="input-group-text btn btn-icon btn-light"
                                    id="assigned_search_by_user_info_btn"
                                    onclick="DailyDashboardContainer.searchAssignedTaskByUserInfo($('#assigned_search_by_user_info'))">
                                    <i class="la la-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class='input-group'>
                            <input type="text" id="assigned_search_by_task_info" class="form-control form-control-solid"
                                title="Search by Task" placeholder="Task">
                            <div class="input-group-append">
                                <button class="input-group-text btn btn-icon btn-light"
                                    onclick="DailyDashboardContainer.searchAssignedTaskByTaskInfo($('#assigned_search_by_task_info'))"
                                    id="assigned_search_by_task_info_btn"><i class="la la-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class='input-group'>
                            <input type='text' class="form-control" readonly title="Select date & time range"
                                placeholder="Date & time range" id="assigned_search_by_datetime_range" />
                            <div class="input-group-append">
                                <button class="input-group-text btn btn-icon btn-light"
                                    id="assigned_search_by_datetime_range_btn"><i class="la la-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-center">
                        <span class="btn btn-icon btn-light-warning" id="reset_assigned_search" title="Reset"
                            onclick="DailyDashboardContainer.resetAssigneeLists($(this))"
                            data-area="assigned_user_table_area"><i class="fas fa-recycle"></i></span>
                    </div>
                </div>

                <div class="mt-5 navi navi-spacer-x-0 p-0" id="assigned_user_table_area"></div>
                <!--end::Table container-->
            </div>
            <!--begin::Body-->
        </div>
        <!--end::Tables Widget 9-->
    </div>
</div>


<div id="loading"></div>

<div class="card card-custom ml-3 mr-4 mb-5 mb-xl-8">
    <!--begin::Header-->
    <div class="card-header border-0 py-5">
        <h3 class="mt-3 card-title align-items-start flex-column">
            <span class="card-label font-weight-bolder text-dark">Events</span>
        </h3>
    </div>
    <!--end::Header-->
    <!--begin::Body-->
    <div class="card-body py-3">
        <div id="self_calendar"></div>
    </div>
</div>

{{-- <div id="self_calendar"></div> --}}

<section>
    <div id="task_comment_popover_content" class="d-none">
        <div class="input-group">
            <input type="text" id="popover_task_comment" class="form-control" placeholder="Task Comment"
                aria-label="Task Comment" aria-describedby="button-addon1">
            <input type="hidden" value="" id="popover_task_id">
            <button class="btn btn-primary btn_save_task_comment"
                onclick="DailyDashboardContainer.saveTaskComment($(this))" type="button" data-bs-toggle="popover"
                data-bs-placement="bottom" data-bs-html="true" data-bs-title="Search">
                <i class="fas fa-save"></i>
            </button>
            <button class="btn btn-danger popover-dismiss-btn" onclick="DailyDashboardContainer.dismissPopover()"
                type="button" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-html="true"
                data-bs-title="Search">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</section>
<script>
    $('[data-toggle="tooltip"]').tooltip({
        trigger: 'hover'
    })
    var todayDate = moment().startOf('day');
    var YM = todayDate.format('YYYY-MM');
    var YESTERDAY = todayDate.clone().subtract(1, 'day').format('YYYY-MM-DD');
    var TODAY = todayDate.format('YYYY-MM-DD');
    var TOMORROW = todayDate.clone().add(1, 'day').format('YYYY-MM-DD');
    var DailyDashboardContainer = {
        calendarEl: document.getElementById('self_calendar'),
        loadCalendarEvents: function(event_extra_param = {}) {
            calendar = new FullCalendar.Calendar(DailyDashboardContainer.calendarEl, {
                plugins: ['dayGrid', 'timeGrid', 'list'],
                themeSystem: 'bootstrap',
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                // height: 400,
                // contentHeight: 300,
                // aspectRatio: 3,
                nowIndicator: true,
                dayMaxEvents: true, // allow "more" link when too many events
                selectable: true,
                views: {
                    dayGridMonth: {
                        buttonText: 'month'
                    },
                    timeGridWeek: {
                        buttonText: 'week'
                    },
                    timeGridDay: {
                        buttonText: 'day'
                    },
                    listWeek: {
                        buttonText: 'list'
                    }
                },
                eventTimeFormat: { // like '14:30:00'
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: true
                },
                defaultView: 'timeGridDay',
                editable: true,
                eventLimit: true, // allow "more" link when too many events
                navLinks: true,
                events: {
                    url: '{{ route('cal-event.all.officer-id') }}',
                    extraParams: event_extra_param,
                    failure: function() {
                        document.getElementById('script-warning').style.display = 'block'
                    },
                },
                loading: function(bool) {
                    console.log('loading')
                    document.getElementById('loading').style.display =
                        bool ? 'block' : 'none';
                },
                eventClick: function(e) {
                    console.log(e)
                    event_id = e.event.extendedProps.event_id
                    DailyDashboardContainer.eventDescriptionShow(event_id)
                },
                eventMouseEnter: function(e) {
                    console.log(e)
                    DailyDashboardContainer.eventDescriptionPopOverShow(e)
                },
                eventMouseLeave: function(e) {
                    console.log(e)
                    $('.popover').popover('hide');
                },
            });
            calendar.render();
        },
        eventDescriptionShow: function(event_id) {
            $('.popover').popover('hide');
            url = '{{ route('cal-event.show-event') }}';
            data = {
                event_id
            }
            ajaxCallAsyncCallbackAPI(url, data, 'post', function(response) {
                if (response.status != 'error') {
                    $('#m_panel_body').html(response)
                    $('.offcanvas-title').html('Event Description')
                    $('#m_panel_toggle').click(quickPanelToggler());
                } else {
                    toastr.error('Trouble in viewing event');
                    console.log(response)
                }
            })
        },
        eventDescriptionPopOverShow: function(e) {
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
            console.log(event_detail);
            start = event_detail.allDay ? moment(event_detail.startDate).format("Do MMM, YYYY") : moment(
                event_detail.startDate).format("Do MMM, YYYY - h:mm a");
            // end = event_detail.allDay ? moment(event_detail.endDate).format("Do MMM, YYYY") : moment(
            //     event_detail.endDate).format("Do MMM, YYYY - h:mm a");

            new bootstrap.Popover(e.el, {
                container: 'body',
                title: '',
                html: true,
                placement: 'right',
                sanitize: false,
                content: '<div id="event_popover_" class="fw-bolder mb-2">' + event_detail.title +
                    '</div><div class="fs-7"><span class="fw-bold">Time:</span> ' + start
                // '</div><div class="fs-7 mb-4"><span class="fw-bold">End:</span> ' + end +
                // '</div><div onclick="DailyDashboardContainer.dismissPopover()" class="btn btn-danger popover-dismiss-btn w-100 btn-sm btn-icon"><button class="btn btn-danger popover-dismiss-btn btn-sm btn-icon" type="button"><i class="fas fa-times"></i></button></div>'
            }).show()

            // KTApp.initBootstrapPopover(e.el, popoverBody).show()

            $('.popover-dismiss').click(function() {
                $('.popover').popover('hide');
            })
        },
        searchAssignedTaskByUserInfo: function(elem) {
            url = '{{ route('tasks.search.assigned.user-info') }}'
            data = {
                search_key: $(elem).val()
            };
            ajaxCallAsyncCallbackAPI(url, data, 'post', function(response) {
                if (response.status == 'error') {
                    toastr.error('Error in searching.')
                } else {
                    $('#assigned_user_table_area').html(response)
                }
            });
        },

        addTaskFromDashboard: function(elem) {
            url = '{{ route('tasks.create') }}';
            data = {};
            ajaxCallAsyncCallbackAPI(url, data, 'post', function(response) {
                if (response.status != 'error') {
                    $('#m_panel_body').html(response)
                    $('#kt_calendar_datepicker_start_date').val(new Date().toISOString().split('T')[0])
                    $('#kt_calendar_datepicker_end_date').val(new Date().toISOString().split('T')[0])
                    $('.offcanvas-title').html('Create Task')
                    $('#m_panel_toggle').click(quickPanelToggler());
                } else {
                    toastr.error('Trouble in creating task');
                    console.log(response)
                }
            })
        },

        searchAssignedTaskByTaskInfo: function(elem) {
            url = '{{ route('tasks.search.assigned.task-info') }}'
            data = {
                search_key: $(elem).val()
            };
            ajaxCallAsyncCallbackAPI(url, data, 'post', function(response) {
                if (response.status == 'error') {
                    toastr.error('Error in searching.')
                } else {
                    $('#assigned_user_table_area').html(response)
                }
            });
        },

        searchAssignedTaskByDateTimeRange: function(elem) {
            url = '{{ route('tasks.search.assigned.datetime-range') }}'
            data = {
                date_time_range: $('#assigned_search_by_datetime_range').val()
            };
            ajaxCallAsyncCallbackAPI(url, data, 'post', function(response) {
                if (response.status == 'error') {
                    toastr.error('Error in searching.')
                } else {
                    $('#assigned_user_table_area').html(response)
                }
            });
        },
        paginate: function(elem) {
            next_page = $(elem).attr('data-paginate-page');
            url = $(elem).attr('data-paginate-url');
            search = $(elem).attr('data-search-key');
            area = $(elem).attr('data-area');
            data = {
                page: next_page,
                search_key: search
            };
            ajaxCallAsyncCallbackAPI(url, data, 'post', function(response) {
                $('#' + area).html(response)
            })

        },
        saveTaskComment: function(elem) {
            url = '{{ route('tasks.update.comment') }}'
            data = {
                task_id: $('#popover_task_id').val(),
                task_comment: $('.popover-body #popover_task_comment').val(),
            }
            ajaxCallAsyncCallbackAPI(url, data, 'post', function(response) {
                if (response.status == 'error') {
                    toastr.error('Error in adding comment.')
                } else {
                    toastr.success('Successfully Added Comment')
                }
                DailyDashboardContainer.dismissPopover()
                $('.menu-item.menu-item-active a').click()
            });
        },
        dismissPopover: function() {
            $('.popover').popover('hide');
        },

        resetAssigneeLists: function(elem) {
            url = '{{ route('tasks.user.assigned') }}';

            $('#assigned_search_by_user_info').val('')
            $('#assigned_search_by_task_info').val('')
            $('#assigned_search_by_datetime_range').val('')

            ajaxCallAsyncCallback(url, {}, 'html', 'post', function(response) {
                if (response.status == 'error') {
                    toastr.error('Error in searching.')
                } else {
                    $('#assigned_user_table_area').html(response)
                }
            })
        },
    };
    DailyDashboardContainer.loadCalendarEvents();

    $(document).ready(function() {
        $('#assigned_search_by_datetime_range').val('');
        DailyDashboardContainer.resetAssigneeLists();
    });

    $('#assigned_search_by_datetime_range').daterangepicker({
        buttonClasses: ' btn',
        applyClass: 'btn-primary',
        cancelClass: 'btn-secondary',

        timePicker: true,
        timePickerIncrement: 30,
        locale: {
            format: 'DD/MM/YYYY h:mm A'
        }
    }, function(start, end, label) {
        $('#assigned_search_by_datetime_range .form-control').val(start.format('MM/DD/YYYY h:mm A') + ' / ' +
            end.format('MM/DD/YYYY h:mm A'));
    });

    $('#assigned_search_by_datetime_range_btn').click(function(e) {
        DailyDashboardContainer.searchAssignedTaskByDateTimeRange($(this));
    })

    $('.callPopover').click(function(e) {
        $('#popover_task_id').val($(this).attr('data-task-id'));
        $('.popover').popover('hide');
        new bootstrap.Popover(document.querySelector('.callPopover'), {
            container: 'body',
            title: 'Add Comment',
            html: true,
            placement: 'bottom',
            sanitize: false,
            content() {
                return document.querySelector('#task_comment_popover_content').innerHTML;
            }
        }).show()
    })

    $('.popover-dismiss').click(function() {
        $('.popover').popover('hide');
    })
</script>
