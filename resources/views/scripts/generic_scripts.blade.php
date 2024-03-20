<script>
    var Generic_Container = {
        storeTask: function (elem) {
            data = $('#kt_modal_add_event_form').serializeArray();
            task_organizer = $('#task_organizer_info').val();
            notifications = {};
            $('.event_notification').each(function (i, elem) {
                medium = $(elem).find('.notification_medium').val()
                if (medium) {
                    interval = $(elem).find('.notification_time').val()
                    unit = $(elem).find('.notification_unit').val()
                    notification = {medium, interval, unit, is_dispatched: 0}
                    notifications[i] = notification
                }
            })

            task_to_event = $('#task_to_event').is(":checked") ? 1 : 0;

            data.push({name: 'task_organizer', value: task_organizer});
            data.push({name: 'notifications', value: JSON.stringify(notifications)});
            data.push({name: 'task_to_event', value: JSON.stringify(task_to_event)});

            url = '{{route('tasks.store')}}';
            ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                if (response.status === 'success') {
                    toastr.success('Task Create Successfully');
                    $('#m_panel_toggle').click(quickPanelToggler());
                    $('.menu-item.menu-item-active a').click()
                } else {
                    toastr.error(response.data)
                    console.log(response);
                }
            })
        },

        editTask: function (elem) {
            url = '{{route('tasks.edit')}}';
            edit_type = $(elem).attr('data-edit-type');
            parent_id = $(elem).attr('data-parent-id');
            task_id = $(elem).attr('data-task-id');
            data = {task_id, parent_id, edit_type};
            ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                if (response.status != 'error') {
                    $('#m_panel_body').html(response)

                    title = parent_id != 0 ? ' Sub ' : ' ';

                    if (edit_type && edit_type == 'assign') {
                        $('.offcanvas-title').html('Assign ' + title + ' Task')
                    } else {
                        $('.offcanvas-title').html('Edit ' + title + ' Task')
                    }
                    $('#m_panel_toggle').click(quickPanelToggler());
                } else {
                    toastr.error('Trouble in editing task');
                    console.log(response)
                }
            })
        },

        updateTask: function (elem) {
            url = '{{route('tasks.update')}}';
            data = $('#kt_modal_update_task_form').serializeArray();
            task_organizer = {};
            task_assignee = {};
            notifications = {};
            $('.added-user-area').each(function (i, elem) {
                user = JSON.parse($(elem).attr('data-added-user-info'))
                user_type = $(elem).find('.user-permission-select').val()
                user['user_type'] = user_type;
                if (user_type == 'organizer') {
                    task_organizer = user;
                } else {
                    task_assignee[i] = user;
                }
            });

            $('.event_notification').each(function (i, elem) {
                medium = $(elem).find('.notification_medium').val()
                interval = $(elem).find('.notification_time').val()
                unit = $(elem).find('.notification_unit').val()
                notification = {medium, interval, unit, is_dispatched: 0}
                notifications[i] = notification
            })

            task_to_event = $('#task_to_event').is(":checked") ? 1 : 0;

            data.push({name: 'task_organizer', value: JSON.stringify(task_organizer)});
            data.push({name: 'task_assignee', value: JSON.stringify(task_assignee)});
            data.push({name: 'notifications', value: JSON.stringify(notifications)});
            data.push({name: 'task_to_event', value: JSON.stringify(task_to_event)});

            ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                if (response.status != 'error') {
                    $('#m_panel_toggle').click(quickPanelToggler());
                    $('.menu-item.menu-item-active a').click();
                } else {
                    toastr.error('Trouble in editing task');
                    console.log(response)
                }
            })
        },

        updateTaskStatus: function (elem, status = 'completed') {
            url = '{{route('tasks.update.status')}}';
            data = {
                'task_status': status,
                'task_id': $(elem).attr('data-task-id'),
            };
            ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                if (response.status == 'success') {
                    toastr.success('Successfully Done!');
                    // $('#task_item_area_' + $(elem).attr('data-task-id')).remove();
                    $('.task_item_div_' + $(elem).attr('data-task-id')).remove();
                    $('.menu-item.menu-item-active a').click();
                } else {
                    toastr.error('Task Completion Error!');
                    console.log(response)
                }
                Generic_Container.dismissTooltip();
            })
        },

        addTask: function (elem) {
            url = '{{route('tasks.create')}}';
            parent_id = elem.data('parent-id');
            parent_title = elem.data('parent-title');
            data = {parent_id, parent_title};
            ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                if (response.status != 'error') {
                    $('#m_panel_body').html(response)
                    $('#kt_calendar_datepicker_start_date').val(new Date().toISOString().split('T')[0]);
                    $('#kt_calendar_datepicker_end_date').val(new Date().toISOString().split('T')[0]);
                    title = parent_id ? ' Sub ' : '';
                    $('.offcanvas-title').html('Create ' + title + ' Task');
                    $('#m_panel_toggle').click(quickPanelToggler());
                } else {
                    toastr.error('Trouble in creating task');
                    console.log(response)
                }
            })
        },

        addSubtask: function (elem) {
            parent_task_id = $(elem).data('task-id')
            url = '{{route('tasks.create')}}';
            data = {};
            ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                if (response.status != 'error') {
                    $('#m_panel_body').html(response)
                    $('#kt_calendar_datepicker_start_date').val(new Date().toISOString().split('T')[0])
                    $('#kt_calendar_datepicker_end_date').val(new Date().toISOString().split('T')[0])
                    $('.offcanvas-title').text('Create Task')
                    $('#m_panel_toggle').click(quickPanelToggler());
                    $('#parent_task_id').val(parent_task_id)
                } else {
                    toastr.error('Trouble in creating task');
                    console.log(response)
                }
            })
        },

        addNewTaskNotification: function () {
            event_notification_length = $('.event_notification').length
            event_notification_length = !isNaN(event_notification_length) ? parseInt(event_notification_length) : 0;
            event_notification_length += 1;
            content = `<div class="row event_notification w-100 mb-2" id="event_notification_${event_notification_length}">
                        <div class="col d-flex align-items-center">
                            <i class="fa fa-bell mr-2 pr-2"></i>
                            <select class="select-select2 form-select form-select-solid notification_medium" data-notification-length="${event_notification_length}" id="">
                                <option value="">--Select--</option>
                                <option value="email">Email</option>
                                <option value="notification">Notification</option>
                            </select>
                        </div>
                        <div class="col">
                            <input type="number" value="30" class="form-control form-control-solid notification_time" data-notification-length="${event_notification_length}">
                        </div>
                        <div class="col d-flex align-items-center">
                            <select class="select-select2 form-select form-select-solid notification_unit" data-notification-length="${event_notification_length}">
                                <option value="minutes">minutes</option>
                                <option value="hours">hours</option>
                                <option value="days">days</option>
                                <option value="weeks">weeks</option>
                            </select>
                            <div class="ml-2 pl-2 fa fa-trash remove_notification_btn text-danger" title="Remove" onclick="Generic_Container.removeTaskNotification($(this))" data-notification-length="${event_notification_length}"></div>
                        </div>
                    </div>`;
            $('.event_notification_area').append(content)

            Generic_Container.select2init()
        },

        removeTaskNotification: function (elem) {
            $('#event_notification_' + elem.attr('data-notification-length')).remove()
        },

        select2init: function (obj = {}) {
            $('.select-select2').select2({
                minimumResultsForSearch: -1,
            });
        },

        dismissTooltip: function () {
            $('.tooltip.fade.show').remove()
        },

        dismissPopover: function () {
            $('.popover').popover('hide');
        },

        saveTaskComment: function (elem) {
            url = '{{route('tasks.update.comment')}}'
            data = {
                task_id: $('#popover_task_id').val(),
                task_comment: $('.popover-body #popover_task_comment').val(),
            }
            ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                if (response.status == 'error') {
                    toastr.error('Error in adding comment.')
                } else {
                    toastr.success('Successfully Added Comment')
                }
                Generic_Container.dismissPopover()
                $('.menu-item.menu-item-active a').click()
            });
        },

        dateTimeRangePickerInitOld: function (elem) {
            $(elem).daterangepicker({
                buttonClasses: 'btn',
                applyClass: 'btn-primary',
                cancelClass: 'btn-secondary',
                timePicker: true,
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'DD/MM/YYYY hh:mm A'
                }
            });
        },

        dateTimeRangePickerInit: function (elem) {
            $(elem).daterangepicker({
                autoUpdateInput: false,
                drops: 'auto',
                buttonClasses: 'btn',
                applyClass: 'btn-primary',
                cancelClass: 'btn-secondary',
                timePicker: true,
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'DD/MM/YYYY hh:mm A'
                }
            });

            $(elem).on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY hh:mm A') + ' - ' + picker.endDate.format('DD/MM/YYYY hh:mm A'));
            });

            $(elem).on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });
        },

        dateTimeRangePickerInitWithValue: function (elem) {
            $(elem).daterangepicker({
                autoUpdateInput: false,
                buttonClasses: 'btn',
                applyClass: 'btn-primary',
                cancelClass: 'btn-secondary',
                timePicker: true,
                locale: {
                    format: 'DD/MM/YYYY hh:mm A'
                }
            });
            $(elem).on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY hh:mm A') + ' - ' + picker.endDate.format('DD/MM/YYYY hh:mm A'));
            });

            $(elem).on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });

        },

        assignAndCreateNewTask: function (elem) {
            url = '{{route('tasks.update')}}';
            data = $('#kt_modal_update_task_form').serializeArray();
            task_organizer = {};
            task_assignee = {};
            notifications = {};
            $('.added-user-area').each(function (i, elem) {
                user = JSON.parse($(elem).attr('data-added-user-info'))
                user_type = $(elem).find('.user-permission-select').val()
                task_date_time = $(elem).find('.assigned_user_date_time').val();
                user['user_type'] = user_type;
                user['task_date_time'] = task_date_time;
                if (user_type == 'organizer') {
                    task_organizer = user;
                } else {
                    task_assignee[i] = user;
                }
            });

            $('.event_notification').each(function (i, elem) {
                medium = $(elem).find('.notification_medium').val()
                interval = $(elem).find('.notification_time').val()
                unit = $(elem).find('.notification_unit').val()
                notification = {medium, interval, unit, is_dispatched: 0}
                notifications[i] = notification
            })

            task_to_event = $('#task_to_event').is(":checked") ? 1 : 0;

            data.push({name: 'task_organizer', value: JSON.stringify(task_organizer)});
            data.push({name: 'task_assignee', value: JSON.stringify(task_assignee)});
            data.push({name: 'notifications', value: JSON.stringify(notifications)});
            data.push({name: 'task_to_event', value: JSON.stringify(task_to_event)});

            ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                if (response.status != 'error') {
                    $('#m_panel_toggle').click(quickPanelToggler());
                    $('.menu-item.menu-item-active a').click();
                } else {
                    toastr.error('Trouble in editing task');
                    console.log(response)
                }
            })
        },

        unAssignUser: function (elem) {
            KTApp.block('#kt_content')
            url = '{{route('tasks.user.unassign-user')}}';
            task_user_id = $(elem).attr('data-task-user-id');
            data = {task_user_id};
            ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                KTApp.unblock('#kt_content');
                if (response.status == 'success') {
                    $('#task_user_' + task_user_id).remove();
                    toastr.success('Successfully Done!');
                }

            })
            KTApp.unblock('#kt_content')
        },

        taskCommentPanel: function (elem) {
            KTApp.block('#kt_content')
            url = '{{route('tasks.comments.panel')}}';
            parent_id = $(elem).attr('data-parent-id');
            data = {task_id: $(elem).attr('data-task-id')};
            ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                KTApp.unblock('#kt_content')
                if (response.status != 'error') {
                    $('#m_panel_body').html(response)
                    title = (parent_id && parent_id != 0) ? 'Sub ' : ' ';

                    $('.offcanvas-title').html(title + 'Task Comments')
                    $('#m_panel_toggle').click(quickPanelToggler());
                } else {
                    toastr.error('Trouble in commenting task');
                    console.log(response)
                }
            })
            KTApp.unblock('#kt_content')
        },

        loadComments: function (elem) {
            selected_officer_id = $(elem).attr('data-receiver-officer-id');
            selected_officer_name = $(elem).attr('data-receiver-officer-name');
            task_id = $(elem).attr('data-task-id');

            $("#chat_panel_current_user_name").html(selected_officer_name)
            $('#cm_receiver_officer_id').val(selected_officer_id)

            url = '{{route('tasks.comments.get-by-officer-id')}}';
            data = {selected_officer_id, task_id}
            ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                if (response.status != 'error') {
                    $('#load_comments').html(response)
                } else {
                    toastr.error('Trouble in commenting task');
                    console.log(response)
                }
            })
        },

        addTaskComment: function (elem) {
            if ($('#comment_box').val() == '' || !$('#comment_box').val()) {
                toastr.warning('Please Write Comment');
                return;
            }
            data = $('#comment_on_task_form').serialize()
            url = '{{route('tasks.comments.save')}}';
            ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                if (response.status === 'success') {
                    // toastr.success('Successfully added comment.')
                    receiver_officer_id = $('#cm_receiver_officer_id').val()
                    $('#cm_receiver_' + receiver_officer_id + '[data-receiver-officer-id=' + receiver_officer_id + ']').click();
                    $('#comment_box').val('')
                } else {
                    toastr.error('Something went wrong')
                    console.log(response);
                }
            })
        },
    };
</script>
