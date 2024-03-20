<h6 class="dropdown-header">
    Alerts Center
</h6>
@forelse ($notifications as $notification)
    <a class="dropdown-item d-flex align-items-center border-bottom" href="#">
        <div class="mr-3">
            <div class="icon-circle">
                <i class="fas fa-info-circle text-warning"></i>
            </div>
        </div>
        <div>
            <div class="small text-gray-500">{{ date('Y-m-d H:i:s', $notification['time']) }}</div>
            <div class="font-weight-bold">{{ $notification['title'] }}</div>
            <div class="">{{ json_decode($notification['message'], true)['content'] }}</div>
        </div>
    </a>
@empty
    <p>No Notifications Found.</p>
@endforelse
