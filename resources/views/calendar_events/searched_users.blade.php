@if(Arr::has($searched_users, 'data'))
    @forelse($searched_users['data'] as $searched_user)
        @if($searched_user['user_email'])
            <div
                class="cursor-pointer d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed searched_user"
                data-searched-email="{{$searched_user['user_email']}}"
                data-searched-name-en="{{$searched_user['user_name_en']}}"
                data-searched-name-bn="{{$searched_user['user_name_bn']}}"
                data-user-info="{{json_encode($searched_user, JSON_UNESCAPED_UNICODE)}}">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-35px symbol-circle">
                        <img alt="Pic" src="{{asset('assets/media/avatars/blank.png')}}"/>
                    </div>
                    <div class="ml-5">
                        <a href="javascript:;"
                           class="fs-5 fw-bolder text-dark h5 mb-2">{{$searched_user['user_name_bn']}}</a>
                        <div class="fw-bold text-muted">{{$searched_user['user_email']}}</div>
                        <div class="fw-bold text-muted">{{$searched_user['user_designation_name_bn']}}</div>
                    </div>
                </div>
            </div>
        @endif
    @empty
        @if(filter_var($search_key, FILTER_VALIDATE_EMAIL))
            <div
                class="cursor-pointer d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed searched_user"
                data-searched-email="{{$search_key}}" data-searched-name-en="{{explode('@',$search_key)[0]}}"
                data-searched-name-bn="{{explode('@',$search_key)[0]}}"
                data-user-info="{{json_encode(['user_email' => $search_key, 'user_name_en' =>explode('@',$search_key)[0], 'user_name_bn' =>explode('@',$search_key)[0], 'user_designation_name_bn'=>'','user_officer_id'=>''], JSON_UNESCAPED_UNICODE)}}">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-35px symbol-circle">
                        <img alt="Pic" src="{{asset('assets/media/avatars/blank.png')}}"/>
                    </div>
                    <div class="ml-5">
                        <a href="javascript:;"
                           class="fs-5 fw-bolder text-dark h5 mb-2">{{explode('@',$search_key)[0]}}</a>
                        <div class="fw-bold text-muted">{{$search_key}}</div>
                    </div>
                </div>
            </div>
        @else
            <div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
                <div class="d-flex align-items-center">
                    <div class="ml-5">
                        <div class="fs-5 text-muted fw-bolder mb-2">No Guest Found</div>
                    </div>
                </div>
            </div>
        @endif
    @endforelse
@else
    @if(filter_var($search_key, FILTER_VALIDATE_EMAIL))
        <div
            class="cursor-pointer d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed searched_user"
            data-searched-email="{{$search_key}}" data-searched-name-en="{{explode('@',$search_key)[0]}}"
            data-searched-name-bn="{{explode('@',$search_key)[0]}}"
            data-user-info="{{json_encode(['user_email' => $search_key, 'user_name_en' =>explode('@',$search_key)[0], 'user_name_bn' =>explode('@',$search_key)[0], 'user_designation_name_bn'=>'','user_officer_id'=>''], JSON_UNESCAPED_UNICODE)}}">
            <div class="d-flex align-items-center">
                <div class="symbol symbol-35px symbol-circle">
                    <img alt="Pic" src="{{asset('assets/media/avatars/blank.png')}}"/>
                </div>
                <div class="ml-5">
                    <a href="javascript:;" class="fs-5 fw-bolder text-dark h5 mb-2">{{explode('@',$search_key)[0]}}</a>
                    <div class="fw-bold text-muted">{{$search_key}}</div>
                </div>
            </div>
        </div>
    @else
        <div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
            <div class="d-flex align-items-center">
                <div class="ml-5">
                    <div class="fs-5 text-muted fw-bolder mb-2">No User Found!</div>
                </div>
            </div>
        </div>
    @endif
@endif

<script>
    @if($type == 'view_other_calendar')
    $('.searched_user').click(function () {
        EventCalendarContainer.viewOtherCalendar($(this));
    })
    @else
    $('.searched_user').click(function () {
        EventCalendarContainer.addUserInInviteList($(this));
    })
    @endif
</script>
