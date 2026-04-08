"use strict"
function getCurrentFilters() {
    let picker = $('.dateRange').data('daterangepicker');
    let dateText = $('.dateRange span').text();
    let startDate = null;
    let endDate = null;

    if (dateText !== $('.data-to-js').data('date-placeholder')) {
        startDate = picker.startDate.format('YYYY-MM-DD');
        endDate = picker.endDate.format('YYYY-MM-DD');
    }

    // Get payment method IDs as array
    let paymentMethodIds = [];
    $('#offcanvasFilterMenu .category-checkbox:checked').each(function() {
        paymentMethodIds.push($(this).val());
    });

    return {
        startDate: startDate,
        endDate: endDate,
        search: $('.search-bar-input').val() || null,
        paymentMethodId: paymentMethodIds,
        counterId: $('.change-counter').val() || 'all',
        customerId: $('.customer-change').val() || 'all'
    };
}

$(function () {
    // Clear date parameters from URL on initial load if no dates are selected
    // Preserve date parameters if they exist in the URL
    let currentUrl = new URL(window.location.href);
    let startDateFromUrl = currentUrl.searchParams.get('start_date');
    let endDateFromUrl = currentUrl.searchParams.get('end_date');

    // Convert the URL dates into moment objects (if they exist)
    let startDate = startDateFromUrl ? moment(startDateFromUrl, 'YYYY-MM-DD') : null;
    let endDate = endDateFromUrl ? moment(endDateFromUrl, 'YYYY-MM-DD') : null;

    if (!startDate || !endDate) {
        currentUrl.searchParams.delete('start_date');
        currentUrl.searchParams.delete('end_date');
        window.history.pushState({}, '', currentUrl.toString());

        $('.dateRange span').html($('.data-to-js').data('date-placeholder'));
    } else {
        $('.dateRange span').html(startDate.format('DD MMM, YYYY') + ' - ' + endDate.format('DD MMM, YYYY'));
    }

    if (startDateFromUrl && endDateFromUrl) {
        $('.dateRange span').html(moment(startDateFromUrl).format('DD MMM, YYYY') + ' - ' + moment(endDateFromUrl).format('DD MMM, YYYY'))
    }



    $('.dateRange').daterangepicker({
        autoUpdateInput: false,
        startDate: startDate || undefined,
        endDate: endDate || undefined,
        locale: {
            cancelLabel: 'Clear'
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    $('.dateRange').on('apply.daterangepicker', function(ev, picker) {
        $(this).find('span').html(picker.startDate.format('DD MMM, YYYY') + ' - ' + picker.endDate.format('DD MMM, YYYY'));

        // Reset page parameter and update URL
        let currentUrl = new URL(window.location.href);
        currentUrl.searchParams.delete('page');
        currentUrl.searchParams.set('start_date', picker.startDate.format('YYYY-MM-DD'));
        currentUrl.searchParams.set('end_date', picker.endDate.format('YYYY-MM-DD'));
        window.history.pushState({}, '', currentUrl.toString());

        showLoader();
        let filters = getCurrentFilters();
        searchOrderDetails(
            picker.startDate.format('YYYY-MM-DD'),
            picker.endDate.format('YYYY-MM-DD'),
            filters.search,
            $('.data-to-js').data('type'),
            filters.paymentMethodId,
            filters.counterId,
            filters.customerId
        );
    });

    $('.dateRange').on('cancel.daterangepicker', function(ev, picker) {
        $(this).find('span').html($('.data-to-js').data('date-placeholder'));

        // Reset page parameter and remove date parameters from URL
        let currentUrl = new URL(window.location.href);
        currentUrl.searchParams.delete('page');
        currentUrl.searchParams.delete('start_date');
        currentUrl.searchParams.delete('end_date');
        window.history.pushState({}, '', currentUrl.toString());

        showLoader();
        let filters = getCurrentFilters();
        searchOrderDetails(
            null,
            null,
            filters.search,
            $('.data-to-js').data('type'),
            filters.paymentMethodId,
            filters.counterId,
            filters.customerId
        );
    });
});

$(function () {
    /*=====================
        Changing svg color
        ========================*/
    $("img.svg").each(function () {
        var $img = jQuery(this);
        var imgID = $img.attr("id");
        var imgClass = $img.attr("class");
        var imgURL = $img.attr("src");

        jQuery.get(
            imgURL,
            function (data) {
                // Get the SVG tag, ignore the rest
                var $svg = jQuery(data).find("svg");

                // Add replaced image's ID to the new SVG
                if (typeof imgID !== "undefined") {
                    $svg = $svg.attr("id", imgID);
                }
                // Add replaced image's classes to the new SVG
                if (typeof imgClass !== "undefined") {
                    $svg = $svg.attr("class", imgClass + " replaced-svg");
                }

                // Remove any invalid XML tags as per http://validator.w3.org
                $svg = $svg.removeAttr("xmlns:a");

                // Check if the viewport is set, else we gonna set it if we can.
                if (
                    !$svg.attr("viewBox") &&
                    $svg.attr("height") &&
                    $svg.attr("width")
                ) {
                    $svg.attr(
                        "viewBox",
                        "0 0 " + $svg.attr("height") + " " + $svg.attr("width")
                    );
                }

                // Replace image with new SVG
                $img.replaceWith($svg);
            },
            "xml"
        );
    });
});


$(document).ready(function () {
    $('#viewDetailsDropdown').on('click', function (e) {
        // Prevent dropdown from closing immediately on click
        e.stopPropagation();
        $(this).next('.dropdown-menu').toggle();
    });
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').hide();
        }
    });
});

$(document).ready(function () {
    $('.js-select2-custom').each(function () {
        $(this).select2({
            dropdownParent: $(this).closest('.offcanvas-filter'),
        });
    });
});

// Function to sync filter menu fields with URL parameters
function syncFilterMenuFields() {
    let currentUrl = new URL(window.location.href);

    // Sync customer dropdown
    let customerId = currentUrl.searchParams.get('customer_id');
    $('#offcanvasFilterMenu .customer-change').val(customerId || 'all').trigger('change');

    // Sync counter dropdown
    let counterId = currentUrl.searchParams.get('counter_id');
    if (!counterId && customerId === 'all') {
        counterId = $('#offcanvasFilterMenu .change-counter option:first').val();
    }
    $('#offcanvasFilterMenu .change-counter').val(counterId || 'all').trigger('change');

    // Sync payment method checkboxes
    let paymentMethods = currentUrl.searchParams.getAll('payment_method_id[]');
    $('#offcanvasFilterMenu .category-checkbox').prop('checked', false);
    if (paymentMethods && paymentMethods.length > 0) {
        paymentMethods.forEach(function(methodId) {
            $('#offcanvasFilterMenu .category-checkbox[value="' + methodId + '"]').prop('checked', true);
        });
    }

    // Reinitialize select2 if it exists
    if(typeof $.fn.select2 === 'function') {
        $('#offcanvasFilterMenu .js-select2-custom').select2({
            dropdownParent: $('#offcanvasFilterMenu')
        });
    }
}

// Add click handler for filter menu toggle
$('#filterMenuToggle').on('click', function () {
    syncFilterMenuFields();
    $('#offcanvasFilterMenu').toggleClass('open');
    $('#overlay').toggleClass('active');
});

// Also sync when clicking the filter button (if it exists)
$('.filter-btn').on('click', function () {
    syncFilterMenuFields();
});

// Close menus when clicking the close button
$('.closeOfcanvus').on('click', function () {
    $(this).closest('.offcanvas-filter').removeClass('open');
    $('#overlay').removeClass('active');
});

// Additional cancel buttons
document.getElementById('cancel_filter')?.addEventListener('click', function () {
    $('#offcanvasFilterMenu').removeClass('open');
    $('#overlay').removeClass('active');
});

document.getElementById('cancel_hold')?.addEventListener('click', function () {
    $('#offcanvasOrderItemsMenu').removeClass('open');
    $('#overlay').removeClass('active');
});

$(document).ready(function () {
    // --- table column show/hide
    $('.toggle-switch-input').on('change', function () {
        const columnClass = $(this).attr('id').replace('toggleColumn_', '');
        const isChecked = $(this).is(':checked');

        $(`th[data-column="${columnClass}"], td[data-column="${columnClass}"]`).toggle(isChecked);
    });
    // --- table column show/hide ends

    $('.search-bar-input').on('input', function () {
        let value = $(this).val();
        let filters = getCurrentFilters();
        searchOrderDetails(
            filters.startDate,
            filters.endDate,
            value,
            $('.data-to-js').data('type'),
            filters.paymentMethodId,
            filters.counterId,
            filters.customerId
        );
    });

    $('.btn-apply').on('click', function (e) {
        e.preventDefault();
        showLoader();

        let filters = getCurrentFilters();

        searchOrderDetails(
            filters.startDate,
            filters.endDate,
            filters.search,
            $('.data-to-js').data('type'),
            filters.paymentMethodId,
            filters.counterId,
            filters.customerId
        );

        printFilterCount(['type', 'search', 'start_date', 'end_date', 'page']);
        // Close the filter menu
        $('#offcanvasFilterMenu').removeClass('open');
        $('#overlay').removeClass('active');
    });
});

// Update the pagination click handler to properly get all current filters including search
$(document).on('click', '.page-area a', function(e) {
    e.preventDefault();
    let url = $(this).attr('href');
    if (url) {
        showLoader();
        let page = url.split('page=')[1] || 1;
        let filters = getCurrentFilters();

        let currentUrl = window.location.href;
        currentUrl = updateQueryStringParameter(currentUrl, 'page', page);
        window.history.pushState({}, '', currentUrl);

        searchOrderDetails(
            filters.startDate,
            filters.endDate,
            filters.search,
            $('.data-to-js').data('type'),
            filters.paymentMethodId,
            filters.counterId,
            filters.customerId
        );
        $(window).scrollTop(0);
    }
});

// Helper function to update URL parameters
function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
        return uri.replace(re, '$1' + key + "=" + value + '$2');
    }
    else {
        return uri + separator + key + "=" + value;
    }
}

function showLoader() {
    $('.loading-overlay').css('display', 'flex');
}

function hideLoader() {
    $('.loading-overlay').css('display', 'none');
}

// Modify searchOrderDetails success callback
function searchOrderDetails(startDate, endDate, search, type, paymentMethodIds, counterId, customerId) {
    showLoader();

    // Update URL parameters
    let currentUrl = new URL(window.location.href);

    // Update or remove date parameters
    if (startDate && endDate) {
        currentUrl.searchParams.set('start_date', startDate);
        currentUrl.searchParams.set('end_date', endDate);
    } else {
        currentUrl.searchParams.delete('start_date');
        currentUrl.searchParams.delete('end_date');
    }

    // Update other parameters
    if (search) {
        currentUrl.searchParams.set('search', search);
    } else {
        currentUrl.searchParams.delete('search');
    }

    currentUrl.searchParams.set('type', type);
    if(counterId !== null){
        currentUrl.searchParams.set('counter_id', counterId);
    }
    if(customerId !== null){
        currentUrl.searchParams.set('customer_id', customerId);
    }

    // Update payment method IDs
    currentUrl.searchParams.delete('payment_method_id[]');
    if (Array.isArray(paymentMethodIds) && paymentMethodIds.length > 0) {
        paymentMethodIds.forEach(id => {
            currentUrl.searchParams.append('payment_method_id[]', id);
        });
    }

    // Get page from URL or default to 1
    let page = currentUrl.searchParams.get('page') || 1;

    // Update URL
    window.history.pushState({}, '', currentUrl.toString());

    // Store current column visibility state
    let visibilityState = {};
    $('.toggle-switch-input').each(function() {
        let columnId = $(this).attr('id');
        let columnClass = columnId.replace('toggleColumn_', '');
        visibilityState[columnClass] = $(this).prop('checked');
    });

    // Create request data object
    let requestData = {
        start_date: startDate || null,
        end_date: endDate || null,
        search: search || null,
        type: type,
        counter_id: counterId || 'all',
        customer_id: customerId || 'all',
        page: page
    };

    // Add payment method IDs to request
    if (Array.isArray(paymentMethodIds) && paymentMethodIds.length > 0) {
        requestData['payment_method_id[]'] = paymentMethodIds;
    }

    $.ajax({
        url: $('.data-to-js').data('search-route'),
        type: 'GET',
        data: requestData,
        traditional: true,
        success: function (response) {
            $('.list-table-data').empty().html(response.view);
            $('.count-order-details').empty().html(response.count);

            // Reapply column visibility after content update
            for (let columnClass in visibilityState) {
                let isVisible = visibilityState[columnClass];
                $(`th[data-column="${columnClass}"], td[data-column="${columnClass}"]`).toggle(isVisible);
            }

            hideLoader();
            $('.download-invoice').on('click', function () {
                let url = $(this).data('url');
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (response) {
                        closeAllOffcanvas();
                        $('#print-invoice').addClass('open');
                        $('#overlay').addClass('active');
                        toggleBodyScroll();
                        $('#printableArea').empty().html(response.view);
                        $(".print-div").on('click', function(){
                            let divName = $(this).data('name');
                            let title = $(this).data('title');
                            printDiv(divName, title);
                        });
                    },
                    error: function (xhr) {
                        console.log('Error:', xhr);
                    }
                })
            });


            $(document).on('click', '.close-print-invoice', function () {
                $('#print-invoice').removeClass('open');
                $('#overlay').removeClass('active');
                toggleBodyScroll();
            });

            $('.order-items').on('click', function () {
                let url = $(this).data('url');
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (response) {
                        $('.show-order-items-menu').empty().html(response);
                        $('#offcanvasOrderItemsMenu').toggleClass('open');
                        $('#overlay').toggleClass('active');
                        $('.closeOfcanvus').on('click', function () {
                            $(this).closest('.offcanvas-filter').removeClass('open');
                            $('#overlay').removeClass('active');
                        });
                        $('#overlay').on('click', function () {
                            closeAllOffcanvas();
                            $(this).removeClass('active');
                            toggleBodyScroll();
                        });

                    },
                    error: function(xhr) {
                        console.log('Error:', xhr);
                    }
                })
            })

            replaceSvgImages();
        },
        error: function(xhr) {
            console.log('Error:', xhr);
            hideLoader();
        }
    });
}

// Handle pagination clicks


// Reset page parameter when applying new filters or search
$('.search-bar-input').on('input', function () {
    let currentUrl = new URL(window.location.href);
    currentUrl.searchParams.delete('page');
    window.history.pushState({}, '', currentUrl.toString());

    let filters = getCurrentFilters();
    searchOrderDetails(
        filters.startDate,
        filters.endDate,
        $(this).val(),
        $('.data-to-js').data('type'),
        filters.paymentMethodId,
        filters.counterId,
        filters.customerId
    );
});

// Reset page parameter when applying filters
$('.btn-apply').on('click', function (e) {
    e.preventDefault();

    let currentUrl = new URL(window.location.href);
    currentUrl.searchParams.delete('page');
    window.history.pushState({}, '', currentUrl.toString());

    let filters = getCurrentFilters();
    searchOrderDetails(
        filters.startDate,
        filters.endDate,
        filters.search,
        $('.data-to-js').data('type'),
        filters.paymentMethodId,
        filters.counterId,
        filters.customerId
    );

    $('#offcanvasFilterMenu').removeClass('open');
    $('#overlay').removeClass('active');
});

// Helper function to get URL parameters safely
function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

// Initialize column visibility on page load
$(document).ready(function() {
    // Apply initial visibility state from localStorage if exists
    let savedState = localStorage.getItem('orderColumnVisibility');
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

// Store visibility state when toggled
$('.toggle-switch-input').on('change', function() {
    let visibilityState = {};
    $('.toggle-switch-input').each(function() {
        let columnId = $(this).attr('id');
        visibilityState[columnId] = $(this).prop('checked');
    });
    localStorage.setItem('orderColumnVisibility', JSON.stringify(visibilityState));
});

$('.btn-clear-filter').on('click', function (e) {
    e.preventDefault();
    showLoader();

    // Reset all filter fields in the menu
    $('#offcanvasFilterMenu .customer-change').val('all').trigger('change');
    $('#offcanvasFilterMenu .change-counter').val('all').trigger('change');
    $('#offcanvasFilterMenu .category-checkbox').prop('checked', false);

    // Reset search input and trigger change event
    $('.search-bar-input').val('').trigger('input');

    // Clear URL parameters but keep type
    let currentUrl = new URL(window.location.href);
    let type = currentUrl.searchParams.get('type');
    currentUrl.search = '';
    if (type) {
        currentUrl.searchParams.set('type', type);
    }

    // Check if date was actually selected (not the placeholder text)
    let dateText = $('.dateRange span').text();
    let isDateSelected = dateText !== $('.data-to-js').data('date-placeholder');

    // Only keep date parameters if dates were explicitly selected
    if (isDateSelected) {
        let daterangepicker = $('.dateRange').data('daterangepicker');
        currentUrl.searchParams.set('start_date', daterangepicker.startDate.format('YYYY-MM-DD'));
        currentUrl.searchParams.set('end_date', daterangepicker.endDate.format('YYYY-MM-DD'));
    }

    window.history.pushState({}, '', currentUrl.toString());

    // Fetch data with cleared filters
    setTimeout(() => {
        searchOrderDetails(
            isDateSelected ? daterangepicker.startDate.format('YYYY-MM-DD') : null,
            isDateSelected ? daterangepicker.endDate.format('YYYY-MM-DD') : null,
            '',
            type || $('.data-to-js').data('type'),
            [],
            null,
            null
        );
    }, 100);
    printFilterCount(['type', 'search', 'start_date', 'end_date', 'page']);

    // Close the filter menu
    $('#offcanvasFilterMenu').removeClass('open');
    $('#overlay').removeClass('active');

    // Sync all fields with URL
    syncFilterMenuFields();
    toggleBodyScroll();
});

function exportOrders() {
    // Get all visible columns
    let visibleColumns = [];
    $('.toggle-switch-input:checked').each(function() {
        visibleColumns.push($(this).attr('id').replace('toggleColumn_', ''));
    });

    // Get current filter parameters
    let currentUrl = new URL(window.location.href);
    let params = new URLSearchParams(currentUrl.search);

    // Add visible columns to parameters
    params.set('columns', visibleColumns.join(','));

    // Trigger download
    window.location.href = $('.data-to-js').data('export-route') + '?' + params.toString();
}

$('.download-invoice').on('click', function () {
    let url = $(this).data('url');
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            closeAllOffcanvas();
            $('#print-invoice').addClass('open');
            $('#overlay').addClass('active');
            toggleBodyScroll();
            $('#printableArea').empty().html(response.view);
            $(".print-div").on('click', function(){
                let divName = $(this).data('name');
                let title = $(this).data('title');
                printDiv(divName, title);
            });
        },
        error: function (xhr) {
            console.log('Error:', xhr);
        }
    })
});


$(document).on('click', '.close-print-invoice', function () {
    $('#print-invoice').removeClass('open');
    $('#overlay').removeClass('active');
    toggleBodyScroll();
});

function closeAllOffcanvas() {
    $('#offcanvasFilterMenu, #print-invoice').removeClass('open');
}
$('#overlay').on('click', function () {
    closeAllOffcanvas();
    $(this).removeClass('active');
    toggleBodyScroll();
});

function toggleBodyScroll() {
    const isAnyOpen = $('#offcanvasFilterMenu').hasClass('open') ||
        $('#offcanvasHoldMenu').hasClass('open') ||
        $('#print-invoice').hasClass('open');

    $('body').toggleClass('modal-open', isAnyOpen);
}
$(document).ready(function () {
    $('.submit-refund-form').on('click', function (e) {
        e.preventDefault();
        let url = $(this).closest('form').attr('action');
        let data = $(this).closest('form').serializeArray();
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                toastr.success(response.message);
                setTimeout(() => {
                    location.reload();
                }, 1500);

            },
            error: function (xhr) {
                for (let key in xhr.responseJSON.errors) {
                    toastr.error(xhr.responseJSON.errors[key][0]);
                }
            }
        });
    });

    $('.character-count-field').each(function () {
        initialCharacterCount($(this));
    });

    $('.character-count-field').on('keyup change', function () {
        initialCharacterCount($(this));
    });

    function initialCharacterCount(item) {
        let str = item.val();
        let maxCharacterCount = item.data('max-character');
        let characterCount = str.length;
        if (characterCount > maxCharacterCount) {
            item.val(str.substring(0, maxCharacterCount));
            characterCount = maxCharacterCount;
        }
        item.closest('.character-count').find('p').text(characterCount + '/' + maxCharacterCount);
    }
});


$('.order-items').on('click', function () {
    let url = $(this).data('url');
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            $('.show-order-items-menu').empty().html(response);
            $('#offcanvasOrderItemsMenu').toggleClass('open');
            $('#overlay').toggleClass('active');
            $('.closeOfcanvus').on('click', function () {
                $(this).closest('.offcanvas-filter').removeClass('open');
                $('#overlay').removeClass('active');
            });
            $('#overlay').on('click', function () {
                $('#offcanvasOrderItemsMenu').removeClass('open');
                $('#overlay').removeClass('active');
            });

        },
        error: function(xhr) {
            console.log('Error:', xhr);
        }
    })
})

$('#OrderItemsMenuToggle').on('click', function() {
    $('#offcanvasOrderItemsMenu').toggleClass('open');
    $('#overlay').toggleClass('active');
});
