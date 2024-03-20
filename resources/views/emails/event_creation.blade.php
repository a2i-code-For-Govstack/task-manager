<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Invitation</title>

    <link href="{{asset('assets/plugins/global/plugins.bundle.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/css/style.bundle.css')}}" rel="stylesheet" type="text/css"/>

</head>
<body>
<div style="font-family:Arial,Helvetica,sans-serif; line-height: 1.5; font-weight: normal; font-size: 15px; color: #2F3044; min-height: 100%; margin:0; padding:0; width:100%; background-color:#edf2f7">
    <br>
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin:0 auto; padding:0; max-width:600px">
        <tbody>
        {{--        <tr>--}}
        {{--            <td align="center" valign="center" style="text-align:center; padding: 40px">--}}
        {{--                <a href="https://keenthemes.com" rel="noopener" target="_blank">--}}
        {{--                    <img alt="Logo" src="/metronic8/demo1/assets/media/logos/mail.svg">--}}
        {{--                </a>--}}
        {{--            </td>--}}
        {{--        </tr>--}}
        <tr>
            <td align="left" valign="center">
                <div style="text-align:left; margin: 0 20px; padding: 40px; background-color:#ffffff; border-radius: 6px">
                    <!--begin:Email content-->
                    <div style="padding-bottom: 30px">You have been invited to join
                        <strong>{{$event_data['event_title']}}</strong>
                    </div>
                    <div style="padding-bottom: 40px; text-align:center;">
                        {!! $event_data['event_description'] !!}
                    </div>
                    <div style="padding-bottom: 30px">Start: {{$event_data['event_start']}}</div>
                    <div style="padding-bottom: 30px">End: {{$event_data['event_end']}}</div>
                    <div style="padding-bottom: 30px">{{Arr::has($event_data, 'event_location') ?  'Event Location: '.$event_data['event_location'] : ''}}</div>
                    <!--end:Email content-->
                </div>
            </td>
        </tr>
        <tr>
            <td align="center" valign="center" style="font-size: 13px; text-align:center;padding: 20px; color: #6d6e7c;">
                <p>Copyright © <a href="https://tappware.com" rel="noopener" target="_blank">Tappware</a>.</p>
            </td>
        </tr>


        </tbody>
    </table>
</div>
</body>
</html>
