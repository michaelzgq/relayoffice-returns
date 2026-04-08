let isTransitioning = false;

$(document).on('click', '.offcanvas-toggle', function () {
    if (isTransitioning) return;
    isTransitioning = true;

    let target = $(this).data('target');
    let $newOffcanvas = $(target);
    let newOverlay = $newOffcanvas.data('overlay');

    let $currentOffcanvas = $('.offcanvas-filter.open');
    if ($currentOffcanvas.length) {
        let currentOverlay = $currentOffcanvas.data('overlay');
        $currentOffcanvas.removeClass('open');
        $(currentOverlay).removeClass('active');

        setTimeout(function () {
            $newOffcanvas.addClass('open');
            $(newOverlay).addClass('active');
            $('body').addClass('modal-open');
            isTransitioning = false;
        }, 300);
    } else {
        $newOffcanvas.addClass('open');
        $(newOverlay).addClass('active');
        $('body').addClass('modal-open');
        isTransitioning = false;
    }
});



$(document).on('click', '.overlay', function () {
    let overlayId = '#' + $(this).attr('id');
    let $offcanvas = $('.offcanvas-filter[data-overlay="' + overlayId + '"]');
    $offcanvas.removeClass('open');
    $(this).removeClass('active');
    $('body').removeClass('modal-open');
});

$(document).on('click', '.closeOfcanvus', function () {
    let $offcanvas = $(this).closest('.offcanvas-filter');
    let overlaySelector = $offcanvas.data('overlay');
    $offcanvas.removeClass('open');
    $(overlaySelector).removeClass('active');
    $('body').removeClass('modal-open');
});

$(document).on('keydown', function (e) {
    if (e.key === 'Escape' || e.keyCode === 27) {
        let $openOffcanvas = $('.offcanvas-filter.open');
        if ($openOffcanvas.length) {
            let overlaySelector = $openOffcanvas.data('overlay');
            $openOffcanvas.removeClass('open');
            $(overlaySelector).removeClass('active');
            $('body').removeClass('modal-open');
        }
    }
});