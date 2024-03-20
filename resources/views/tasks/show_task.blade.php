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
        <td class="annual-plan-title">Task Title</td>
        <td style="width: 60%;padding-left: 2%">
            {{ $task_info['title_en'] }}
        </td>
    </tr>
    <tr>
        <td class="annual-plan-title">Description</td>
        <td style="width: 60%;padding-left: 2%">
            {{ $task_info['description'] }}
        </td>
    </tr>
    <tr>
        <td class="annual-plan-title">Task Location</td>
        <td style="width: 60%;padding-left: 2%">
            {{ $task_info['location'] }}
        </td>
    </tr>
    <tr>
        <td class="annual-plan-title">Date and Time</td>
        <td style="width: 60%;padding-left: 2%">
            {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $task_info['task_start_date_time'])->format('d-m-Y h:i A') }}
            - {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $task_info['task_end_date_time'])->format('d-m-Y h:i A') }}
        </td>
    </tr>
    <tr>
        <td class="annual-plan-title">Participants</td>
        <td style="width: 60%;padding-left: 2%">
            @foreach ($task_info['task_users'] as $event_guest)
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
                                @if($event_guest['user_type'] == 'organizer')
                                    <span class="ml-2 badge badge-primary">{{ $event_guest['user_type'] }}</span></a>
                            @endif
                            <div class="fw-bold text-muted">{{ $event_guest['user_email'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </td>
    </tr>
</table>
