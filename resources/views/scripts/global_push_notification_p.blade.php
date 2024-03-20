<script src="https://js.pusher.com/beams/1.0/push-notifications-cdn.js"></script>
<script>
    // beamsClient.start()
    //     .then(() => beamsClient.addDeviceInterest('hello'))
    //     .then(() => console.log('Successfully registered and subscribed!'))
    //     .catch(console.error);
</script>

<script type="text/javascript" defer>
    navigator.serviceWorker.register('/service-worker.js');
    const beamsClient = new PusherPushNotifications.Client({
        // instanceId: '3d19265a-197f-4434-89aa-83b45cee4ffe',
        instanceId: 'd5b421ff-bc38-4a14-8ef5-a6a463667e08',
    });
    var BackGroundSyncData = {
            pushNotification: function () {
                console.log("pushNotification")
                var currentUserId = "{{config('fire_notification_constants.push_notifier_app_mode')}}_{{$current_office['employee_record_id']}}";
                beamsClient
                    .start()
                    .then((beamsClient) => beamsClient.getDeviceId())
                    .then((deviceId) => {
                            console.log('deviceId', deviceId)
                            beamsClient.getUserId().then((userId) => {
                                console.log(userId);
                                if (userId == null || userId == "null") {
                                    console.log('first condition');
                                    const beamsTokenProvider = new PusherPushNotifications.TokenProvider({
                                        url: "{{url('/api/notification/authorization')}}",
                                    });
                                    beamsClient.setUserId(currentUserId, beamsTokenProvider);
                                } else if (userId !== currentUserId) {

                                    console.log('second condition');

                                    beamsClient.stop().then(() => {
                                        console.log('stopped ' + userId);
                                        const beamsTokenProvider = new PusherPushNotifications.TokenProvider({
                                            url: "{{url('/api/notification/authorization')}}",
                                        });

                                        beamsClient.start()
                                            .then((beamsClient) => {
                                                beamsClient.setUserId(currentUserId, beamsTokenProvider);
                                            })
                                            .catch(console.error);
                                    });
                                } else {
                                    console.log('do nothing');
                                }
                            });
                        }
                    )
                    .catch(console.error);
            },
            registerNotification: function () {
                var currentUserId = "{{config('fire_notification_constants.push_notifier_app_mode')}}_{{$current_office['employee_record_id']}}";

                beamsClient.getUserId().then((userId) => {
                    // console.log('userId', userId);
                    // if (userId && userId !== currentUserId) {
                    //     return beamsClient.stop();
                    // }
                    if (userId == null || userId == "null") {
                        const beamsTokenProvider = new PusherPushNotifications.TokenProvider({
                            url: "{{url('/api/notification/authorization')}}",
                        });
                        beamsClient.start()
                            .then(() => beamsClient.setUserId(currentUserId, beamsTokenProvider))
                            .catch(console.error);
                    }
                }).catch(console.error);
            },

            renderNotification: function () {
                var notifications = JSON.parse(localStorage.getItem('Nothi-Notification-List'));
                if (notifications == null || notifications == "null") {
                    notifications = [];
                }
                var n_html = '';
                $.each(notifications, function (i, notification_data) {
                    var redirect_url = "javascript:;";
                    {{--if (notification_data.data.action == 'dak_receive') {--}}
                    {{--    redirect_url = '<?=$this->request->getAttribute('webroot')?>';--}}
                    {{--}--}}
                    var ico_class = '';
                    var btn_bg = '';
                    var title = '';
                    var body = '';
                    var notiHeader = '';
                    var decision = '';
                    if (notification_data.data.action_type === 'nothi_permission' || notification_data.data.action_type === 'note_permission') {
                        ico_class = 'fad fa-users-class';
                        btn_bg = 'bg-light-success';
                        body = notification_data.notification.body + "(" + notification_data.data.nothi_no + ")";
                        notiHeader = notification_data.notification.body;
                        title = notification_data.notification.title;
                    } else if (notification_data.data.action_type === 'dak_receive') {
                        ico_class = 'far fa-mailbox';
                        btn_bg = 'bg-light-primary';
                        body = notification_data.notification.body + "(" + notification_data.data.dak_decision + ")";
                        notiHeader = notification_data.notification.body;
                        title = notification_data.notification.title + "(" + notification_data.data.dak_decision + ")";
                    } else if (notification_data.data.action_type === 'note_receive') {
                        ico_class = 'fad fa-file-alt';
                        btn_bg = 'bg-light-warning';
                        body = notification_data.notification.body + "(" + notification_data.data.nothi_no + ")";
                        notiHeader = notification_data.notification.body;
                        title = notification_data.notification.title + "(" + notification_data.data.nothi_no + ")";
                    } else if (notification_data.data.action_type === 'potrojari_receive') {
                        ico_class = 'fal fa-share';
                        btn_bg = 'bg-light-info';
                        body = notification_data.notification.body + "(" + notification_data.data.nothi_no + ")";
                        notiHeader = notification_data.notification.body;
                        title = notification_data.notification.title + "(" + notification_data.data.nothi_no + ")";
                    } else {
                        ico_class = 'fad fa-comment-alt-lines';
                        btn_bg = 'bg-light';
                        body = notification_data.notification.body;
                        notiHeader = notification_data.notification.body;
                        title = notification_data.notification.title;
                    }

                    n_html += `<a href="javascript:;" class="navi-item">
						<div class="navi-link align-items-start">
							<div class="symbol symbol-40 mr-3 ` + btn_bg + `">
								<div class="symbol-label ` + btn_bg + `">
									<span class="svg-icon svg-icon-md svg-icon-success">
                                        <i class="` + ico_class + `"></i>
									</span>
								</div>
							</div>
							<div class="navi-text">
								<div class="text-dark font-weight-bold w-100 d-flex align-items-start flex-wrap"><p class="mb-0 mr-2">` + notiHeader + `</p><span class="label label-light-warning label-inline font-weight-bold">` + ((notification_data.data.date) ? getSwitchedData(notification_data.data.date) : "") + `</span></div>
								<div class="text-dark d-flex align-items-start flex-wrap notytitle font-weight-bold font-size-h6">` + title + `</div>
							</div>
						</div>
					</a>
                    <div class="separator separator-solid my-1"></div>`;
                })
                if (n_html.length == 0) {
                    n_html += `<div class="py-3 text-center border-top">No Notification found</div>`;
                } else {
                    n_html += `
                <div class="py-3 text-center border-top">
                    <button type="button" class="btn btn-color-gray-600 btn-active-color-primary btn_notification_clear">Clear All
                    <i class="fal fa-arrow-right"></i>
                </button>
            </div>`;
                }
                $("#notification_list").html(n_html);
                $("#notification_count").html("");
                $(".btn_notification_clear").on('click', function () {
                    localStorage.removeItem('Nothi-Notification-List');
                    $('#notification_dropdown').trigger('click');
                })
            }
            ,

            updateNotificationCount: function (event_data) {
                var notifications = JSON.parse(localStorage.getItem('Nothi-Notification-List'));
                if (notifications == null || notifications == "null") {
                    notifications = [];
                }
                notifications.unshift(event_data);
                localStorage.setItem('Nothi-Notification-List', JSON.stringify(notifications));
                var current_count = parseInt(replaceToEn($("#notification_count").html()));
                if (isNaN(current_count)) {
                    current_count = 0;
                }
                $("#notification_count").html(getSwitchedData(++current_count));
            }
        }
    ;

    navigator.serviceWorker.addEventListener('message', function (event) {
        BackGroundSyncData.updateNotificationCount(event.data);
    })

    $(function () {
        BackGroundSyncData.pushNotification();
        $('.notification').on('click', function () {
            BackGroundSyncData.renderNotification("");
        });
    })
</script>
