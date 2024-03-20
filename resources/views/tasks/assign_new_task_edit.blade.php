<form class="form" id="kt_modal_update_task_form">
    <div class="row">
        <div class="col-md-8">
            <!--begin::Input group-->
            <div class="fv-row mb-3">
                <input type="text" class="form-control form-control-solid" placeholder="Task Title" {{$task_user['user_type'] == 'organizer' ? '' : 'readonly'}} name="task_title_en" value="{{$task['title_en']}}"/>
            </div>
            <div class="fv-row mb-3">
                <textarea name="task_description" placeholder="Description" id="" class="form-control form-control-solid" {{$task_user['user_type'] == 'organizer' ? '' : 'disabled'}} cols="10" rows="5">{{$task['description']}}</textarea>
            </div>
            <div class="row row-cols-lg-4 g-10">
                <div class="col">
                    <div class="fv-row mb-3">
                        <input class="form-control form-control-solid datepicker" name="start_date" placeholder="Pick start date" id="kt_calendar_datepicker_start_date" {{$task_user['user_type'] == 'organizer' ? '' : 'disabled'}} value="{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$task['task_start_date_time'])->format('d-m-Y')}}"/>
                    </div>
                </div>
                <div class="col timepicker_col" data-kt-calendar="timepicker">
                    <div class="fv-row mb-3">
                        <input class="form-control form-control-solid timepicker" name="start_time" placeholder="Pick start time" id="kt_calendar_datepicker_start_time" {{$task_user['user_type'] == 'organizer' ? '' : 'disabled'}} value="{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$task['task_start_date_time'])->format('H:i A')}}"/>
                    </div>
                </div>
                <div class="col end_date_picker_col">
                    <div class="fv-row mb-9">
                        <input class="form-control form-control-solid datepicker" name="end_date" placeholder="Pick end date" id="kt_calendar_datepicker_end_date" {{$task_user['user_type'] == 'organizer' ? '' : 'disabled'}} value="{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$task['task_start_date_time'])->format('d-m-Y')}}"/>
                    </div>
                </div>
                <div class="col timepicker_col" data-kt-calendar="timepicker">
                    <div class="fv-row mb-3">
                        <input class="form-control form-control-solid timepicker" name="end_time" placeholder="Pick end time" id="kt_calendar_datepicker_end_time" {{$task_user['user_type'] == 'organizer' ? '' : 'disabled'}} value="{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$task['task_end_date_time'])->format('H:i A')}}"/>
                    </div>
                </div>
            </div>
            <div class="fv-row mb-3">
                <!--begin::Checkbox-->
                <label class="form-check form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="{{$user_task_event}}" id="task_to_event" {{$user_task_event == 1 ? 'checked' : ''}}/>
                    <span class="form-check-label fw-bold" for="task_to_event">Add this task to calendar</span>
                </label>
                <!--end::Checkbox-->
            </div>
            <div class="g-10 mb-3">
                <div class="event_notification_area">
                    @foreach($task_notifications as $task_notification)
                        <div class="row event_notification w-100 mb-2" id="event_notification_{{$task_notification['id']}}">
                            <div class="col d-flex align-items-center">
                                <i class="fa fa-bell mr-2 pr-2"></i>
                                <select class="select-select2 form-select form-select-solid notification_medium" data-notification-length="{{$task_notification['id']}}" id="">
                                    <option value="email" {{$task_notification['notification_medium'] == 'email' ? 'selected' : ''}}>Email</option>
                                    <option value="notification" {{$task_notification['notification_medium'] == 'notification' ? 'selected' : ''}}>Notification</option>
                                </select>
                            </div>
                            <div class="col">
                                <input type="number" value="{{$task_notification['interval']}}" class="form-control form-control-solid notification_time" data-notification-length="{{$task_notification['id']}}">
                            </div>
                            <div class="col d-flex align-items-center">
                                <select class="select-select2 form-select form-select-solid notification_unit" data-notification-length="{{$task_notification['id']}}">
                                    <option value="minutes" {{$task_notification['unit'] == 'minutes' ? 'selected' : ''}}>minutes</option>
                                    <option value="hours" {{$task_notification['unit'] == 'hours' ? 'selected' : ''}}>hours</option>
                                    <option value="days" {{$task_notification['unit'] == 'days' ? 'selected' : ''}}>days</option>
                                    <option value="weeks" {{$task_notification['unit'] == 'weeks' ? 'selected' : ''}}>week</option>
                                </select>
                                <div class="ml-2 pl-2 fa fa-trash remove_notification_btn text-danger" title="Remove" onclick="Generic_Container.removeTaskNotification($(this))" data-notification-length="{{$task_notification['id']}}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="btn btn-light btn-hover-rise add_new_notification_btn mt-3" onclick="Generic_Container.addNewTaskNotification()"><i class="fa fa-plus mr-2"></i>Add New Notification</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="search_box position-relative">
                    <span class="svg-icon svg-icon-2 svg-icon-lg-1 svg-icon-gray-500 position-absolute top-50 ml-5 translate-middle-y mt-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black"></rect>
                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black"></path>
                        </svg>
                    </span>
                <input autocomplete="off" type="search" class="form-control form-control-lg form-control-solid px-15" id="guest_search_field" name="search_key" value="" placeholder="username/name/email">
                <button type="button" class="btn btn-danger" onclick="resetGuestSearchedArea()">Reset</button>
            </div>

            <div class="searched_users_area max-h-200px scroll-y bg-light p-2" onmouseenter="customScrollInit('searched_users_area', 'class')" style="overflow-y: auto"></div>

            <div class="max-h-300px added_users" style="overflow-y: auto;"></div>
        </div>
    </div>
    <input type="hidden" value="{{$task['id']}}" name="task_id">
    <input type="hidden" value="{{json_encode($task_user)}}" name="task_user">
    <div class="card-footer flex-right p-0 py-2" id="m_panel_footer">
        <button onclick="Generic_Container.assignAndCreateNewTask($(this))" type="button" id="kt_modal_add_event_submit" class="btn btn-primary rounded-0">
            <span class="indicator-label">Assign And Create New Task</span>

        </button>
    </div>
</form>
<script>
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

    Generic_Container.select2init()

    $('#guest_search_field').on('keypress', function (e) {
        console.log(e)
        if (e.which == 13) {
            TaskContainer.showPreferredGuests($(this), 'assign')
        }
    })

</script>
