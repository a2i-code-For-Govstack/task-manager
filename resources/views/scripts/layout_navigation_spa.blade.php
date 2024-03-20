<script>

    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip({
            trigger : 'hover'
        })
        $('.menu-item.menu-item-active a').click()
    })

    $('#kt_aside_menu .menu-item .menu-link').click(function (event) {
        $('.popover').popover('hide')
        KTApp.block('#kt_content');
        event.preventDefault();
        let menuItem = $(this)
        let url = menuItem.data('url')
        if (url.length < 1) {
            url = menuItem.attr('href');
        }
        $.ajax({
            async: true,
            type: "GET",
            url: url,
            cache: false,
            success: function (data, textStatus) {
                KTApp.unblock('#kt_content');
                if (data.status === 'error') {
                    toastr.error(data.data.error);
                } else {
                    $('#kt_aside_menu .menu-item').find('.menu-item-active').removeClass('menu-item-active')
                    $('#kt_aside_menu .menu-item').find('.active').removeClass('active')
                    menuItem.parent().addClass('menu-item-active')
                    menuItem.addClass('active')
                    $('#kt_content').html();
                    $('#kt_content').html(data);
                    menuItem = null;
                }
            },
            error: function (data) {
                console.log(data)
                if (data.status === 404) {
                    toastr.error(data.statusText);
                }
                KTApp.unblock('#kt_content');
            },
        });
    });
</script>
