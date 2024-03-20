@if(count($task_users) > 0)
    <div class="d-flex flex-row">
        <!--begin::Aside-->
        <div class="flex-row-auto" id="kt_chat_aside">
            <!--begin::Card-->
            <div class="card card-custom">
                <!--begin::Body-->
                <div class="card-body p-2" style="background-color: lightgrey;border-radius: 8px">
                    <!--begin:Users-->
                    <div class="mt-7" id="chat_recipients_panel">
                        <!--begin:User-->
                        @foreach($task_users as $task_user)
                            <a id="cm_receiver_{{$task_user['user_officer_id']}}"
                               onclick="Generic_Container.loadComments($(this))"
                               href="javascript:;"
                               data-receiver-officer-id="{{$task_user['user_officer_id']}}"
                               data-task-id="{{$task_id}}"
                               data-receiver-officer-name="{{$task_user['user_name_bn']}}"
                            >
                                <div
                                    class="d-flex align-items-center justify-content-between mb-5 bg-hover-primary-o-1 w-300px">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-circle symbol-50 mr-3">
                                            <img alt="Pic" src="{{asset('assets/media/avatars/blank.png')}}">
                                        </div>
                                        <div class="d-flex flex-column">
                                        <span
                                            class="text-dark-75 text-hover-primary font-weight-bold font-size-lg">{{$task_user['user_name_bn']}}</span>
                                            <span class="text-muted font-weight-bold font-size-sm">{{$task_user['user_designation_name_bn']}}
                                ,{{$task_user['user_office_name_bn']}}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <hr>
                    @endforeach

                    <!--end:User-->

                    </div>
                    <!--end:Users-->
                </div>
                <!--end::Body-->
            </div>
            <!--end::Card-->
        </div>
        <div class="flex-row-fluid ml-lg-8" id="kt_chat_content">
            <!--begin::Card-->
            <div class="card card-custom"
                 style="border-color: #007bff82!important;padding-bottom: 2em !important;box-shadow: 0px 0px 10px 1px #007bff6e;margin-top: 7px">
                <!--begin::Header-->
                <div class="card-header align-items-center px-4 py-3">
                    <div class="text-left flex-grow-1"></div>

                    <div class="text-center flex-grow-1">
                        <div class="text-dark-75 font-weight-bold font-size-h5" id="chat_panel_current_user_name"></div>
                    </div>

                    <div class="text-right flex-grow-1">
                        <div class="dropdown dropdown-inline"></div>
                    </div>
                </div>
                <div class="card-body" style="min-height: 300px;">
                    <div id="load_comments">

                    </div>
                </div>
                <div class="card-footer align-items-center">
                    <!--begin::Compose-->
                    <form class="form" action="" id="comment_on_task_form">
                        <input type="hidden" name="sender_officer_id" id="sender_officer_id"
                               value="{{$user_officer_id}}">
                        <input type="hidden" name="task_id" id="task_id" value="{{$task_id}}">
                        <input type="hidden" name="receiver_officer_id" id="cm_receiver_officer_id" value="">
                        <textarea id="comment_box" name="comment" class="form-control border-1" rows="2"
                                  placeholder="Type a message"></textarea>
                    </form>
                    <div class="d-flex align-items-center justify-content-between mt-5">
                        <div class="mr-3">
                            {{--                        <a href="#" class="btn btn-clean btn-icon btn-md mr-1"><i--}}
                            {{--                                class="flaticon2-photograph icon-lg"></i></a>--}}
                            {{--                        <a href="#" class="btn btn-clean btn-icon btn-md"><i--}}
                            {{--                                class="flaticon2-photo-camera  icon-lg"></i></a>--}}
                        </div>
                        <div>
                            <button type="button"
                                    onclick="Generic_Container.addTaskComment($(this))"
                                    class="btn btn-primary btn-md text-uppercase font-weight-bold chat-send py-2 px-6">
                                Send
                            </button>
                        </div>
                    </div>
                    <!--begin::Compose-->
                </div>
                <!--end::Footer-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Content-->
    </div>
@else
    <div class="alert alert-info text-center" role="alert">
        No other users assigned to this task.
    </div>
@endif
<script>
    $(document).ready(function () {
        $('#chat_recipients_panel').children().first().click();
    })
</script>
