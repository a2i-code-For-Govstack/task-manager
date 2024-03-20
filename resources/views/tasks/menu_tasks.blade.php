<div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
    {{--    <x-menu-module-name>Tasks</x-menu-module-name>--}}
    <div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1"
         data-menu-dropdown-timeout="500">
        <!--begin::Menu Nav-->
        <ul class="menu-nav">
            @if(isset($task_setup) && $task_setup != '')
                <x-menu-item class="menu-item-active" href="{{route('tasks.load-all-tasks')}}?task_setup={{$task_setup}}" icon="fa fa-clipboard-list-check">Tasks</x-menu-item>
            @else
                <x-menu-item class="menu-item-active" href="{{route('tasks.load-all-tasks')}}" icon="fa fa-calendar">Tasks</x-menu-item>
            @endif

        </ul>
        <!--end::Menu Nav-->
    </div>
</div>
