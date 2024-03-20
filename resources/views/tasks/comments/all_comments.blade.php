@foreach($task_comments as $comment)

    <div class="messages">
        <div
            class="d-flex flex-column mb-5 align-items-{{$comment['sender_officer_id'] == $user_officer_id?'end' : 'start'}}">
            <div
                class="mt-2 rounded p-5 bg-light-{{$comment['sender_officer_id'] == $user_officer_id?'success' : 'primary'}} text-dark-50 font-weight-bold font-size-lg text-left max-w-400px">
                <span class="text-warning font-size-sm">{{$comment['sender_name_en']}}</span>
                <br>
                {{$comment['comment']}}
                <br>
                <span
                    class="text-warning font-size-sm">{{\Carbon\Carbon::parse($comment['created_at'])->format('d-m-Y H:i A')}}</span>
            </div>
        </div>
    </div>
@endforeach
