$(document).on("ready", function () {
    "use strict";
    // =======================================================
    if (window.localStorage.getItem("hs-builder-popover") === null) {
        $("#builderPopover")
            .popover("show")
            .on("shown.bs.popover", function () {
                $(".popover").last().addClass("popover-dark");
            });

        $(document).on("click", "#closeBuilderPopover", function () {
            window.localStorage.setItem("hs-builder-popover", true);
            $("#builderPopover").popover("dispose");
        });
    } else {
        $("#builderPopover").on("show.bs.popover", function () {
            return false;
        });
    }

    // =======================================================
    $(".js-navbar-vertical-aside-toggle-invoker").click(function () {
        $(".js-navbar-vertical-aside-toggle-invoker i").tooltip("hide");

        if (localStorage.getItem("isMiniSidebar")) {
            localStorage.removeItem("isMiniSidebar");
        } else {
            localStorage.setItem("isMiniSidebar", true);
        }
    });

    // INITIALIZATION OF MEGA MENU
    // =======================================================
    // var megaMenu = new HSMegaMenu($(".js-mega-menu"), {
    //     desktop: {
    //         position: "left",
    //     },
    // }).init();

    // INITIALIZATION OF NAVBAR VERTICAL NAVIGATION
    // =======================================================
    var sidebar = $(".js-navbar-vertical-aside").hsSideNav();

    // INITIALIZATION OF TOOLTIP IN NAVBAR VERTICAL MENU
    // =======================================================
    $(".js-nav-tooltip-link").tooltip({ boundary: "window" });

    $(".js-nav-tooltip-link").on("show.bs.tooltip", function (e) {
        if (!$("body").hasClass("navbar-vertical-aside-mini-mode")) {
            return false;
        }
    });

    // INITIALIZATION OF UNFOLD
    // =======================================================
    $(".js-hs-unfold-invoker").each(function () {
        var unfold = new HSUnfold($(this)).init();
    });

    // INITIALIZATION OF FORM SEARCH
    // =======================================================
    $(".js-form-search").each(function () {
        new HSFormSearch($(this)).init();
    });

    // INITIALIZATION OF SELECT2
    // =======================================================
    $(".js-select2-custom").each(function () {
        let $select = $(this);
        let isInsideOffcanvas = $select.closest(".offcanvas-filter").length > 0;
        let isInsideModal = $select.closest(".modal").length > 0;

        $select.select2({
            dropdownParent: isInsideOffcanvas
                ? $select.closest(".offcanvas-filter")
                : isInsideModal
                ? $select.closest(".modal")
                : null,
        });
        let select2 = $.HSCore.components.HSSelect2.init($select);
    });

    // INITIALIZATION OF DATERANGEPICKER
    // =======================================================
    $(".js-daterangepicker").daterangepicker();

    $(".js-daterangepicker-times").daterangepicker({
        timePicker: true,
        startDate: moment().startOf("hour"),
        endDate: moment().startOf("hour").add(32, "hour"),
        locale: {
            format: "M/DD hh:mm A",
        },
    });

    var start = moment();
    var end = moment();

    function cb(start, end) {
        $(
            "#js-daterangepicker-predefined .js-daterangepicker-predefined-preview"
        ).html(start.format("MMM D") + " - " + end.format("MMM D, YYYY"));
    }

    $("#js-daterangepicker-predefined").daterangepicker(
        {
            startDate: start,
            endDate: end,
            ranges: {
                Today: [moment(), moment()],
                Yesterday: [
                    moment().subtract(1, "days"),
                    moment().subtract(1, "days"),
                ],
                "Last 7 Days": [moment().subtract(6, "days"), moment()],
                "Last 30 Days": [moment().subtract(29, "days"), moment()],
                "This Month": [
                    moment().startOf("month"),
                    moment().endOf("month"),
                ],
                "Last Month": [
                    moment().subtract(1, "month").startOf("month"),
                    moment().subtract(1, "month").endOf("month"),
                ],
            },
        },
        cb
    );

    cb(start, end);

    // INITIALIZATION OF CLIPBOARD
    // =======================================================
    $(".js-clipboard").each(function () {
        var clipboard = $.HSCore.components.HSClipboard.init(this);
    });

    // --- datapicker width control --
    const $dateRangeBtn = $('.dateRange');
    $dateRangeBtn.on('show.daterangepicker', function (ev, picker) {
        const buttonWidth = $(this).outerWidth();
        if (!picker.container.hasClass('show-calendar')) {
            picker.container.find('.ranges ul').css('width', buttonWidth + 'px');
        }
        picker.container.find('.ranges li').on('click', function () {
            setTimeout(() => {
                if (picker.container.hasClass('show-calendar')) {
                    picker.container.find('.ranges ul').css('width', '');
                }
            }, 10);
        });
    });
});

("use strict");
$(".form-alert").on("click", function () {
    let id = $(this).data("id");
    let message = $(this).data("message");
    form_alert(id, message);
});
function form_alert(id, message) {
    Swal.fire({
        title: "Are you sure?",
        text: message,
        type: "warning",
        showCancelButton: true,
        cancelButtonColor: "default",
        confirmButtonColor: "#161853",
        cancelButtonText: "No",
        confirmButtonText: "Yes",
        reverseButtons: true,
    }).then((result) => {
        if (result.value) {
            $("#" + id).submit();
        }
    });
}

function playAudio() {
    audio.play();
}

function pauseAudio() {
    audio.pause();
}

("use strict");
var audio = document.getElementById("myAudio");

function form_alert(id, message) {
    Swal.fire({
        title: "Are you sure?",
        text: message,
        type: "warning",
        showCancelButton: true,
        cancelButtonColor: "default",
        confirmButtonColor: "#377dff",
        cancelButtonText: "No",
        confirmButtonText: "Yes",
        reverseButtons: true,
    }).then((result) => {
        if (result.value) {
            $("#" + id).submit();
        }
    });
}

function call_demo() {
    toastr.info("Update option is disabled for demo!", {
        CloseButton: true,
        ProgressBar: true,
    });
}

$(".change-status").on("click", function () {
    window.location.href = $(this).data("route");
});

// ---- svg conversion
function replaceSvgImages() {
    $('img.svg').each(function () {
        let $img = $(this);
        let imgURL = $img.attr('src');

        $.get(imgURL, function (data) {
            let $svg = $(data).find('svg');

            if ($img.attr('class')) {
                $svg.attr('class', $img.attr('class'));
            }
            $svg.removeAttr('xmlns:a');

            $img.replaceWith($svg);
        }, 'xml');
    });
}
replaceSvgImages();

// --- table column show/hide
 $(document).on('change', '.toggle-switch-input', function () {
    const switchId = $(this).attr('id');

        if (switchId && switchId.startsWith('toggleColumn_')) {
            const columnClass = switchId.replace('toggleColumn_', '');
            const isChecked = $(this).is(':checked');

            $(`th[data-column="${columnClass}"], td[data-column="${columnClass}"]`).toggle(isChecked);
        }
    });
// --- table column show/hide ends
