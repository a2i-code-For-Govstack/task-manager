@if(Arr::has($pending_tasks, 'data') && count($pending_tasks['data']) > 0)
@foreach($pending_tasks['data'] as $pending_task)
<div class="mb-8">
    <div class="d-flex align-items-center" id="task_item_area_{{$pending_task['id']}}">
        <span class="bullet bullet-vertical h-40px bg-warning mr-2"></span>
        <div class="flex-grow-1 mr-1">
            {{--                                    <a href="{{route('tasks.index')}}?task_setup=edit_{{$pending_task['id']}}_page_{{$pending_tasks['current_page']}}" class="text-gray-800 text-hover-primary fw-bolder fs-6">{{$pending_task['title_en']}}--}}
            <a href="javascript:;" class="text-gray-800 text-hover-primary fw-bolder fs-6">{{$pending_task['title_en']}}
                <span class="{{$pending_task['has_event'] == 1 ? 'ml-2 text-danger fa fa-calendar' :'' }}"></span></a>
            @if($pending_task['meta_data'])
                @php
                    $meta_data = json_decode(base64_decode($pending_task['meta_data']), true);
                @endphp
            @else
                @php
                    $meta_data = [];
                @endphp
            @endif
            <div class="d-flex">
                {{\Carbon\Carbon::create($pending_task['task_start_date_time'])->format('d M, Y H:i A')}}
                -
                {{\Carbon\Carbon::create($pending_task['task_end_date_time'])->format('d M, Y H:i A')}}
            </div>
            <div class="d-flex">
                {{$pending_task['task_user']['comments']}}
            </div>
        </div>
    </div>
    <div>
        <div class="mt-4 ml-4">
            @if(isset($meta_data) && is_array($meta_data) && Arr::has($meta_data,'type') && $pending_task['task_user']['user_type'] == 'organizer')
                <a href="{{$meta_data['return_url']}}" target="_blank"
                   class="text-gray-800 text-hover-primary fw-bolder fs-6">
                    <span class="text-uppercase badge badge-info">{{$meta_data['type']}}</span>
                </a>
            @endif
            <span
                class="label label-lg label-inline font-weight-bold {{$pending_task['task_user']['user_type'] == 'organizer' ? 'label-light-primary' : 'label-light-info'}} text-capitalize">{{$pending_task['task_user']['user_type'] == 'organizer' ? 'Self' : $pending_task['task_organizer']['user_name_bn']}}</span>
            <span
                class="label label-light-warning label-lg label-inline font-weight-bold text-capitalize">{{Carbon\Carbon::now()->lt(\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $pending_task['task_end_date_time'])) ? 'Todo' : 'Pending'}}</span>
            <a href="javascript:;"
               class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-1 ml-1"
               data-toggle="tooltip" data-placement="top" title="Mark Completed"
               onclick="Generic_Container.updateTaskStatus($(this))"
               data-task-id="{{$pending_task['id']}}">
                <!--begin::Svg Icon | path: icons/duotune/art/art005.svg-->
                <span class="svg-icon svg-icon-3">
                    <i class="fad fa-check-circle"></i>
                {{--<i class="fa fa-check"></i>--}}
            </span>
            </a>
            <a href="javascript:;"
               class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-1 ml-1"
               onclick="Generic_Container.taskCommentPanel($(this))" data-toggle="tooltip"
               data-placement="top" title="Add Comment" data-task-id="{{$pending_task['id']}}">
            <span class="svg-icon svg-icon-3">
               <i class="fad fa-comment-alt"></i>
            </span>
            </a>
        </div>

    </div>
</div>
@endforeach
    @if($pending_tasks['total'] >= $pending_tasks['to'])
        <table style="width: 100%">
            <thead>
            <tr>
                <td style="width: 30%; text-align: right">
                    {{$pending_tasks['from']}} - {{$pending_tasks['to']}} of {{$pending_tasks['total']}}
                </td>
                <td style="width: 10%; text-align: right">
                    <button onclick="DailyDashboardContainer.paginate($(this))" data-area="pending_task_area" data-search-key="" data-paginate-page="{{$pending_tasks['current_page'] - 1}}" data-paginate-url="{{route('tasks.pending')}}" class="paginate_btn btn btn-secondary btn-icon" {{$pending_tasks['from'] <= 1 ? 'disabled' : ''}}><i class="fa fa-chevron-left"></i></button>
                    <button onclick="DailyDashboardContainer.paginate($(this))" data-area="pending_task_area" data-search-key="" data-paginate-page="{{$pending_tasks['current_page'] + 1}}" data-paginate-url="{{route('tasks.pending')}}" class="paginate_btn btn btn-secondary btn-icon" {{$pending_tasks['to'] >= $pending_tasks['total'] ? 'disabled' : ''}}>
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
