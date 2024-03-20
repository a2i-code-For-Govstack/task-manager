<div id="m_panel_toggle"></div>
<div id="kt_quick_panel" class="offcanvas offcanvas-right p-8">
    <div class="offcanvas-header d-flex align-items-center justify-content-between pb-7">
        <h4 class="offcanvas-title font-weight-bold m-0"></h4>
        <a href="javascript:;" class="btn btn-xs btn-danger btn-hover-danger btn-icon" onclick="quickPanelClose()" id="kt_quick_panel_close" title="Close">
            <i class="ki ki-close icon-xs"></i>
        </a>
    </div>
    <div class="offcanvas-content">
        <div class="offcanvas-wrapper mb-5 scroll-pull" id="m_panel_body"></div>
        <div class="offcanvas-footer"></div>
    </div>
</div>

<script>
    function quickPanelToggler(width='70%') {
        quick_panel = $("#kt_quick_panel");
        if (quick_panel.hasClass('offcanvas-on')) {
            quickPanelClose();
        } else {
            quick_panel.addClass('offcanvas-on');
            quick_panel.css('opacity', 1);
            quick_panel.css('width', width);
            quick_panel.removeClass('d-none');
            $("html").addClass("side-panel-overlay");
        }
    }

    function quickPanelClose() {
        quick_panel = $("#kt_quick_panel");
        quick_panel.removeClass('offcanvas-on');
        quick_panel.css('opacity', 0);
        quick_panel.css('width', '50%');
        quick_panel.addClass('d-none');
        $("html").removeClass("side-panel-overlay");
        $('#m_panel_body').html('')
    }
</script>
