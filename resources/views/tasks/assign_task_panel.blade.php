
<div class="row">
    <div class="col-md-6">
        @foreach ($tasks as $task)
            <p><span><i class="fas fa-tasks"></i></span> <span
                    class="font-weight-bold">{{ $task->title_en }}</span></p>
            @if ($task->description)
                <p><span><i class="fas fa-prescription-bottle"></i></span> {{ $task->description }}</p>
            @endif

            @if ($task->location)
                <p><span><i class="fas fa-map-marker-alt"></i></span> {{ $task->location }}</p>
            @endif
            @php
                $task_start_date_time = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $task['task_start_date_time'])->format('d/m/Y h:i A');
                $task_end_date_time = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $task['task_end_date_time'])->format('d/m/Y h:i A');
                $task_start_end_date_time = $task_start_date_time . ' - ' . $task_end_date_time;
            @endphp
            <p><span><i class="fas fa-clock"></i> </span> {{ $task_start_end_date_time }}</p>
            <hr>
        @endforeach
        <div id="task_assigned_users_list_in_assignee_panel"></div>
        <div class="max-h-300px added_users" style="overflow-y: auto;border-top: 1px solid #3699ff;border-radius: 10px;"></div>
            <div class="card-footer py-2 flex-right text-right" id="m_panel_footer">
                <button onclick="TaskContainer.assignTaskMultiple($('#assign_task_panel_form'))" type="button" id="kt_modal_assign_submit"
                        class="btn btn-primary rounded-0">
                    <span class="indicator-label">Save</span>
                </button>
            </div>
    </div>

    <div class="col-md-6">
        <form class="form" action="" id="assign_task_panel_form">
            <div class="modal-body pt-1 pb-1">
                <div class="row">
                    <div class="col-md-12">
                        <div class="search_box position-relative">
                            <x-office-select grid="12" unit="true"/>
                            <div class="input-group">
                                <input autocomplete="off" type="search" id="search_key"
                                       class="form-control form-control-lg"
                                       name="search_key" value="" placeholder="username/name/email">

                                <button  type="button"
                                         id="guest_search_field" class="btn btn-primary rounded-0">
                                    <span class="indicator-label">Search</span>
                                </button>
                                <button type="button" class="btn btn-danger" onclick="resetGuestSearchedArea()">Reset</button>
                            </div>
                        </div>
                        <div class="searched_users_area p-2 mb-2" onmouseenter="customScrollInit('searched_users_area', 'class')" style="overflow-y: auto;max-height:400px !important;"></div>

{{--                        <div class="max-h-300px added_users"--}}
{{--                             style="overflow-y: auto;border-top: 1px solid #3699ff;border-radius: 10px;"></div>--}}
                        <input type="hidden" id="assigner_officer_id" value="{{ $user_officer_id }}">
                        <input type="hidden" id="assigning_tasks" value="{{ json_encode($task_ids) }}">
                    </div>
                </div>
            </div>
{{--            <div class="card-footer py-2 flex-right text-right" id="m_panel_footer">--}}
{{--                <button onclick="TaskContainer.assignTaskMultiple($(this))" type="button" id="kt_modal_assign_submit"--}}
{{--                        class="btn btn-primary rounded-0">--}}
{{--                    <span class="indicator-label">Submit</span>--}}
{{--                </button>--}}
{{--            </div>--}}
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        showTaskUsers('{{$task_ids[0]}}')
    })

    function showTaskUsers(task_id) {
        url = '{{route('tasks.users')}}';
        data = {task_id};
        ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
            if (response.status != 'error') {
                $('#task_assigned_users_list_in_assignee_panel').html(response)
            } else {
                toastr.error('Trouble in showing task users.');
                console.log(response)
            }
        })
    }

    $('#assign_task_panel_form').submit(function (e) {
        e.preventDefault()
    })

    $('#guest_search_field').on('click', function () {
        serach = {};
        serach = {
            office_id: $('#office_id').val(),
            unit_id: $('#office_unit_id').val(),
            search_value: $('#search_key').val(),
        }
        TaskContainer.showPreferredGuests(serach)
    });
</script>
