<style>
    textarea {
        max-height: 100px !important;
        height: 100px !important;
        overflow-y: scroll !important;
    }
</style>
<!--begin::Form-->
<form class="form" id="kt_modal_add_event_form">
    <div class="row">
        <div class="col-md-12">
            <!--begin::Input group-->
            @php $sub_task_title = '' @endphp

            @if($parent_id)
                @php $sub_task_title = 'Sub' @endphp
                <div class="fv-row mb-3">
                    <p class="font-weight-bolder">Task Title : {{$parent_task->title_en ?: ''}}</p>
                    <p class="font-weight-bolder">Task
                        Datetime: {{ \Carbon\Carbon::create($parent_task['task_start_date_time'])->format('d M, Y h:i A') }}
                        -
                        {{ \Carbon\Carbon::create($parent_task['task_end_date_time'])->format('d M, Y h:i A') }}</p>
                    <input type="hidden" class="form-control form-control-solid"
                           name="parent_task_id" value="{{$parent_id}}"/>
                </div>
            @endif

            <div class="fv-row mb-3">
                <label for="task_title" class="font-weight-bolder">Task Title <span
                        class="text-danger">(*)</span></label>

                <input id="task_title" type="text" class="form-control form-control-solid"
                       placeholder="{{$sub_task_title}} Task Title"
                       name="task_title_en"/>
            </div>
            <div class="fv-row mb-3">
                <label for="task_description" class="font-weight-bolder">Task Description</label>
                <textarea data-autogrow="false" name="task_description" id="task_description" placeholder="Description"
                          class="form-control form-control-solid" cols="10" rows="3"></textarea>
            </div>

            <div class="row g-10">
                <div class="col">
                    <div class="mb-3">
                        <label for="task_start_end_date_time" class="font-weight-bolder">Select date & time range <span
                                class="text-danger">(*)</span></label>
                        <input type='text' class="form-control task_start_end_date_time" readonly
                               id="task_start_end_date_time" name="task_start_end_date_time"
                               placeholder="Select date & time range"/>
                    </div>
                </div>
            </div>

            <div class="g-10 mb-3">
                <label for="task_description" class="font-weight-bolder">Task Location</label>
                <input type="text" class="form-control form-control-solid"
                       placeholder="{{$sub_task_title}} Task Location" name="location"/>
            </div>

            <div class="g-10 mb-3">
                <!--begin::Checkbox-->
                <label class="form-check form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="" id="task_to_event"/>
                    <span class="form-check-label fw-bold" for="task_to_event">Add this {{$sub_task_title}} task to calendar</span>
                </label>
                <!--end::Checkbox-->
            </div>

            <div class="g-10 mb-3">
                <div>
                    <p class="font-weight-bold">Reminder Notification</p>
                </div>

                <div class="event_notification_area">
                    <div class="row event_notification w-100 mb-2" id="event_notification_1">
                        <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3 d-flex align-items-center">
                            <i class="fa fa-bell mr-2 pr-2"></i>
                            <select class="select-select2 form-select form-select-solid notification_medium"
                                    data-notification-length="1" id="">
                                <option value="">--Select--</option>
                                <option value="email">Email</option>
                                <option value="notification">Notification</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3">
                            <input type="number" value="30" class="form-control form-control-solid notification_time"
                                   data-notification-length="1">
                        </div>
                        <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3 d-flex align-items-center">
                            <i class="fa fa-null d-lg-none d-md-none d-sm-block mr-2 pr-2"></i>
                            <select class="select-select2 form-select form-select-solid notification_unit"
                                    data-notification-length="1">
                                <option value="minutes">minutes</option>
                                <option value="hours">hours</option>
                                <option value="days">days</option>
                                <option value="weeks">weeks</option>
                            </select>
                            <div
                                class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3 ml-2 pl-2 fa fa-trash remove_notification_btn text-danger text-right"
                                title="Remove"
                                onclick="Generic_Container.removeTaskNotification($(this))"
                                data-notification-length="1"></div>
                        </div>
                    </div>
                </div>
                <div class="btn btn-light btn-hover-rise add_new_notification_btn mt-3">
                    <i class="fa fa-plus mr-2"></i>Add New Notification
                </div>
            </div>
        </div>
        <input type="hidden" value='{{json_encode_escaped($organizer)}}' name="task_organizer_info"
               id="task_organizer_info">
    </div>
    <button onclick="Generic_Container.storeTask($(this))" type="button" id="kt_modal_add_event_submit"
            class="btn btn-primary rounded-0">
        <span class="indicator-label">Save Task</span>
    </button>
</form>
<script>
    $('.add_new_notification_btn').click(function () {
        Generic_Container.addNewTaskNotification();
    });

    Generic_Container.select2init()
    Generic_Container.dateTimeRangePickerInit('.task_start_end_date_time');
    $('#task_start_end_date_time').val('')

    $('#guest_search_field').on('keypress', function (e) {
        console.log(e)
        if (e.which == 13) {
            TaskContainer.showPreferredGuests($(this))
        }
    })

</script>
