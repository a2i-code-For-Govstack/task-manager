<div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
    <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
        <ul class="menu-nav">
            <li class="menu-item">
                <div class="dropdown">
                    <a class="btn btn-square btn-sm width-120p text-left {{(request()->routeIs('home.dashboard') || request()->routeIs('home.index')) ? 'btn-info' : 'btn-outline-info'}}" href="{{route('home.dashboard')}}"><i class="fas fa-tachometer"></i>Dashboard</a>
                </div>
            </li>
            <li class="menu-item">
                <div class="dropdown">
                    <a class="btn {{request()->routeIs('tasks.index') ? 'btn-warning' : 'btn-outline-warning'}} btn-square btn-sm width-120p text-left" href="{{route('tasks.index')}}"><i class="fas fa-clipboard-list-check"></i>Tasks</a>
                </div>
            </li>
            <li class="menu-item">
                <div class="dropdown">
                    <a class="btn {{request()->routeIs('cal-event.view') ? 'btn-primary' : 'btn-outline-primary'}} btn-square btn-sm width-120p text-left" href="{{route('cal-event.view')}}"><i class="fas fa-calendar-check"></i>Events</a>
                </div>
            </li>
            <li class="menu-item">
                <div class="dropdown">
                    <a class="btn {{request()->routeIs('settings.index') ? 'btn-primary' : 'btn-outline-primary'}} btn-square btn-sm width-120p text-left" href="{{route('settings.index')}}"><i class="fas fa-user-cog"></i>Settings</a>
                </div>
            </li>
        </ul>
    </div>
</div>
