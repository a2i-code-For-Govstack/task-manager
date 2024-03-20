<script>var hostUrl = "assets/";</script>
<!--begin::Global Javascript Bundle(used by all pages)-->
<script src="{{asset('assets/plugins/global/plugins.bundle.js')}}"></script>
<script src="{{asset('assets/js/scripts.bundle.js')}}"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Page Vendors Javascript(used by this page)-->
<script src="{{asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js')}}"></script>
<script src="{{asset('assets/plugins/global/timepicker/jquery.timepicker.min.js')}}"></script>
<script src="{{asset('assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
<!--end::Page Vendors Javascript-->
<!--begin::Page Custom Javascript(used by this page)-->
<script src="{{asset('assets/js/widgets.bundle.js')}}"></script>
<script src="{{asset('assets/js/custom/widgets.js')}}"></script>
<script src="{{asset('assets/js/custom/apps/chat/chat.js')}}"></script>
<script src="{{asset('assets/js/debounce.jquery.js')}}"></script>
<script src="{{asset('assets/js/custom/utilities/modals/users-search.js')}}"></script>
{{--<script>--}}
{{--    const beamsClient = new PusherPushNotifications.Client({--}}
{{--        instanceId: '3d19265a-197f-4434-89aa-83b45cee4ffe',--}}
{{--    });--}}
{{--    console.log('hello')--}}
{{--    beamsClient.start()--}}
{{--        .then(() => beamsClient.addDeviceInterest('hello'))--}}
{{--        .then(() => console.log('Successfully registered and subscribed!'))--}}
{{--        .catch(console.error);--}}
{{--</script>--}}
<script src="{{asset('assets/js/tapp.js')}}"></script>
<script src="{{asset('assets/js/custom-scripts.js')}}"></script>
