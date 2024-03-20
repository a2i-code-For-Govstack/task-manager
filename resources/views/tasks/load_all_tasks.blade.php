<div class="row gy-5 g-xl-8">
    <div class="col-md-12">
        <h1 class="h1 p-2 pl-5 mb-3 border-bottom">Tasks</h1>
    </div>
</div>

<div style="background: white" class="ml-3 mr-5 table-search-header-wrapper mb-4 pt-3 pb-2 shadow-sm">
    <div class="row gy-5 g-xl-8">
        <div class="col-md-7 mt-2">
            <h3 class="p-0 pl-5 mb-3">Tasks</h3>
        </div>

        <div class="col-md-5">
            <div class="d-flex justify-content-md-end pr-3">
                <a id="add_task_btn" data-parent-id="0" data-parent-title="" onclick="Generic_Container.addTask($(this))"
                   class="btn btn-sm btn-light-info btn-square mr-1"
                   href="javascript:;">
                    <i class="fas fa-plus-circle mr-1"></i>
                    Add New Task
                </a>

                {{--                <a id="assign_task_btn" onclick="TaskContainer.assignTaskPanel($(this))" class="btn btn-sm btn-light-warning btn-square mr-1"--}}
                {{--                   href="javascript:;">--}}
                {{--                    <i class="fad fa-angle-double-right"></i>--}}
                {{--                    Assign--}}
                {{--                </a>--}}
            </div>
        </div>
    </div>
</div>

<div class="m-1 row">
    <div class="col-md-12">
        <div class="card card-custom card-stretch gutter-b">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-3 mb-1"><span class="text-capitalize"
                                                                       id="task_type_heading"></span> Tasks</span>
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="annual_entity_selection_area">
                    <ul class="nav nav-tabs custom-tabs mb-0" id="myTab" role="tablist">
                        <li class="nav-item task-table-nav-item">
                            <a href="javascript:;" class="nav-link active" data-toggle="tab"
                               aria-controls="tree" onclick="TaskContainer.loadFromNavLink($(this))"
                               data-tab-task-type="todo">
                                <span class="nav-text">Todo
                                  <span style="background: red;border-radius: 50%;padding: 1px 5px 0;color: white;">{{$task_counts['todo']}}</span>
                                </span>
                            </a>
                        </li>
                        <li class="nav-item task-table-nav-item">
                            <a href="javascript:;" class="nav-link rounded-0" data-toggle="tab"
                               onclick="TaskContainer.loadFromNavLink($(this))" data-tab-task-type="pending">
                                <span class="nav-text">Pending
                                    <span style="background: red;border-radius: 50%;padding: 1px 5px 0;color: white;">{{$task_counts['pending']}}</span>
                                </span>
                            </a>
                        </li>
                        <li class="nav-item task-table-nav-item">
                            <a href="javascript:;" class="nav-link rounded-0" data-toggle="tab"
                               onclick="TaskContainer.loadFromNavLink($(this))" data-tab-task-type="cancelled">
                                <span class="nav-text">Cancelled
                                    <span style="background: red;border-radius: 50%;padding: 1px 5px 0;color: white;">{{$task_counts['cancelled']}}</span>
                                </span>
                            </a>
                        </li>
                        <li class="nav-item task-table-nav-item">
                            <a href="javascript:;" class="nav-link rounded-0" data-toggle="tab"
                               onclick="TaskContainer.loadFromNavLink($(this))" data-tab-task-type="completed">
                                <span class="nav-text">Completed
                                    <span style="background: red;border-radius: 50%;padding: 1px 5px 0;color: white;">{{$task_counts['completed']}}</span>
                                </span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="rp_office_tab">
                        <div class="tab-pane fade border border-top-0 p-3 show active" id="tasks_list_table"
                             role="tabpanel">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.nav-item.task-table-nav-item').first().find('.nav-link').click()
        task_setup = '{{request()->has('task_setup') ? request()->task_setup : ''}}'
        //task_setup==create for new task
        //to edit task_setup==edit_{task_id}_page_{page_no}
        task_action = task_setup.split('_')
        task_id = isArray(task_action) ? task_action[1] : null;
        task_page = isArray(task_action) ? task_action[3] : null;

        if (task_setup == 'create') {
            $('#add_task_btn').click();
        }
        if (task_id && task_page) {
            url = '{{route('tasks.list')}}'
            data = {
                page: task_page
            };
            ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                if (response.status != 'error') {
                    $('#tasks_list_table').html(response)
                    $('#edit_task_' + task_id).click()
                }
            });
        }
    })

</script>
