@extends('layouts.master')
@section('styles')
@endsection
@section('sideMenu')
    @include('tasks.menu_tasks', ['task_setup' => $task_setup])
@endsection
@section('content')

@endsection
@section('scripts')
    <script>
        var TaskContainer = {
            assignTaskPanel: function (elem, scope = 'multiple') {
                parent_id = $(elem).attr('data-parent-id');
                tasks = [];

                if (scope === 'multiple') {
                    if ($('.check_task_checkbox:checked').length > 0) {
                        $('.check_task_checkbox:checked').each(function () {
                            tasks.push($(this).val())
                        })
                    }
                }
                if (scope === 'single') {
                    tasks.push($(elem).attr('data-task-id'))
                }

                if (tasks.length > 0) {
                    url = '{{route('tasks.user.assign.panel')}}';
                    data = {tasks};
                    ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                        if (response.status == 'error') {
                            toastr.error('Error!')
                        } else {
                            $('#m_panel_body').html(response)
                            if (parent_id != 0) {
                                $('.offcanvas-title').html('Assign Sub Task')
                            } else {
                                $('.offcanvas-title').html(title + 'Assign Task')
                            }
                            $('#m_panel_toggle').click(quickPanelToggler());
                            $('#kt_quick_panel').css('width', '60%');
                        }
                    })
                } else {
                    toastr.warning('Please choose task');
                }
            },

            assignTaskMultiple: function (elem) {
                KTApp.block('#kt_quick_panel');
                if ($('.added-user-area').length > 0) {
                    tasks = $('#assigning_tasks').val();
                    task_assignees = {};

                    $('.added-user-area').each(function (i, elem) {
                        user = JSON.parse($(elem).attr('data-added-user-info'))
                        user_type = $(elem).find('.user-permission-select').val()
                        user['user_type'] = 'assigned';
                        task_assignees[i] = user;
                    });
                    assigner_officer_id = $('#assigner_officer_id').val();
                    url = '{{route('tasks.user.assign')}}';
                    data = {tasks: tasks, task_assignees: JSON.stringify(task_assignees), assigner_officer_id}
                    ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                        KTApp.unblock('#kt_quick_panel');
                        if (response.status == 'error') {
                            toastr.error('Task assign unsuccessful');
                        } else {
                            toastr.success('Successfully Assigned')
                            $('#m_panel_toggle').click(quickPanelToggler());
                            $('.menu-item.menu-item-active a').click();
                        }
                    })
                } else {
                    toastr.warning('Please add assignees.');
                }
                KTApp.unblock('#kt_quick_panel');
            },

            loadFromNavLink: function (elem) {
                KTApp.block('#kt_content');
                url = '{{route('tasks.list')}}';
                task_status = $(elem).attr('data-tab-task-type');
                data = {
                    task_status,
                    page: 1,
                    per_page: 10,
                };
                ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                    KTApp.unblock('#kt_content');
                    if (response.status == 'error') {
                        toastr.error('Error!')
                        console.log(response.message);
                    } else {
                        if (task_status === 'completed_assignment_but_task_pending') {
                            $('#task_type_heading').html('Completed Assignment (Task Pending)')
                        } else {
                            $('#task_type_heading').html(data.task_status)
                        }
                        $('.task-table-nav-item').find('.active').removeClass('active');
                        $('#tasks_list_table').html(response)
                        $(elem).addClass('active');
                    }
                })
            },

            deleteTask: function (elem) {
                url = '{{route('tasks.delete')}}'
                data = {
                    task_id: $(elem).attr('data-task-id')
                };
                ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                    if (response.status != 'error') {
                        toastr.success(response.data);
                        $('.menu-item.menu-item-active a').click();
                        $('.task-table-nav-item').find('.active').click();
                    } else {
                        toastr.error('Trouble in cancelling task');
                        console.log(response)
                    }
                    Generic_Container.dismissTooltip()
                })
            },

            showPreferredGuests: function (elem, type = 'addUser') {
                KTApp.block('#kt_quick_panel')
                url = '{{route('tasks.search-assignee')}}'

                data = {
                    office_id: elem.office_id,
                    unit_id: elem.unit_id,
                    search_key: elem.search_value,
                    type: type
                };

                ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                    KTApp.unblock('#kt_quick_panel')
                    if (type == 'view_other_task') {
                        $('.searched_users_area_for_others_calendar').html(response)
                        $('#kt_aside_menu .menu-ajax .menu-link').find('.active').click()
                    } else {
                        $('.searched_users_area').html(response)
                    }

                });
            },

            addUserInInviteList: function (elem) {
                inviting_user = $(elem).attr('data-user-info');
                inviting_user = JSON.parse(inviting_user)
                user_json = JSON.stringify(inviting_user).replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/'/g, '&#039;')
                    .replace(/"/g, '&quot;')
                    .replace(/\n/g, '<br />');
                if ($(`[data-added-user-email-area="${inviting_user.user_email}"]`).length < 1) {
                    content = `<div class="cursor-pointer d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed added-user-area" data-added-user-email-area='${inviting_user.user_email}' data-added-user-info='${user_json}'>
                                        <div style="width: 80%" class="d-flex align-items-center">
                                            <div class="symbol symbol-25px symbol-circle">
                                                <img alt="Pic" src="{{asset('assets/media/svg/avatars/blank.svg')}}"/>
                                            </div>
                                            <div class="ml-5">
                                                <a href="javascript:;" class="fs-5 fw-bolder text-secondary h5 text-hover-dark mb-2">${inviting_user.user_name_bn}</a>
                                                <div class="fw-bold text-muted">${inviting_user.user_email}</div>
                                                <div class="fw-bold text-muted">${inviting_user.user_designation_name_bn}</div>
                                            </div>
                                        </div>
                                        <div style="width:20%">
                                            <div class="ml-2 pl-2 fa fa-times" onclick="TaskContainer.removeAddedUser($(this))" data-added-user-email='${inviting_user.user_email}'></div>
                                        </div>
                                        <input type="hidden" value="assigned" class="user-permission-select">
                                    </div>`;
                    $('.added_users').append(content)

                    Generic_Container.select2init()
                }
            },

            addUserInInviteListWithDateRange: function (elem) {
                inviting_user = $(elem).attr('data-user-info');
                inviting_user = JSON.parse(inviting_user)
                user_json = JSON.stringify(inviting_user).replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/'/g, '&#039;')
                    .replace(/"/g, '&quot;')
                    .replace(/\n/g, '<br />');
                if ($(`[data-added-user-email-area="${inviting_user.user_email}"]`).length < 1) {
                    content = `<div class="py-4 border-bottom border-gray-300 border-bottom-dashed added-user-area" data-added-user-email-area='${inviting_user.user_email}' data-added-user-info='${user_json}'>
                                        <div class="d-flex align-items-center scroll scroll-x" style="width: 100%;">
                                            <div class="symbol symbol-25px symbol-circle">
                                                <img alt="Pic" src="{{asset('assets/media/svg/avatars/blank.svg')}}"/>
                                            </div>
                                             <div class="ms-2" style="width:5%">
                                                <div class="ml-2 pl-2 fa fa-times" onclick="TaskContainer.removeAddedUser($(this))" data-added-user-email='${inviting_user.user_email}'></div>
                                            </div>
                                            <div class="ml-5">
                                                <a href="javascript:;" class="fs-5 fw-bolder text-secondary h5 text-hover-dark mb-2">${inviting_user.user_name_bn}</a>
                                                <div class="fw-bold text-muted">${inviting_user.user_email}</div>
                                                <div class="fw-bold text-muted">${inviting_user.user_designation_name_bn}</div>
                                            </div>
                                        </div>
                                        <input type='text' class="form-control assigned_user_date_time" readonly name="assigned_user_date_time" placeholder="Select date & time range"/>
                                        <input type="hidden" value="assigned" class="user-permission-select">
                                    </div>`;
                    $('.added_users').append(content)
                    Generic_Container.dateTimeRangePickerInit('.assigned_user_date_time')
                    Generic_Container.select2init()
                }
            },

            addNewNotification: function () {
                event_notification_length = $('.event_notification').length
                event_notification_length = !isNaN(event_notification_length) ? parseInt(event_notification_length) : 0;
                event_notification_length += 1;
                content = `<div class="row event_notification w-100 mb-2" id="event_notification_${event_notification_length}">
                        <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3 d-flex align-items-center">
                            <i class="fa fa-bell mr-2 pr-2"></i>
                            <select class="select-select2 form-select form-select-solid notification_medium" data-notification-length="${event_notification_length}" id="">
                                <option value="email">Email</option>
                                <option value="notification">Notification</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3 d-flex align-items-center">
                            <input type="number" value="30" class="form-control form-control-solid notification_time" data-notification-length="${event_notification_length}">
                        </div>
                        <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3 d-flex align-items-center">
                            <select class="select-select2 form-select form-select-solid notification_unit" data-notification-length="${event_notification_length}">
                                <option value="minutes">minutes</option>
                                <option value="hours">hours</option>
                                <option value="days">days</option>
                                <option value="weeks">weeks</option>
                            </select>
                            <div class="col-sm-6 col-md-3 col-lg-3 col-xs-6 col-xl-3 ml-2 pl-2 fa fa-trash remove_notification_btn text-danger text-right" title="Remove" onclick="Generic_Container.removeTaskNotification($(this))" data-notification-length="${event_notification_length}"></div>
                        </div>
                    </div>`;
                $('.event_notification_area').append(content)

                Generic_Container.select2init()
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

            paginate: function (elem) {
                next_page = $(elem).attr('data-paginate-page');
                url = $(elem).attr('data-paginate-url');
                area = $(elem).attr('data-area');
                data = {
                    page: next_page,
                    task_status: $('.task-table-nav-item').find('.active').attr('data-tab-task-type')
                };
                ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                    $('#' + area).html(response)
                })
            },
            showTaskUsers: function (elem) {
                url = '{{route('tasks.users')}}';
                task_id = $(elem).attr('data-task-id');
                data = {task_id};
                ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                    if (response.status != 'error') {
                        $('#m_panel_body').html(response)
                        $('.offcanvas-title').html('Task Users')
                        $('#m_panel_toggle').click(quickPanelToggler());
                    } else {
                        toastr.error('Trouble in showing task users.');
                        console.log(response)
                    }
                })
            },
            showTaskInfo: function (elem) {
                url = '{{route('tasks.show')}}';
                task_id = $(elem).attr('data-task-id');
                data = {task_id};
                ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                    if (response.status != 'error') {
                        $('#m_panel_body').html(response)
                        $('.offcanvas-title').html('Show Task')
                        $('#m_panel_toggle').click(quickPanelToggler('40%'));
                    } else {
                        toastr.error('Trouble in showing task users.');
                        console.log(response)
                    }
                })
            },
        }

    </script>
@endsection
