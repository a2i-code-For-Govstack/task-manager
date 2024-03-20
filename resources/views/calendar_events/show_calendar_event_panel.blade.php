<!--begin::Form-->
<style>
    .annual-plan-table {
        width: 100%;
    }

    .annual-plan-table table tr td {
        background-color: black;
    }

    .annual-plan-title {
        width: 40%;
        padding: 2%;
        background-color: #ebedf3
    }
</style>
<table class="annual-plan-table" border="1">
    <tr>
        <td class="annual-plan-title">Event Title</td>
        <td style="width: 60%;padding-left: 2%">
            {{ $event_details['event_title_en'] }}
        </td>
    </tr>
    <tr>
        <td class="annual-plan-title">Description</td>
        <td style="width: 60%;padding-left: 2%">
            {{ $event_details['event_description'] }}
        </td>
    </tr>
    <tr>
        <td class="annual-plan-title">Event Location</td>
        <td style="width: 60%;padding-left: 2%">
            {{ $event_details['event_location'] }}
        </td>
    </tr>
    <tr>
        <td class="annual-plan-title">Date Time</td>
        <td style="width: 60%;padding-left: 2%">
            {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event_details['event_end_date_time'])->format('d-m-Y') }}
            {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event_details['event_start_date_time'])->format('h:i A') }}
            - {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event_details['event_end_date_time'])->format('h:i A') }}
        </td>
    </tr>
    <tr>
        <td class="annual-plan-title">Meeting Link</td>
        <td style="width: 60%;padding-left: 2%">
            {{ $event_details['event_previous_link'] }}
        </td>
    </tr>

    <tr>
        <td class="annual-plan-title">Participants</td>
        <td style="width: 60%;padding-left: 2%">
            @foreach ($event_details['event_guests'] as $event_guest)
                <div class="py-4 border-bottom border-gray-300 border-bottom-dashed added-user-area"
                     data-added-user-email-area='{{ $event_guest['user_email'] }}'
                     data-added-user-info='{{ json_encode_escaped($event_guest) }}'>
                    <div class="d-flex align-items-center scroll scroll-x" style="width: 90%;">
                        <div class="symbol symbol-25px symbol-circle">
                            <img alt="Pic" src="{{ asset('assets/media/svg/avatars/blank.svg') }}"/>
                        </div>
                        <div class="ml-5">
                            <a href="javascript:;"
                               class="fs-5 fw-bolder text-gray-900 text-hover-dark mb-2">{{ $event_guest['user_name_bn'] }}
                                <span
                                    class="ml-2 badge badge-{{$event_guest['user_type'] == 'organizer' ? 'primary':'info'}}">{{ $event_guest['user_type'] }}</span></a>
                            <div class="fw-bold text-muted">{{ $event_guest['user_email'] }}</div>
                        </div>
                    </div>
                    {{--                            <div class="ms-2">--}}
                    {{--                                <select--}}
                    {{--                                    class="select-select2 form-select form-select-solid form-select-sm user-permission-select"--}}
                    {{--                                    readonly="true">--}}
                    {{--                                    <option value="organizer" selected="selected">{{ $event_guest['user_type'] }}--}}
                    {{--                                    </option>--}}
                    {{--                                </select>--}}
                    {{--                            </div>--}}
                </div>
            @endforeach
        </td>
    </tr>
</table>
