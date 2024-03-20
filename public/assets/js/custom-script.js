var basic = $('#demo-basic').croppie({
    viewport: {
        width: 150,
        height: 600
    },
    boundary: {
        width: 400,
        height: 400
    }
});
$('.my-image').croppie({
    enableExif: true,
    mouseWheelZoom: true,
    showZoomer: true,
    enableResize: true,
    viewport: {
        width: 200,
        height: 200,
    },
    boundary: {
        width: 300,
        height: 300
    }
});
$('.my-signature').croppie({
    enableExif: true,
    mouseWheelZoom: true,
    showZoomer: true,
    enableResize: true,
    viewport: {
        width: 200,
        height: 200,
    },
    boundary: {
        width: 300,
        height: 300
    }
});

$('#owndesignation_structure_tree_search').keyup(function () {
    $('#own_office_organogram_tree').jstree(true).show_all();
    $('#own_office_organogram_tree').jstree('search', $(this).val());
});
$('#searchPlaneField').keyup(function () {
    $('#newAudit').jstree(true).show_all();
    $('#newAudit').jstree('search', $(this).val());
});

$("#editButtons").click(function () {
    $("#saveButtons").removeClass('d-none');
    $(this).addClass('d-none');
    $(".photo-upload").removeClass('d-none');
    $(".form-control").removeAttr('disabled');
    $(".form-control").addClass('border-dark');
});
$("#cancelButtons").click(function () {
    $("#saveButtons").addClass('d-none');
    $("#editButtons").removeClass('d-none');
    $(".photo-upload").addClass('d-none');
    $(".form-control").attr('disabled', 'disabled');
    $(".form-control").removeClass('border-dark');
});
// var resizeOpts = {
//     handles: 'e, w',
// };
// $("#kt_quick_cart").resizable(resizeOpts);
$("#kt_quick_cart").resize(function () {
    $(this).css("left", 'auto');
    $(this).css("overflow-x", 'hidden');
});
// $("#sidePanel").click(function(){
//     $("#kt_quick_cart").addClass('offcanvas-on')
// })
// $("#kt_quick_cart_toggle").click(function() {
//     $('html').addClass('side-panel-overlay');
// });
// $("#kt_quick_cart_close").click(function(){
//     $("#kt_quick_cart_close").removeClass('offcanvas-on');
//     $('html').removeClass('side-panel-overlay');
// });
$("#ajaxLoad").click(function () {
    $.get('ajax-load', function (data) {
        $("#kt_content").html(data);
    });
    // var url = '{{url('ajax-load')}}';
    // console.log('url', url);
})
// Class definition


/*********************************/
$.fn.extend({
    treed: function (o) {

        var openedClass = 'glyphicon-minus-sign';
        var closedClass = 'glyphicon-plus-sign';

        if (typeof o != 'undefined') {
            if (typeof o.openedClass != 'undefined') {
                openedClass = o.openedClass;
            }
            if (typeof o.closedClass != 'undefined') {
                closedClass = o.closedClass;
            }
        }
        ;

        //initialize each of the top levels
        var tree = $(this);
        tree.addClass("tree");
        tree.find('li').has("ul").each(function () {
            var branch = $(this); //li with children ul
            branch.prepend("<i class='indicator glyphicon " + closedClass + "'></i>");
            branch.addClass('branch');
            branch.on('click', function (e) {
                if (this == e.target) {
                    var icon = $(this).children('i:first');
                    icon.toggleClass(openedClass + " " + closedClass);
                    $(this).children().children().toggle();
                }
            })
            branch.children().children().toggle();
        });
        //fire event from the dynamically added icon
        tree.find('.branch .indicator').each(function () {
            $(this).on('click', function () {
                $(this).closest('li').click();
            });
        });
        //fire event to open branch if the li contains an anchor instead of text
        tree.find('.branch>a').each(function () {
            $(this).on('click', function (e) {
                $(this).closest('li').click();
                e.preventDefault();
            });
        });
        //fire event to open branch if the li contains a button instead of text
        tree.find('.branch>button').each(function () {
            $(this).on('click', function (e) {
                $(this).closest('li').click();
                e.preventDefault();
            });
        });
    }
});

//Initialization of treeviews

// $('#tree2').treed();

// $('#tree2').treed({openedClass:'fas fa-folder-open', closedClass:'fas fa-folder-close'});

// $('#tree2').treed({openedClass:'fas fa-chevron-right', closedClass:'fas fa-chevron-down'});
/**********************************/
$('select').select2();
// $('#fiscalyear').select2({
//     data: fiscalYear
// });
var data = $('#mySelect2').find(':selected');
$('#mySelect2').on('change', function () {
    var data = $("#mySelect2 option:selected").text();
    console.log('data', data);
})
/*************************/
var KTCalendarBasic = function () {

    return {
        //main function to initiate the module
        init: function () {
            var todayDate = moment().startOf('day');
            var YM = todayDate.format('YYYY-MM');
            var YESTERDAY = todayDate.clone().subtract(1, 'day').format('YYYY-MM-DD');
            var TODAY = todayDate.format('YYYY-MM-DD');
            var TOMORROW = todayDate.clone().add(1, 'day').format('YYYY-MM-DD');

            var calendarEl = document.getElementById('kt_calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: ['bootstrap', 'interaction', 'dayGrid', 'timeGrid', 'list'],
                themeSystem: 'bootstrap',

                isRTL: KTUtil.isRTL(),

                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },

                height: 800,
                contentHeight: 780,
                aspectRatio: 3,  // see: https://fullcalendar.io/docs/aspectRatio

                nowIndicator: true,
                now: TODAY + 'T09:25:00', // just for demo

                views: {
                    dayGridMonth: {buttonText: 'month'},
                    timeGridWeek: {buttonText: 'week'},
                    timeGridDay: {buttonText: 'day'}
                },

                defaultView: 'dayGridMonth',
                defaultDate: TODAY,

                editable: true,
                eventLimit: true, // allow "more" link when too many events
                navLinks: true,
                events: [
                    {
                        title: 'All Day Event',
                        start: YM + '-01',
                        description: 'Toto lorem ipsum dolor sit incid idunt ut',
                        className: "fc-event-danger fc-event-solid-warning"
                    },
                    {
                        title: 'Reporting',
                        start: YM + '-14T13:30:00',
                        description: 'Lorem ipsum dolor incid idunt ut labore',
                        end: YM + '-14',
                        className: "fc-event-success"
                    },
                    {
                        title: 'Company Trip',
                        start: YM + '-02',
                        description: 'Lorem ipsum dolor sit tempor incid',
                        end: YM + '-03',
                        className: "fc-event-primary"
                    },
                    {
                        title: 'ICT Expo 2017 - Product Release',
                        start: YM + '-03',
                        description: 'Lorem ipsum dolor sit tempor inci',
                        end: YM + '-05',
                        className: "fc-event-light fc-event-solid-primary"
                    },
                    {
                        title: 'Dinner',
                        start: YM + '-12',
                        description: 'Lorem ipsum dolor sit amet, conse ctetur',
                        end: YM + '-10'
                    },
                    {
                        id: 999,
                        title: 'Repeating Event',
                        start: YM + '-09T16:00:00',
                        description: 'Lorem ipsum dolor sit ncididunt ut labore',
                        className: "fc-event-danger"
                    },
                    {
                        id: 1000,
                        title: 'Repeating Event',
                        description: 'Lorem ipsum dolor sit amet, labore',
                        start: YM + '-16T16:00:00'
                    },
                    {
                        title: 'Conference',
                        start: YESTERDAY,
                        end: TOMORROW,
                        description: 'Lorem ipsum dolor eius mod tempor labore',
                        className: "fc-event-primary"
                    },
                    {
                        title: 'Meeting',
                        start: TODAY + 'T10:30:00',
                        end: TODAY + 'T12:30:00',
                        description: 'Lorem ipsum dolor eiu idunt ut labore'
                    },
                    {
                        title: 'Lunch',
                        start: TODAY + 'T12:00:00',
                        className: "fc-event-info",
                        description: 'Lorem ipsum dolor sit amet, ut labore'
                    },
                    {
                        title: 'Meeting',
                        start: TODAY + 'T14:30:00',
                        className: "fc-event-warning",
                        description: 'Lorem ipsum conse ctetur adipi scing'
                    },
                    {
                        title: 'Happy Hour',
                        start: TODAY + 'T17:30:00',
                        className: "fc-event-info",
                        description: 'Lorem ipsum dolor sit amet, conse ctetur'
                    },
                    {
                        title: 'Dinner',
                        start: TOMORROW + 'T05:00:00',
                        className: "fc-event-solid-danger fc-event-light",
                        description: 'Lorem ipsum dolor sit ctetur adipi scing'
                    },
                    {
                        title: 'Birthday Party',
                        start: TOMORROW + 'T07:00:00',
                        className: "fc-event-primary",
                        description: 'Lorem ipsum dolor sit amet, scing'
                    },
                    {
                        title: 'Click for Google',
                        url: 'http://google.com/',
                        start: YM + '-28',
                        className: "fc-event-solid-info fc-event-light",
                        description: 'Lorem ipsum dolor sit amet, labore'
                    }
                ],

                eventRender: function (info) {
                    var element = $(info.el);

                    if (info.event.extendedProps && info.event.extendedProps.description) {
                        if (element.hasClass('fc-day-grid-event')) {
                            element.data('content', info.event.extendedProps.description);
                            element.data('placement', 'top');
                            KTApp.initPopover(element);
                        } else if (element.hasClass('fc-time-grid-event')) {
                            element.find('.fc-title').append('<div class="fc-description">' + info.event.extendedProps.description + '</div>');
                        } else if (element.find('.fc-list-item-title').lenght !== 0) {
                            element.find('.fc-list-item-title').append('<div class="fc-description">' + info.event.extendedProps.description + '</div>');
                        }
                    }
                }
            });

            calendar.render();
        }
    };
}();

jQuery(document).ready(function () {
    KTCalendarBasic.init();
});
/*************************/
var KTSummernoteDemo = function () {
    // Private functions
    var demos = function () {
        $('.create_plan').summernote({
            height: 250,
            tabsize: 2
        });
    }

    return {
        // public functions
        init: function () {
            demos();
        }
    };
}();

// Initialization
jQuery(document).ready(function () {
    KTSummernoteDemo.init();
});
// Split(['#split-0', '#split-1', '#split-2'],{
//     minSize: 150,
//     snapOffset: 10,
//     gutterSize: 5,
// });
// Split(['#split-3', '#split-4'], {
//     direction: 'vertical',
//     minSize: 150,
//     gutterSize: 5,
//     snapOffset: 10,
// });



