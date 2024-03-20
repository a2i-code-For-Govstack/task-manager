<script src="https://cdnjs.cloudflare.com/ajax/libs/push.js/1.0.8/push.min.js"></script>

<script>
    $('#alertsDropdown').click(function () {
        $('#alertsDropdownArea').html('')
        $.get("{{ route('notifications.lists') }}", function (data, status) {
            $('#alertsDropdownArea').html(data)
            $("#alertsCounter").text(0)
        });
    })
    const socket = new WebSocket('wss://{{config('ntfy.ntfy_domain')}}/task_manager_rid_{{ $userDetails['employee_record_id'] }}/ws');
    socket.addEventListener('message', function (event) {
        event_data = JSON.parse(event.data);
        if (event_data.title) {
            alert_count = $('#alertsCounter').text();
            alert_count = parseInt(alert_count)
            alert_count++;
            $("#alertsCounter").text(alert_count)

            if ('serviceWorker' in navigator && 'PushManager' in window) {
                message = JSON.parse(event_data.message);
                Push.create(event_data.title, {
                    body: message.content,
                    icon: '/icon.png',
                    timeout: 4000,
                    onClick: function () {
                        window.focus();
                        this.close();
                    }
                });
            }
        }
    });
</script>
