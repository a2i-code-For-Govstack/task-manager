<div class="row gy-5 g-xl-8">
    <div class="col-md-12">
        <div class="card card-custom ml-3 mb-xl-8">
            <h1 class="h1 p-2 pl-5 mb-3 border-bottom">Settings</h1>
        </div>
    </div>
</div>
<div class="row gy-5 g-xl-8" style="max-height: 80vh; overflow-y: scroll">
    <div class="col-xl-12">
        <div class="card card-custom ml-3 mb-xl-8">
            <div class="card-header border-0 py-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label font-weight-bolder text-dark">Notification Settings</span>
                </h3>
                <div class="card-toolbar"></div>
            </div>
            <div class="card-body pt-2" id="pending_task_area">
                <form class="form">
                    <div class="form-group row">
                        <label class="col-3 col-form-label">Email Notification</label>
                        <div class="col-3">
                           <span class="switch switch-lg switch-icon">
                            <label>
                             <input onchange="changeUserNotificationSetting($(this))" data-notification-type="email" type="checkbox" {{$user_notification ? ($user_notification->email ? 'checked=checked' : '') : ''}} name="notification_checkbox"/>
                             <span></span>
                            </label>
                           </span>
                        </div>
                        <label class="col-3 col-form-label">Push Notification</label>
                        <div class="col-3">
                           <span class="switch switch-lg switch-icon">
                            <label>
                             <input onchange="changeUserNotificationSetting($(this))" data-notification-type="web_pusher" type="checkbox" {{$user_notification ? ($user_notification->web_pusher ? 'checked=checked' : '') : ''}} name="notification_checkbox"/>
                             <span></span>
                            </label>
                           </span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript">
    function changeUserNotificationSetting(elem) {
        url = '{{route('settings.notifications.change')}}';
        type = $(elem).attr('data-notification-type')
        status = $(elem).is(':checked') === true ? 1 : 0;
        data = {type, status}
        ajaxCallAsyncCallbackAPI(url, data, 'post', function (resp) {
            if (resp.status === 'success') {
                toastr.success(resp.data);
            } else {
                console.log(resp)
                toastr.error(resp.message);
            }
        })
    }
</script>
