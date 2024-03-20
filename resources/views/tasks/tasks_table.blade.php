@if (Arr::has($tasks, 'data') && count($tasks['data']) > 0)
    @foreach ($tasks['data'] as $task)
        <div class="mb-6 pb-2 pt-2 pl-2" id="task_item_area_{{ $task['id'] }}"
             style="border-bottom: 1px solid #3699ff75;border-left: 1px solid #3699ff75; border-radius: 5px ">
            <div class="row">
                <div class="col-md-8">
                    <div class="d-flex flex-column align-items-cente py-2 w-75">
                        <span>
                            <a href="javascript:;"
                               class="text-dark-75 font-weight-bold text-hover-primary font-size-lg mb-1">
                                {{ $task['title_en'] }}
                                <span
                                    class="{{ $task['task_user'] && $task['task_user']['has_event'] == 1 ? 'ml-2 text-danger fa fa-calendar' : '' }}"></span>
                            </a>
                            <span
                                class="label {{$task['task_user'] &&  $task['task_user']['user_type'] == 'organizer' ? 'label-light-primary' : 'label-light-info' }} label-inline font-weight-bold text-capitalize">
                                {{ $task['task_user'] && $task['task_user']['user_type'] == 'organizer' ? 'Self' : $task['task_organizer']['user_name_bn'] }}
                            </span>
                        </span>
                        <span class="text-muted font-weight-bold">
                            {{ \Carbon\Carbon::create($task['task_start_date_time'])->format('d M, Y h:i A') }} -
                            {{ \Carbon\Carbon::create($task['task_end_date_time'])->format('d M, Y h:i A') }}
                        </span>
                        <!--end::Data-->
                    </div>
                    @if ($task['meta_data'])
                        @php
                            $meta_data = json_decode(base64_decode($task['meta_data']), true);
                        @endphp
                    @else
                        @php
                            $meta_data = json_decode(base64_decode($task['meta_data']), true);
                        @endphp
                    @endif
                    @if (isset($meta_data) && is_array($meta_data) && Arr::has($meta_data, 'type') && $task['task_user'] && $task['task_user']['user_type'] == 'organizer')
                        <a href="{{ $meta_data['return_url'] }}" target="_blank"
                           class="btn btn-sm btn-light-info btn-square mr-1">
                            <i class="far fa-external-link"></i>{{ $meta_data['type'] }}
                        </a>
                    @endif
                </div>
                <div class="col-md-4 text-right">
                    @if ($task['task_status'] != 'completed' && $task['task_user'] &&  $task['task_user']['task_user_status'] != 'completed' && $task['task_status'] != 'cancelled')
                        @if ($task['task_user'] && $task['task_user']['user_type'] == 'organizer')
                            <a id="add_task_btn" data-parent-id="{{ $task['id'] }}"
                               data-parent-title="{{$task['title_en']}}" onclick="Generic_Container.addTask($(this))"
                               class="btn btn-sm btn-light-info btn-square mr-1"
                               href="javascript:;">
                                <i class="fas fa-plus-circle mr-1"></i>
                                Sub Task
                            </a>
                        @endif

                        <a href="javascript:;" title="Mark Completed"
                           class="mr-1 btn btn-icon btn-square btn-sm btn-light btn-hover-icon-danger btn-icon-primary"
                           onclick="Generic_Container.updateTaskStatus($(this))"
                           data-task-id="{{ $task['id'] }}" data-parent-id="{{ $task['parent_task_id'] }}">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fa fa-check"></i>
                                    </span>
                        </a>

                        @if ($task['task_user'] && $task['task_user']['user_type'] == 'organizer')
                            <a href="javascript:;"
                               class="mr-1 btn btn-icon btn-square btn-sm btn-light btn-hover-icon-danger btn-icon-primary"
                               title="Cancel Task" onclick="TaskContainer.deleteTask($(this))"
                               data-task-id="{{ $task['id'] }}">
                                        <span class="svg-icon svg-icon-2">
                                            <i class="fa fa-trash"></i>
                                        </span>
                            </a>
                            {{--                            @if($task['parent_task_id'] == 0)--}}
                            {{--                                <a href="javascript:;"--}}
                            {{--                                   class="mr-1 btn btn-icon btn-square btn-sm btn-light btn-hover-icon-danger btn-icon-primary"--}}
                            {{--                                   title="Add Sub Task" onclick="Generic_Container.addSubtask($(this))"--}}
                            {{--                                   data-task-id="{{ $task['id'] }}">--}}
                            {{--                                        <span class="svg-icon svg-icon-2">--}}
                            {{--                                            <i class="fa fa-plus-circle"></i>--}}
                            {{--                                        </span>--}}
                            {{--                                </a>--}}
                            {{--                            @endif--}}
                        @endif

                        <a href="javascript:;"
                           class="mr-1 btn btn-icon btn-square btn-sm btn-light btn-hover-icon-danger btn-icon-primary"
                           id="edit_task_{{ $task['id'] }}" title="Edit Task"
                           onclick="Generic_Container.editTask($(this))" data-task-id="{{ $task['id'] }}"
                           data-parent-id="0">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fa fa-edit"></i>
                                    </span>
                        </a>
                        <a href="javascript:;"
                           class="mr-1 btn btn-square btn-sm btn-light btn-hover-icon-danger btn-icon-primary pl-0 pr-0"
                           id="assign_single_task_{{ $task['id'] }}" title="Assign Task"
                           onclick="TaskContainer.assignTaskPanel($(this), 'single')"
                           data-task-id="{{ $task['id'] }}">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fa fa-user-plus pl-0"></i>
                                        <sup
                                            style="border-radius: 50%;border: 1px solid red;padding: 0 3px 0 3px;color: white;background: red;">{{$task['task_users_count'] - 1}}</sup>
                                    </span>
                        </a>
                    @endif
                    @if ($task['task_user'] && $task['task_user']['user_type'] != 'organizer')
                        <a href="javascript:;" title="Task Users"
                           class="mr-1 btn btn-square btn-sm btn-light btn-hover-icon-danger btn-icon-primarym pl-0 pr-0"
                           onclick="TaskContainer.showTaskUsers($(this))" data-task-id="{{ $task['id'] }}">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fad fa-user-alt pl-0"></i>
                                        <sup
                                            style="border-radius: 50%;border: 1px solid red;padding: 0 3px 0 3px;color: white;background: red;">{{$task['task_users_count'] - 1}}</sup>

                                </span>
                        </a>
                    @endif
                    <a href="javascript:;" title="Show Task"
                       class="mr-1 btn btn-icon btn-square btn-sm btn-light btn-hover-icon-danger btn-icon-primary"
                       onclick="TaskContainer.showTaskInfo($(this))"
                       data-task-id="{{ $task['id'] }}" data-parent-id="{{ $task['parent_task_id'] }}">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fa fa-eye"></i>
                                    </span>
                    </a>
                    <a href="javascript:;"
                       class="mr-1 btn btn-icon btn-square btn-sm btn-light btn-hover-icon-danger btn-icon-primary"
                       onclick="Generic_Container.taskCommentPanel($(this))" title="Comments"
                       data-task-id="{{ $task['id'] }}">
                                <span class="svg-icon svg-icon-3">
                                    <i class="fa fa-comment"></i>
                                </span>
                    </a>

                </div>

            </div>
        </div>
        @if($task['sub_tasks'])
            @foreach($task['sub_tasks'] as $sub_task)
                <div class="ml-5 mb-6 pb-2 pt-2 pl-2" id="task_item_area_{{ $sub_task['id'] }}"
                     style="border-bottom: 1px solid #3699ff75;border-left: 1px solid #3699ff75; border-radius: 5px ">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex flex-column align-items-cente py-2 w-75">
                                <span>
                                    <a href="javascript:;"
                                       class="text-dark-75 font-weight-bold text-hover-primary font-size-lg mb-1">
                                        {{ $sub_task['title_en'] }}
                                        <span
                                            class="{{ isset($sub_task['task_user']) && $sub_task['task_user']['has_event'] == 1 ? 'ml-2 text-danger fa fa-calendar' : '' }}"></span>
                                    </a>
{{--                                    <span--}}
                                    {{--                                        class="label {{ (isset($sub_task['task_user']) && $sub_task['task_user']['user_type'] == 'organizer') ? 'label-light-primary' : 'label-light-info' }} label-inline font-weight-bold text-capitalize">--}}
                                    {{--                                        {{ (isset($sub_task['task_user']) && ($sub_task['task_user']['user_type'] == 'organizer')) ? 'Self' : (isset($sub_task['task_organizer']['user_name_bn']) ?? '' ) }}--}}
                                    {{--                                    </span>--}}
                                </span>
                                <span class="text-muted font-weight-bold">
                                    {{ \Carbon\Carbon::create($sub_task['task_start_date_time'])->format('d M, Y h:i A') }} -
                                    {{ \Carbon\Carbon::create($sub_task['task_end_date_time'])->format('d M, Y h:i A') }}
                                </span>
                                <!--end::Data-->
                            </div>
                            @if ($sub_task['meta_data'])
                                @php
                                    $meta_data = json_decode(base64_decode($sub_task['meta_data']), true);
                                @endphp
                            @else
                                @php
                                    $meta_data = json_decode(base64_decode($sub_task['meta_data']), true);
                                @endphp
                            @endif
                            @if (isset($meta_data) && is_array($meta_data) && Arr::has($meta_data, 'type') && $sub_task['task_user']['user_type'] == 'organizer')
                                <a href="{{ $meta_data['return_url'] }}" target="_blank"
                                   class="btn btn-sm btn-light-info btn-square mr-1">
                                    <i class="far fa-external-link"></i>{{ $meta_data['type'] }}
                                </a>
                            @endif
                        </div>
                        <div class="col-md-4 text-right">
                            @if ($sub_task['task_status'] != 'completed' && $sub_task['task_user']['task_user_status'] != 'completed' && $sub_task['task_status'] != 'cancelled')
                                <a href="javascript:;" title="Mark Completed"
                                   class="mr-1 btn btn-icon btn-square btn-sm btn-light btn-hover-icon-danger btn-icon-primary"
                                   onclick="Generic_Container.updateTaskStatus($(this))"
                                   data-task-id="{{ $sub_task['id'] }}">
                                                                    <span class="svg-icon svg-icon-2">
                                                                        <i class="fa fa-check"></i>
                                                                    </span>
                                </a>

                                @if ($sub_task['task_organizer'] && $sub_task['task_organizer']['user_officer_id'] == $userDetails['employee_record_id'])
                                    <a href="javascript:;"
                                       class="mr-1 btn btn-icon btn-square btn-sm btn-light btn-hover-icon-danger btn-icon-primary"
                                       title="Cancel Sub Task" onclick="TaskContainer.deleteTask($(this))"
                                       data-task-id="{{ $sub_task['id'] }}">
                                                                        <span class="svg-icon svg-icon-2">
                                                                            <i class="fa fa-trash"></i>
                                                                        </span>
                                    </a>
                                    @if($sub_task['parent_task_id'] == 0)
                                        <a href="javascript:;"
                                           class="mr-1 btn btn-icon btn-square btn-sm btn-light btn-hover-icon-danger btn-icon-primary"
                                           title="Add Sub Task" onclick="Generic_Container.addSubtask($(this))"
                                           data-task-id="{{ $sub_task['id'] }}">
                                                                        <span class="svg-icon svg-icon-2">
                                                                            <i class="fa fa-plus-circle"></i>
                                                                        </span>
                                        </a>
                                    @endif
                                @endif

                                <a href="javascript:;"
                                   class="mr-1 btn btn-icon btn-square btn-sm btn-light btn-hover-icon-danger btn-icon-primary"
                                   id="edit_task_{{ $sub_task['id'] }}" title="Edit Sub Task"
                                   onclick="Generic_Container.editTask($(this))" data-task-id="{{ $sub_task['id'] }}"
                                   data-parent-id="{{ $sub_task['parent_task_id'] }}">
                                                                    <span class="svg-icon svg-icon-2">
                                                                        <i class="fa fa-edit"></i>
                                                                    </span>
                                </a>
                                <a href="javascript:;"
                                   class="mr-1 btn btn-square btn-sm btn-light btn-hover-icon-danger btn-icon-primary pl-0 pr-0"
                                   id="assign_single_task_{{ $sub_task['id'] }}"
                                   data-parent-id="{{ $sub_task['parent_task_id'] }}" title="Assign Sub Task"
                                   onclick="TaskContainer.assignTaskPanel($(this), 'single')"
                                   data-task-id="{{ $sub_task['id'] }}">
                                                                    <span class="svg-icon svg-icon-2">
                                                                        <i class="fa fa-user-plus pl-0"></i>
                                                                        <sup
                                                                            style="border-radius: 50%;border: 1px solid red;padding: 0 3px 0 3px;color: white;background: red;">{{$sub_task['task_users_count'] - 1}}</sup>
                                                                    </span>
                                </a>
                            @endif
                            @if ($sub_task['task_user'] && $sub_task['task_user']['user_type'] != 'organizer')
                                <a href="javascript:;" title="Sub Task Users"
                                   class="mr-1 btn btn-square btn-sm btn-light btn-hover-icon-danger btn-icon-primarym pl-0 pr-0"
                                   onclick="TaskContainer.showTaskUsers($(this))" data-task-id="{{ $sub_task['id'] }}"
                                   data-parent-id="{{ $sub_task['parent_task_id'] }}">
                                                                <span class="svg-icon svg-icon-2">
                                                                    <i class="fad fa-user-alt pl-0"></i>
                                                                        <sup
                                                                            style="border-radius: 50%;border: 1px solid red;padding: 0 3px 0 3px;color: white;background: red;">
{{--                                                                            {{$sub_task['task_users_count'] - 1}}--}}
                                                                        </sup>

                                                                </span>
                                </a>
                            @endif
                            <a href="javascript:;" title="Show Sub Task"
                               class="mr-1 btn btn-icon btn-square btn-sm btn-light btn-hover-icon-danger btn-icon-primary"
                               onclick="TaskContainer.showTaskInfo($(this))"
                               data-task-id="{{ $sub_task['id'] }}" data-parent-id="{{ $sub_task['parent_task_id'] }}">
                                    <span class="svg-icon svg-icon-2">
                                        <i class="fa fa-eye"></i>
                                    </span>
                            </a>
                            <a href="javascript:;"
                               class="mr-1 btn btn-icon btn-square btn-sm btn-light btn-hover-icon-danger btn-icon-primary"
                               onclick="Generic_Container.taskCommentPanel($(this))" title="Comments"
                               data-task-id="{{ $sub_task['id'] }}" data-parent-id="{{ $sub_task['parent_task_id'] }}">
                                                                <span class="svg-icon svg-icon-3">
                                                                    <i class="fa fa-comment"></i>
                                                                </span>
                            </a>

                        </div>

                    </div>
                </div>
            @endforeach
        @endif
    @endforeach

    @if ($tasks['total'] >= $tasks['to'])
        <table style="width: 100%">
            <thead>
            <tr>
                <td style="width: 90%; text-align: right">
                    {{ $tasks['from'] }} - {{ $tasks['to'] }} of {{ $tasks['total'] }}
                </td>
                <td style="width: 10%; text-align: right">
                    <button onclick="TaskContainer.paginate($(this))" data-area="tasks_list_table"
                            title="Previous"
                            data-search-key="" data-paginate-page="{{ $tasks['current_page'] - 1 }}"
                            data-paginate-url="{{ route('tasks.list') }}" class="pagination__button"
                        {{ $tasks['from'] <= 1 ? 'disabled' : '' }}><i class="fa fa-chevron-left"></i></button>
                    <button onclick="TaskContainer.paginate($(this))" data-area="tasks_list_table"
                            title="Next"
                            data-search-key="" data-paginate-page="{{ $tasks['current_page'] + 1 }}"
                            data-paginate-url="{{ route('tasks.list') }}" class="pagination__button"
                        {{ $tasks['to'] >= $tasks['total'] ? 'disabled' : '' }}>
                        <i class="fa fa-chevron-right"></i>
                    </button>
                </td>
            </tr>
            </thead>
        </table>
    @endif
@else
    <div class="text-center">No Task Found.</div>
@endif

<script>
    $('[data-toggle="tooltip"]').tooltip({
        trigger: 'hover'
    })

    $('.callPopover').click(function (e) {
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
</script>
