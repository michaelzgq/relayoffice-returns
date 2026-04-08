"use strict";

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#viewer').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$("#customFileEg1").on('change', function () {
    readURL(this);
});

"use strict";

$(".update-customer-balance").on('click', function () {
    let val = $(this).data('id');
    update_customer_balance_cl(val);
});

function update_customer_balance_cl(val) {
    $("#customer_id").val(val);
}

$(".print-div").on('click', function () {
    let divName = $(this).data('name');
    let title = $(this).data('title');
    printDiv(divName, title);
});

function printDiv(divName, title) {
    var originalTitle = document.title; // Save the original title
    document.title = title || 'Order Invoice';
    if ($('html').attr('dir') === 'rtl') {
        $('html').attr('dir', 'ltr')
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        $('.width-inone').attr('dir', 'rtl')
        window.print();
        document.body.innerHTML = originalContents;
        $('html').attr('dir', 'rtl')
        location.reload()
    } else {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload()
    }
}

"use strict";
$(document).on('ready', function () {
    // INITIALIZATION OF SELECT2
    // =======================================================
    $('.js-select2-custom').each(function () {
        var select2 = $.HSCore.components.HSSelect2.init($(this));
    });
});

function getRndInteger() {
    return Math.floor(Math.random() * 90000) + 10000;
}

function discount_option(val) {
    if ($(val).val() == 'percent') {
        $("#percent").removeClass('d-none').show();
        $("#amount").hide();
    }
    if ($(val).val() == 'amount') {
        $("#amount").removeClass('d-none').show();
        $("#percent").hide();
    }
}

"use strict";

function getRequest(route, id) {
    $.get({
        url: route,
        dataType: 'json',
        success: function (data) {
            $('#' + id).empty().append(data.options);
        },
    });
}

"use strict";

function update_quantity_plst(val) {
    $("#product_id").val(val);
    console.log(val);
}


"use strict";

function update_quantity_sto(val) {
    $("#product_id").val(val);
    console.log(val);
}

/*supplier transaction list*/
"use strict";
$("#start_date").on('change', function () {
    let start = $('#start_date').val();
    if (start) {
        $('#end_date').attr('min', $(this).val());
        $('#end_date').attr('required', true);
        //console.log("jkfjkf");
    }

});
$("#end_date").on("change", function () {
    $('#start_date').attr('max', $(this).val());
});

function add_new_purchase(val) {
    $("#supplier_id").val(val);
    //console.log(val);
}

function due_calculate() {
    let purchase_amount = $('#purchased_amount').val();
    let paid_amount = $('#paid_amount').val();
    let due_amount = parseInt(purchase_amount) - parseInt(paid_amount);
    $('#due_amount').val(due_amount);
    //console.log(purchase_amount);
    $('#paid_amount').attr('max', purchase_amount);
}

function payment_due(val) {
    $("#due_pay_supplier_id").val(val);
}

"use strict";

function due_remain() {
    let total_due_amount = $('#total_due_amount').val();
    let pay_amount = $('#pay_amount').val();
    let remain_due = parseInt(total_due_amount) - parseInt(pay_amount);
    $('#remaining_due_amount').val(remain_due);
}


"use strict";

function accountChangeTr(val) {
    let hide_id = val;
    $('.account').show();
    $('#account_to_id').removeClass('d-none');
    $("#account_to_id option[value='" + hide_id + "']").hide();
    console.log(val);
}


"use strict";
// INITIALIZATION OF CHARTJS
// =======================================================
if (typeof Chart !== 'undefined' && typeof ChartDataLabels !== 'undefined') {
    Chart.plugins.unregister(ChartDataLabels);
}

if ($('.js-chart').length) {
    $('.js-chart').each(function () {
        $.HSCore.components.HSChartJS.init($(this));
    });
}


"use strict";
$("#lastMonthStatistic").hide();

function chart_statistic(val) {
    let chart = val;
    if (val == 'monthly') {
        $("#lastYearStatistic").hide();
        $("#lastMonthStatistic").show();
    } else {
        $("#lastMonthStatistic").hide();
        $("#lastYearStatistic").show();
    }
}


"use strict";

function update_quantity(val) {
    $("#product_id").val(val);
}

"use strict";

function exportList(element) {
    const exportType = element.id;
    let visibleColumns = [];
    $('.update-column-visibility:checked').each(function () {
        visibleColumns.push($(this).attr('id').replace('toggleColumn_', ''));
    });

    // Get current filter parameters
    let currentUrl = new URL(window.location.href);
    let params = new URLSearchParams(currentUrl.search);

    // Add visible columns to parameters
    params.set('columns', visibleColumns.join(','));
    params.set('export_type', exportType);

    // Trigger download
    window.location.href = $('.data-to-js').data('export-route') + '?' + params.toString();
}

const pageTitle = $('.data-to-js').data('title');
$('.update-column-visibility').on('change', function () {
    let visibilityState = {};
    $('.update-column-visibility').each(function () {
        let columnId = $(this).attr('id');
        visibilityState[columnId] = $(this).prop('checked');
    });
    localStorage.setItem(`${pageTitle}-column-visibility`, JSON.stringify(visibilityState));
});
$(document).ready(function () {
    let savedState = localStorage.getItem(`${pageTitle}-column-visibility`);
    if (savedState) {
        let visibilityState = JSON.parse(savedState);
        for (let columnId in visibilityState) {
            let columnClass = columnId.replace('toggleColumn_', '');
            let isVisible = visibilityState[columnId];
            $(`#${columnId}`).prop('checked', isVisible);
            $(`th[data-column="${columnClass}"], td[data-column="${columnClass}"]`).toggle(isVisible);
        }
    }
});

function printFilterCount(excludedParams = []) {
    let currentUrl = new URL(window.location.href);
    let params = currentUrl.searchParams;
    let filteredParams = Array.from(params.keys()).filter(param =>
        !excludedParams.includes(param) && params.get(param) && params.get(param) !== 'all'
    );
    let hasPriceRange = filteredParams.includes('min_price') && filteredParams.includes('max_price');
    let hasDateRange = filteredParams.includes('start_date') && filteredParams.includes('end_date');
    if (hasPriceRange) {
        filteredParams = filteredParams.filter(param => param !== 'min_price' && param !== 'max_price');
        filteredParams.push('price_range');
    }
    if (hasDateRange) {
        filteredParams = filteredParams.filter(param => param !== 'start_date' && param !== 'end_date');
        filteredParams.push('date_range');
    }

    let paramCount = filteredParams.length;
    if (paramCount > 0) {
        $('.show-filter-count').append('<span class="position-absolute badge badge-primary filter-count">' + paramCount + '</span>');
    } else {
        $('.show-filter-count').find('.filter-count').remove();
    }
}

function restoreOverlayAfterModalClose() {
    $('#globalChangeStatusModal, #deleteModalWithShift, #deleteModal, #update-quantity').on('hidden.bs.modal', function () {
        const $openOffcanvas = $('.offcanvas-filter.open');
        if ($openOffcanvas.length) {
            const overlaySelector = $openOffcanvas.data('overlay');
            $(overlaySelector).addClass('active');
            $('#offcanvasView .edit-resource').removeClass('disabled');
        }
    });
}

function handleModalBackdrop(modalSelector, editButtonSelector) {
    $(modalSelector).on('click', function () {
        $('.overlay').removeClass('active');
        setTimeout(function () {
            if ($('.modal-backdrop').length > 0 && $('.offcanvas-filter.open').length > 0) {
                $(editButtonSelector).addClass('disabled');
            }
        }, 1);
    });
}

function initializeCanvasAjax(triggerSelector, url, canvasSelector, callback, initSelect2 = false) {
    $(document).on('click', triggerSelector, function () {
        $(canvasSelector).empty();
        const id = $(this).data('id');
        $.ajax({
            url: url,
            type: 'GET',
            data: {id},
            success: function (data) {
                $(canvasSelector).html(data);
                // initialize tooltips
                $('[data-toggle="tooltip"]').tooltip();
                if (typeof callback === 'function') callback();
                if (initSelect2) {
                    $('.js-select2-custom').each(function () {
                        $.HSCore.components.HSSelect2.init($(this));
                    });
                }
            },
            error: function (xhr) {
                console.error(xhr);
            }
        });
    });
}

function initializeModalWithAjax(triggerSelector, fetchUrl, modalId, initSelect2 = false, getChildren = false) {
    $(document).on('click', triggerSelector, function () {
        const id = $(this).data('id');
        $.ajax({
            url: fetchUrl,
            type: 'GET',
            data: {id},
            success: function (data) {
                $(modalId).remove();
                $('body').append(data);
                const $modal = $(modalId);
                $modal.modal('show');

                $modal.on('hidden.bs.modal', function () {
                    $(this).remove();
                });

                if (initSelect2) {
                    $.HSCore.components.HSSelect2.init($('.js-select2-custom'));
                }

                restoreOverlayAfterModalClose();

                setTimeout(function () {
                    if ($('.modal-backdrop').length > 0 && $('.offcanvas-filter.open').length > 0) {
                        $('#offcanvasView .edit-resource').addClass('disabled');
                    }
                }, 1);

                if (getChildren) {
                    getSubcategories(id);
                    $(document)
                        .off('change', '#resource-select')
                        .on('change', '#resource-select', function () {
                            getSubcategories(id);
                        });
                }
            },
        });
    });
}

$(document).on('click', '.global-change-status', function (e) {
    e.preventDefault();
    const $this = $(this);

    $('#global-status-change-title').text($this.data('title'));
    $('#global-status-change-description').text($this.data('description'));
    $('#global-status-change-image').attr('src', $this.data('image'));
    const modalTarget = $this.data('target');
    $(modalTarget).modal('show');
    $(`${modalTarget} form`).attr('action', $this.data('route'));
});

$(document).on('click', '.delete-resource', function () {
    const id = $(this).data('id');
    const route = $(`.data-to-js`).data('delete-route').replace(':id', id);
    $('#deleteModal form').attr('action', route);
    if ($(this).data('title')) {
        $('#deleteModal').find('.title').text($(this).data('title'));
    }

    if ($(this).data('subtitle')) {
        $('#deleteModal').find('.subtitle').text($(this).data('subtitle'));
    }

    if ($(this).data('image')) {
        $('#deleteModal').find('.delete-image').attr('src', $(this).data('image'));
    }

    if ($(this).data('cancel-text')) {
        $('#deleteModal').find('.cancel-text').text($(this).data('cancel-text'));
    }

    if ($(this).data('confirm-text')) {
        $('#deleteModal').find('.confirm-text').text($(this).data('confirm-text'));
    }

    if ($(this).data('change-order') == 1) {
        $('#deleteModal').find('.cancel-text').addClass('order-2');
        $('#deleteModal').find('.confirm-text').addClass('order-1');
    }

});

// --- Timepicker initialize
$(document).ready(function () {
    $('.timePicker').each(function () {
        const $input = $(this);
        $input.attr('placeholder', '-- : -- --');
        $input.daterangepicker({
            singleDatePicker: true,
            timePicker: true,
            timePicker24Hour: false,
            timePickerSeconds: false,
            autoUpdateInput: false,
            locale: {
                format: 'hh : mm A'
            },
            opens: 'center',
            drops: 'down'
        });
        $input.on('apply.daterangepicker', function(ev, picker) {
            const formattedTime = picker.startDate.format('hh : mm A');
            $(this).val(formattedTime);
        });
        $input.on('show.daterangepicker', function (ev, picker) {
            picker.container.find('.calendar-table').hide();
            picker.container.find('.calendar-time').css('visibility', 'visible');
        });
    });
    // Trigger input focus when clicking on the icon
    $('.time-picker-icon').on('click', function () {
        $(this).siblings('.timePicker').trigger('focus');
    });
});

