<div class="card card-custom card-stretch gutter-b">
    @if($user_email)
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">
                   Calendar of : {{$user_email}}
                </span>
            </h3>
        </div>
    @endif
    <div class="card-body py-3">

        <div class="border-bottom mt-2 mb-2">Search For Other Calendar</div>
        <div class="search_box position-relative">
            <div class="office-select-div">
                <x-office-select  grid="12" unit="true"/>
                <span class="svg-icon svg-icon-2 svg-icon-lg-1 svg-icon-gray-500 position-absolute top-50 ml-5 mt-4 translate-middle-y">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black"></rect>
                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black"></path>
                        </svg>
                    </span>
                <input autocomplete="off" type="search" id="search_key"
                       class="form-control form-control-lg form-control-solid px-15"
                       name="search_key" value="" placeholder="username/name/email">

                <br>
                <button  type="button"
                         id="others_calendar_search_field" class="btn btn-primary rounded-0">
                    <span class="indicator-label">Search</span>
                </button>
                <button type="button" class="btn btn-danger" onclick="resetGuestSearchedArea()">Reset</button>
            </div>
        </div>

        <div class="searched_users_area_for_others_calendar max-h-200px bg-light" style="overflow-y: auto" onmouseenter="customScrollInit('searched_users_area', 'class')"></div>

        <div class="border-bottom mb-2 mt-2 font-weight-bolder">My Calendar</div>
        <div class="checkbox-list">
            <label class="form-check form-check-inline form-check-solid mr-5 is-invalid d-flex align-items-center">
                <input class="form-check-input-sm form-check-input calendar_filter_checkbox" disabled type="checkbox" {{in_array($self_calendar['user_office_id'], $filter_office_ids) ? '':'checked'}} data-office-id="{{$self_calendar['user_office_id']}}" name="self_calendar" value="{{$self_calendar['user_office_id']}}">
                <span class="fw-bold ps-1 fs-7 "> {{$self_calendar['user_office_name_en']}}</span>
            </label>
        </div>

        <div class="border-bottom mt-2 mb-2 font-weight-bolder">Other Calendars</div>
        <div class="checkbox-list">
            @foreach($shared_calendars as $shared_calendar)
                <label class="form-check form-check-inline form-check-solid mr-5 is-invalid d-flex align-items-center">
                    <input class="form-check-input-sm form-check-input calendar_filter_checkbox" type="checkbox" {{in_array($shared_calendar['user_office_id'], $filter_office_ids) ? '':'checked'}} data-office-id="{{$shared_calendar['user_office_id']}}" name="shared_calendar" value="{{$shared_calendar['user_office_id']}}">
                    <span class="fw-bold ps-1 fs-7 "> {{$shared_calendar['user_office_name_en']}}</span>
                </label>
            @endforeach
        </div>
    </div>
</div>

<script>
    $('.calendar_filter_checkbox').change(function () {
        filter_office_ids = [];
        $(".calendar_filter_checkbox").each(function (i, v) {
            if (!$(v).is(':checked')) {
                filter_office_ids.push($(v).val())
            }
        })
        EventCalendarContainer.loadCalendarEvents({filter_office_ids});
    });

    $('#others_calendar_search_field').on('click', function () {
        serach = {};
        serach = {
            office_id: $('#office_id').val(),
            unit_id: $('#office_unit_id').val(),
            search_value: $('#search_key').val(),
        }
        EventCalendarContainer.showPreferredGuests(serach,'view_other_calendar')
    });
</script>
