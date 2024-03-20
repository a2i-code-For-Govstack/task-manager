<style>
    textarea {
        max-height: 100px !important;
        height: 100px !important;
        overflow-y: scroll !important;
    }
</style>

@php
    $task_label = $parent_id ?  'Sub ' : '';
@endphp
<form class="form" id="kt_modal_update_task_form">
    <div class="row">
        <div class="col-md-12">
            <div class="fv-row mb-3">
                <label for="task_title" class="font-weight-bolder">Task Title <span
                        class="text-danger">(*)</span></label>
                <input id="task_title" type="text" class="form-control form-control-solid"
                       placeholder="{{$task_label}} Task Title"
                       {{$task_user['user_type'] == 'organizer' ? '' : 'readonly'}} name="task_title_en"
                       value="{{$task['title_en']}}"/>
            </div>
            <div class="fv-row mb-3">
                <label for="task_description" class="font-weight-bolder">Task Description</label>

                <textarea data-autogrow="false" name="task_description" placeholder="Description" id="task_description"
                          class="form-control form-control-solid"
                          {{$task_user['user_type'] == 'organizer' ? '' : 'disabled'}} cols="10"
                          rows="5">{{$task['description']}}</textarea>
            </div>
            @php
                $task_start_date_time = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$task['task_start_date_time'])->format('d/m/Y h:i A');
                $task_end_date_time = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$task['task_end_date_time'])->format('d/m/Y h:i A');
                $task_start_end_date_time = $task_start_date_time . ' - ' . $task_end_date_time;
            @endphp
            <div class="fv-row mb-3">
                <label for="task_start_end_date_time" class="font-weight-bolder">Select date & time range <span
                        class="text-danger">(*)</span></label>
                <input type='text' class="form-control task_start_end_date_time" readonly
                       name="task_start_end_date_time" value="{{$task_start_end_date_time}}"
                       {{$task_user['user_type'] == 'organizer' ? '' : 'disabled'}} autocomplete="off"
                       placeholder="Select date & time range"/>
            </div>

            <div class="g-10 mb-3">
                <label for="task_description" class="font-weight-bolder">Task Location</label>
                <input id="task_description" type="text" class="form-control form-control-solid"
                       value="{{$task['location']}}"
                       {{$task_user['user_type'] == 'organizer' ? '' : 'readonly'}} placeholder="{{$task_label}} Task Location"
                       name="location"/>
            </div>

            <div class="fv-row mb-3">
                <label class="form-check form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="{{$user_task_event}}"
                           id="task_to_event" {{$user_task_event == 1 ? 'checked' : ''}}/>
                    <span class="form-check-label fw-bold" for="task_to_event">Add this {{lcfirst($task_label)}} task to calendar</span>
                </label>
            </div>
            <div class="g-10 mb-3">
                <div>
                    <p class="font-weight-bold">Reminder Notification</p>
                </div>

                <div class="event_notification_area">
                    @foreach($task_notifications as $task_notification)
                        <div class="row event_notification w-100 mb-2"
                             id="event_notification_{{$task_notification['id']}}">
                            <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3 d-flex align-items-center">
                                <i class="fa fa-bell mr-2 pr-2"></i>
                                <select class="select-select2 form-select form-select-solid notification_medium"
                                        data-notification-length="{{$task_notification['id']}}" id="">
                                    <option
                                        value="email" {{$task_notification['notification_medium'] == 'email' ? 'selected' : ''}}>
                                        Email
                                    </option>
                                    <option
                                        value="notification" {{$task_notification['notification_medium'] == 'notification' ? 'selected' : ''}}>
                                        Notification
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3">
                                <input type="number" value="{{$task_notification['interval']}}"
                                       class="form-control form-control-solid notification_time"
                                       data-notification-length="{{$task_notification['id']}}">
                            </div>
                            <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3 d-flex align-items-center">
                                <i class="fa fa-null d-lg-none d-md-none d-sm-block mr-2 pr-2"></i>
                                <select class="select-select2 form-select form-select-solid notification_unit"
                                        data-notification-length="{{$task_notification['id']}}">
                                    <option
                                        value="minutes" {{$task_notification['unit'] == 'minutes' ? 'selected' : ''}}>
                                        minutes
                                    </option>
                                    <option value="hours" {{$task_notification['unit'] == 'hours' ? 'selected' : ''}}>
                                        hours
                                    </option>
                                    <option value="days" {{$task_notification['unit'] == 'days' ? 'selected' : ''}}>
                                        days
                                    </option>
                                    <option value="weeks" {{$task_notification['unit'] == 'weeks' ? 'selected' : ''}}>
                                        week
                                    </option>
                                </select>
                                <div
                                    class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3 ml-2 pl-2 fa fa-trash remove_notification_btn text-danger text-right"
                                    title="Remove"
                                    onclick="Generic_Container.removeTaskNotification($(this))"
                                    data-notification-length="{{$task_notification['id']}}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="btn btn-light btn-hover-rise add_new_notification_btn mt-3"
                     onclick="Generic_Container.addNewTaskNotification()"><i class="fa fa-plus mr-2"></i>Add New
                    Notification
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" value="{{$task['id']}}" name="task_id">
    <input type="hidden" value="{{json_encode($task_user)}}" name="task_user">
    <div class="card-footer flex-right p-0 py-2" id="m_panel_footer">
        <button onclick="Generic_Container.updateTask($(this))" type="button" id="kt_modal_add_event_submit"
                class="btn btn-primary rounded-0">
            <span class="indicator-label">Update Task</span>

        </button>
    </div>
</form>
<script type="application/javascript">
    Generic_Container.select2init()
    Generic_Container.dateTimeRangePickerInitWithValue('.task_start_end_date_time');
</script>
