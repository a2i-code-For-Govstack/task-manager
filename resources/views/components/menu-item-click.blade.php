@props(['icon_color' => '#3699FF', 'href' => '404', 'icon' => 'far fa-dashboard', 'class' => '', 'demo' => ''])
<li class="menu-item {{$class}}" aria-haspopup="true">
    <a href="javascript:;" ddd={{ $demo }} class="menu-link">
        <i class="{{$icon}} menu-icon" style="color: {{$icon_color}}"></i>
        <span class="menu-text">{{ $slot  }}</span>
    </a>
</li>
