<div class="row">
    <div class="col-md-12">
        @foreach($task_all_users as $task_individual_user)
            <div id="task_user_{{$task_individual_user['id']}}" class="cursor-pointer d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed added-user-area" data-added-user-email-area='{{$task_individual_user['user_email']}}' data-added-user-info='{{json_encode_escaped($task_individual_user)}}'>
                <div style="width: 80%" class="d-flex align-items-center">
                    <div class="symbol symbol-25px symbol-circle">
                        <img alt="Pic" src="{{asset('assets/media/svg/avatars/blank.svg')}}"/>
                    </div>
                    <div class="ml-5">

                        <a href="javascript:;" class="fs-5 fw-bolder text-gray-900 text-hover-dark mb-2">{{$task_individual_user['user_name_bn']}}
                            @if($task_individual_user['user_type'] =='organizer')
                                <span class="badge badge-circle badge-primary">Organizer</span>
                            @endif
                        </a>


                        <div class="fw-bold text-muted">{{$task_individual_user['user_email']}}</div>
                        <div class="fw-bold text-muted">{{$task_individual_user['user_designation_name_bn']}}</div>
                        <div class="">Status: <span class="badge text-capitalize">{{$task_individual_user['task_user_status']}}</span></div>
                    </div>
                    <input type="hidden" value="{{$task_individual_user['user_type']}}" class="user-permission-select">
                </div>

                @if($task_individual_user['user_type'] != 'organizer')
                    <div style="width:20%">
                        <div class="ml-2 pl-2 fa fa-times" onclick="Generic_Container.unAssignUser($(this))" data-task-user-id='{{$task_individual_user['id']}}'></div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
