@if(Arr::has($task_assignees, 'data') && count($task_assignees['data']) > 0)
    @foreach($task_assignees['data'] as $task_assignee)
        <a href="javascript:;" class="navi-item">
            <div class="navi-link">
                <div class="symbol symbol-50 symbol-light mr-4">
                                <span class="symbol-label">
                                    <img src="{{asset('assets/media/avatars/businessman.png')}}"
                                         class="h-75 align-self-end"
                                         alt="">
                                </span>
                </div>
                <div class="navi-text">
                    <div class="font-weight-bold">
                        <strong>{{$task_assignee['user_name_bn']}}</strong>, {{$task_assignee['user_designation_name_bn']}}
                    </div>
                    <div class="text-muted">
                        <span class="text-primary font-weight-bold">{{$task_assignee['task']['title_en']}}</span>
                        <span
                            class="label label-light-{{$task_assignee['task_user_status'] =='completed'?'success':'danger'}} label-inline font-weight-bold text-capitalize">
                            @if($task_assignee['task_user_status'] == 'pending')
                                {{ Carbon\Carbon::now()->lt(\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $task_assignee['task']['task_end_date_time'])) ? 'Todo' : 'Pending' }}
                            @else
                                {{$task_assignee['task_user_status']}}
                            @endif
                        </span>
                    </div>
                    <div class="text-muted">
                        {{\Carbon\Carbon::create($task_assignee['task']['task_start_date_time'])->format('d M, Y H:i A')}}
                        -
                        {{\Carbon\Carbon::create($task_assignee['task']['task_end_date_time'])->format('d M, Y H:i A')}}
                    </div>
                </div>
            </div>
        </a>
    @endforeach

    @if($task_assignees['total'] >= $task_assignees['to'] )
        <table style="width: 100%">
            <thead>
            <tr>
                <td style="width: 85%; text-align: right">
                    {{$task_assignees['from']}} - {{$task_assignees['to']}} of {{$task_assignees['total']}}
                </td>
                <td style="width: 15%; text-align: right">
                    <button onclick="DailyDashboardContainer.paginate($(this))" data-area="assigned_user_table_area"
                            data-search-key="" data-paginate-page="{{$task_assignees['current_page'] - 1}}"
                            data-paginate-url="{{route('tasks.user.assigned')}}"
                            class="pagination__button" {{$task_assignees['from'] <= 1 ? 'disabled' : ''}}><i
                            class="fa fa-chevron-left"></i>
                    </button>
                    <button onclick="DailyDashboardContainer.paginate($(this))" data-area="assigned_user_table_area"
                            data-search-key="" data-paginate-page="{{$task_assignees['current_page'] + 1}}"
                            data-paginate-url="{{route('tasks.user.assigned')}}"
                            class="pagination__button" {{$task_assignees['to'] >= $task_assignees['total'] ? 'disabled' : ''}}>
                        <i class="fa fa-chevron-right"></i>
                    </button>
                </td>
            </tr>
            </thead>
        </table>
    @endif
@else
    <div class="h4 text-center">No data found.</div>
@endif
