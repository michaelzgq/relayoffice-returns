@if ($errors->any())
    <script>
        "use strict";

        @foreach($errors->all() as $error)
        toastr.error('{{$error}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif

<script>
    "use strict";

    function fullScreen() {
        var elem = document.documentElement;
        if (!document.fullscreenElement) {
            elem.requestFullscreen().catch(err => {
                alert(`Error attempting to enable full-screen mode: ${err.message} (${err.name})`);
            });
        } else {
            document.exitFullscreen();
        }
    }

    $(document).on('click', function (e) {
        if ($(e.target).closest('.input-group-custom').length) {
            $('.search-result-box').css('display', 'block');
        } else if (!$(e.target).closest('.search-result-box').length) {
            $('.search-result-box').css('display', 'none');
        }
    });

    function handleDecrementButtonDisabled() {
        $('.counter-input').each(function () {
            if ($(this).val() <= 1) {
                $(this).siblings('.btn-decrement').attr('disabled', true);
            } else {
                $(this).siblings('.btn-decrement').attr('disabled', false);
            }
        })
    }

    $(document).ready(function () {
        handleDecrementButtonDisabled();
    })

    $(".empty-cart").on('click', function () {
        emptyCart();
    });

    $('.fullscreen-button').on('click', fullScreen)

    $(document).on('click', '#logoutLink', function (e) {
        e.preventDefault();

        Swal.fire({
            title: '{{\App\CPU\translate('Do you want to logout')}}?',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonColor: '#FC6A57',
            cancelButtonColor: '#363636',
            confirmButtonText: `{{\App\CPU\translate('Yes')}}`,
            denyButtonText: `{{\App\CPU\translate('Don\'t Logout')}}'`,
        }).then((result) => {
            if (result.value) {
                window.location.href = '{{route('admin.auth.logout')}}';
            } else {
                Swal.fire('{{\App\CPU\translate('Canceled')}}', '', '{{\App\CPU\translate('info')}}');
            }
        });
    });

    $(document).on('ready', function () {

        $(".print-div").on('click', function () {
            let divName = $(this).data('name');
            printDiv(divName);
        });

        $(".invoice-close").on('click', function () {
            window.location.href = $(this).data('route');
        });

        $('.category-show').on('change', function () {
            set_category_filter($(this).val());
        });

        $('.cart-change').on('change', function () {
            cart_change($(this).val());
        });

        $('.customer-change').on('change', function () {
            customer_change($(this).val());
        });
        $(".hover-content-text").on('click', function () {
            let product_id = $(this).closest('.single-cart-data').data('id');
            addToCart(product_id);
        });

        // $('.js-hs-unfold-invoker').each(function () {
        //     var unfold = new HSUnfold($(this)).init();
        // });

        $('.pos-search').focus();
        $.ajax({
            url: '{{route('admin.pos.get-cart-ids')}}',
            type: 'GET',

            dataType: 'json',
            beforeSend: function () {
                $('#loading').removeClass('d-none');
            },
            success: function (data) {
                var output = '';
                for (var i = 0; i < data.cart_nam.length; i++) {
                    output += `<option value="${data.cart_nam[i]}" ${data.current_user == data.cart_nam[i] ? 'selected' : ''}>${data.cart_nam[i]}</option>`;
                }
                $('#cart_id').html(output);
                $('#current_customer').text(data.current_customer);
                $('#current_customer_phone').text(data.current_customer_phone);
                $('#current_customer_balance').text(data.current_customer_balance);
                $('#cart').empty().html(data.view);
                if (data.user_type === 'sc') {
                    customer_Balance_Append(data.user_id);
                }
            },
            complete: function () {
                $('#loading').addClass('d-none');
            },
        });
    });

    $(document).on('ready', function () {

        $(".direction-toggle").on("click", function () {
            setDirection(localStorage.getItem("direction"));
        });

        function setDirection(direction) {
            if (direction == "rtl") {
                localStorage.setItem("direction", "ltr");
                $("html").attr('dir', 'ltr');
                $(".direction-toggle").find('span').text('Toggle RTL')
            } else {
                localStorage.setItem("direction", "rtl");
                $("html").attr('dir', 'rtl');
                $(".direction-toggle").find('span').text('Toggle LTR')
            }
        }

        if (localStorage.getItem("direction") == "rtl") {
            $("html").attr('dir', "rtl");
            $(".direction-toggle").find('span').text('Toggle LTR')
        } else {
            $("html").attr('dir', "ltr");
            $(".direction-toggle").find('span').text('Toggle RTL')
        }

    })

    function payment_option(val) {
        if ($(val).val() != 1 && $(val).val() != 0) {
            $("#collected_cash").addClass('d-none');
            $("#returned_amount").addClass('d-none');
            $("#balance").addClass('d-none');
            $("#remaining_balance").addClass('d-none');
            $("#transaction_ref").removeClass('d-none');
            $('#cash_amount').attr('required', false);
        } else if ($(val).val() == 1) {
            $("#collected_cash").removeClass('d-none');
            $("#returned_amount").removeClass('d-none');
            $("#transaction_ref").addClass('d-none');
            $("#balance").addClass('d-none');
            $("#remaining_balance").addClass('d-none');

        } else if ($(val).val() == 0) {
            $("#balance").removeClass('d-none');
            $("#remaining_balance").removeClass('d-none');
            $("#collected_cash").addClass('d-none');
            $("#returned_amount").addClass('d-none');
            $("#transaction_ref").addClass('d-none');
            $('#cash_amount').attr('required', false);
            let customerId = $('#customer').val();
            $.ajax({
                url: '{{route('admin.pos.customer-balance')}}',
                type: 'GET',
                data: {
                    customer_id: customerId
                },
                dataType: 'json',
                beforeSend: function () {
                    $('#loading').removeClass('d-none');
                },
                success: function (data) {
                    let balance = data.customer_balance;
                    let order_total = $('#total_price').text();
                    let remain_balance = parseInt(balance) - parseInt(order_total);
                    $('#balance_customer').val(balance);
                    $('#balance_remain').val(remain_balance);
                },
                complete: function () {
                    $('#loading').addClass('d-none');
                },
            });
        }

    }

    function initializedCustomer() {
        $.ajax({
            url: '{{route('admin.pos.selected-customer')}}',
            type: 'GET',

            dataType: 'json',

            success: function (data) {
                if (data.user_type == 1) {
                    let balance = data.current_customer_balance;
                    let order_total = $('#total_price').text();
                    let remain_balance = parseInt(balance) - parseInt(order_total);

                    $('.customer-phone-section').removeClass('d-none');
                    $('.customer-wallet-section').removeClass('d-none');

                    $('#current_customer').text(data.current_customer);
                    $('#current_customer_phone').text(data.current_customer_phone);
                    $('#current_customer_balance').text(data.current_customer_balance);

                    $('#balance_customer').val(balance);
                    $('#balance_remain').val(remain_balance);

                    $('#wallet-payment-section').removeClass('d-none')

                    $('#customer').append(`<option value="${data.customer_id}" selected>${data.current_customer}</option>`);

                } else {
                    $('#customer').append(`<option value="0" selected>${$('#currentCustomer').val()}</option>`);
                    $('#wallet-payment-section').addClass('d-none')
                }
            },

        });
    }

    initializedCustomer();

    function customer_change(val) {
        $.post({
            url: '{{route('admin.pos.remove-coupon')}}',
            data: {
                _token: '{{csrf_token()}}',
                user_id: val
            },
            beforeSend: function () {
                $('#loading').removeClass('d-none');
            },
            success: function (data) {
                var output = '';
                for (var i = 0; i < data.cart_nam.length; i++) {
                    output += `<option value="${data.cart_nam[i]}" ${data.current_user == data.cart_nam[i] ? 'selected' : ''}>${data.cart_nam[i]}</option>`;
                }
                $('#cart_id').html(output);
                $('#current_customer').text(data.current_customer);

                if (val == 0) {
                    $('.customer-phone-section').addClass('d-none');
                    $('.customer-wallet-section').addClass('d-none');
                } else {
                    $('.customer-phone-section').removeClass('d-none');
                    $('.customer-wallet-section').removeClass('d-none');
                    $('#current_customer_phone').text(data.current_customer_phone);
                    $('#current_customer_balance').text(data.current_customer_balance);
                }

                $('#cart').empty().html(data.view);
                customer_Balance_Append(val);

                if (val == 0) {
                    $('#wallet-payment-section').addClass('d-none')

                } else {
                    $('#wallet-payment-section').removeClass('d-none')
                }
            },
            complete: function () {
                $('#loading').addClass('d-none');
            }
        });
    }

    function cart_change(val) {
        let cart_id = val;
        let url = "{{route('admin.pos.change-cart')}}" + '/?cart_id=' + val;
        document.location.href = url;
    }

    function extra_discount() {
        let discount = $('#dis_amount').val();
        let type = $('#type_ext_dis').val();
        if (discount) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.post({
                url: '{{ route('admin.pos.discount') }}',
                data: {
                    _token: '{{csrf_token()}}',
                    discount: discount,
                    type: type,
                },
                beforeSend: function () {
                    $('#loading').removeClass('d-none');
                },
                success: function (data) {
                    if (data.extra_discount === 'success') {
                        toastr.success('{{ \App\CPU\translate('extra_discount_added_successfully') }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    } else if (data.extra_discount === 'empty') {
                        toastr.warning('{{ \App\CPU\translate('your_cart_is_empty') }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });

                    } else {
                        toastr.warning('{{ \App\CPU\translate('this_discount_is_not_applied_for_this_amount') }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }

                    $('.modal-backdrop').addClass('d-none');
                    $('#cart').empty().html(data.view);
                    if (data.user_type === 'sc') {
                        customer_Balance_Append(data.user_id);
                    }
                    $('.pos-search').focus();
                    $('#dis_amount').val(discount);
                },
                complete: function () {
                    $('.modal-backdrop').addClass('d-none');
                    $(".footer-offset").removeClass("modal-open");
                    $('#loading').addClass('d-none');
                }
            });
        }
    }

    function coupon_discount() {
        let coupon_code = $('#coupon_code').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.post({
            url: '{{route('admin.pos.coupon-discount')}}',
            data: {
                _token: '{{csrf_token()}}',
                coupon_code: coupon_code,
            },
            beforeSend: function () {
                $('#loading').removeClass('d-none');
            },
            success: function (data) {
                if (data.coupon === 'success') {
                    toastr.success('{{ \App\CPU\translate('coupon_added_successfully') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                } else if (data.coupon === 'amount_low') {
                    toastr.warning('{{ \App\CPU\translate('this_discount_is_not_applied_for_this_amount') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                } else if (data.coupon === 'cart_empty') {
                    toastr.warning('{{ \App\CPU\translate('your_cart_is_empty') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                } else if (data.coupon === 'min_purchase') {
                    toastr.warning('{{ \App\CPU\translate('minimum_purchase_to_avail_this_coupon_is ')}} {{  \App\CPU\Helpers::currency_symbol() }}' + data.min_purchase, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                } else {
                    toastr.warning('{{ \App\CPU\translate('coupon_is_invalid') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }

                $('#cart').empty().html(data.view);
                if (data.user_type === 'sc') {
                    customer_Balance_Append(data.user_id);
                }
                $('.pos-search').focus();
                $('#coupon_code').val(coupon_code);

                $(".coupon-slider-button").each(function () {
                    if ($(this).data('coupon') == coupon_code) {
                        $(this).addClass('active');
                    }
                })

            },
            complete: function () {
                $('.modal-backdrop').addClass('d-none');
                $(".footer-offset").removeClass("modal-open");
                $('#loading').addClass('d-none');

            }
        });

    }

    function set_category_filter(id) {
        var nurl = new URL('{!!url()->full()!!}');
        nurl.searchParams.set('category_id', id);
        location.href = nurl;
    }

    $('#search-form').on('submit', function (e) {
        e.preventDefault();
        var keyword = $('#datatableSearch').val();
        var nurl = new URL('{!!url()->full()!!}');
        nurl.searchParams.set('keyword', keyword);
        location.href = nurl;
    });

    $('.pos-single-product-card').on('click', function () {
        let productId = $(this).data('id');
        quickView(productId);
    });

    function quickView(product_id) {
        $.ajax({
            url: '{{route('admin.pos.quick-view')}}',
            type: 'GET',
            data: {
                product_id: product_id
            },
            dataType: 'json',
            success: function (data) {
                $('#quick-view').modal('show');
                $('#quick-view-modal').empty().html(data.view);
            },
        });
    }

    function addToCart(form_id) {
        let productId = form_id;

        let productQty = $('#product_qty').val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.post({
            url: '{{ route('admin.pos.add-to-cart') }}',
            data: {
                _token: '{{csrf_token()}}',
                id: productId,
                quantity: productQty,
            },
            success: function (data) {
                if (data.qty == 0) {
                    toastr.warning('{{\App\CPU\translate('product_quantity_end!')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                    $('#product-id-' + productId).find('.pos-product-item').find('.counter-input').val(data.cart_quantity);
                    $('#product-id-' + productId).find('.pos-product-item').addClass('active');
                } else {
                    toastr.success('{{\App\CPU\translate('item_has_been_added_in_your_cart!')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                    const prevValue = $('#product-id-' + productId).find('.pos-product-item').find('.counter-input').val();
                    $('#product-id-' + productId).find('.pos-product-item').find('.counter-input').val(parseInt(prevValue) + parseInt(productQty));
                    $('#product-id-' + productId).find('.pos-product-item').addClass('active');
                }

                $('#cart').empty().html(data.view);
                if (data.user_type === 'sc') {
                    customer_Balance_Append(data.user_id);
                }
                $('.pos-search').val('').focus();
                $('#search-box').addClass('d-none');
                handleDecrementButtonDisabled();
            },
            complete: function () {
                $('#cartloader').addClass('d-none');

            }
        });

    }

    function removeFromCart(key) {
        $.post('{{ route('admin.pos.remove-from-cart') }}', {_token: '{{ csrf_token() }}', key: key}, function (data) {
            $('#cart').empty().html(data.view);
            if (data.user_type === 'sc') {
                customer_Balance_Append(data.user_id);
            }
            toastr.info('{{\App\CPU\translate('item_has_been_removed_from_cart')}}', {
                CloseButton: true,
                ProgressBar: true
            });
            $('#single-product-card-' + key).find('.counter-input').val(0);
            $('#single-product-card-' + key).removeClass('active');
            $('#single-product-card-' + key + '.pos-product-item_thumb').addClass('single-cart-data');

            //location.reload();
            $('.pos-search').focus();

        });
    }

    function emptyCart() {
        Swal.fire({
            title: '{{\App\CPU\translate('Are_you_sure?')}}',
            text: '{{\App\CPU\translate('You_want_to_remove_all_items_from_cart!!')}}',
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#161853',
            cancelButtonText: '{{\App\CPU\translate('No')}}',
            confirmButtonText: '{{\App\CPU\translate('Yes')}}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $.post('{{ route('admin.pos.emptyCart') }}', {_token: '{{ csrf_token() }}'}, function (data) {
                    $('#cart').empty().html(data.view);
                    $('.pos-search').focus();
                    if (data.user_type === 'sc') {
                        customer_Balance_Append(data.user_id);
                    }
                    toastr.info('{{\App\CPU\translate('Item_has_been_removed_from_cart')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                    location.reload()
                });

            }
        })

    }

    function updateCart() {
        $.post('<?php echo e(route('admin.pos.cart_items')); ?>', {_token: '<?php echo e(csrf_token()); ?>'}, function (data) {
            $('#cart').empty().html(data);
        });
    }

    // Increment functionality for the single product page
    $('.increment-decrement-section .btn-increment').click(function () {
        let container = $(this).closest('.increment-decrement-section');
        let input = container.find('.counter-input');
        let currentVal = parseInt(input.val());

        if (!isNaN(currentVal)) {
            input.val(currentVal + 1);
            updateQuantity(container.data('id'), input.val());
        }
    });

    // Decrement functionality for the single product page
    $('.increment-decrement-section .btn-decrement').click(function () {

        let container = $(this).closest('.increment-decrement-section');
        let input = container.find('.counter-input');
        let currentVal = parseInt(input.val());

        if (!isNaN(currentVal) && currentVal > 1) {
            input.val(currentVal - 1);
            updateQuantity(container.data('id'), input.val());
        }
    });

    // Manual input change handling for the single product page
    $('.increment-decrement-section .counter-input').on('input', function () {
        let input = $(this);
        let value = parseInt(input.val());

        if (isNaN(value) || value < 1) {
            input.val(1);
        }
        updateQuantity(input.closest('.increment-decrement-section').data('id'), input.val());
    });

    function updateQuantity(id, qty) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.post({
            url: '{{ route('admin.pos.updateQuantity') }}',
            data: {
                _token: '{{csrf_token()}}',
                key: id,
                quantity: qty,
            },
            beforeSend: function () {
                $('#loading').removeClass('d-none');
            },
            success: function (data) {
                if(data?.message)
                {
                    toastr.warning(data.message, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
                $('.single_card_input_' + id).val(data.cart_quantity);
                $('.pos-search').focus();
                $('#cart').empty().html(data.view);
                if (data.user_type === 'sc') {
                    customer_Balance_Append(data.user_id);
                }
                handleDecrementButtonDisabled();
            },
            complete: function () {
                $('#loading').addClass('d-none');
            }
        });


    }

    $('.js-select2-custom').each(function () {
        var select2 = $.HSCore.components.HSSelect2.init($(this));
    });

    $('.js-data-example-ajax').select2({
        ajax: {
            url: '{{route('admin.pos.customers')}}',
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            __port: function (params, success, failure) {
                var $request = $.ajax(params);

                $request.then(success);
                $request.fail(failure);

                return $request;
            }
        }
    });

    jQuery(".search-bar-input").on('keyup', function () {
        $(".search-card").removeClass('d-none').show();
        let name = $(".search-bar-input").val();
        if (name.length > 0) {
            $('#search-box').removeClass('d-none').show();
            $.get({
                url: '{{route('admin.pos.search-products')}}',
                dataType: 'json',
                data: {
                    name: name
                },
                beforeSend: function () {
                    $('#loading').removeClass('d-none');
                },
                success: function (data) {
                    // if (data.count == 0) {
                    //     $('#search-box').addClass('d-none');
                    // }
                    $('.search-result-box').empty().html(data.result);
                },
                complete: function () {
                    $('#loading').addClass('d-none');
                },
            });
        } else {
            $('.search-result-box').empty();
            $('#search-box').addClass('d-none');
        }
    });

    jQuery(".search-bar-input").on('keyup', delay(function () {
        console.log("hi")
        $(".search-card").removeClass('d-none').show();
        let name = $(".search-bar-input").val();
        if (name.length > 0 || isNaN(name)) {

            $.get({
                url: '{{route('admin.pos.search-by-add')}}',
                dataType: 'json',
                data: {
                    name: name
                },
                success: function (data) {
                    if (data.count == 1) {
                        $('.pos-search').attr("disabled", true);
                        addToCart(data.id);
                        $('.pos-search').attr("disabled", false);
                        $('.search-result-box').empty().html(data.result);
                        $('.pos-search').val('');
                        $('#search-box').addClass('d-none');
                    }
                },
            });
        } else {
            $('.search-result-box').empty();
        }
    }, 1000));

    $(document).ready(function () {
        if ($(".navbar-vertical-content li.active").length) {
            $('.navbar-vertical-content').animate({
                scrollTop: $(".navbar-vertical-content li.active").offset().top - 150
            }, 10);
        }
    });

    $(document).ready(function () {
        $(document).on('click', '.search-result-box .list-group-item', function () {
            $('.pos-search').val('');
            $('.search-result-box').addClass('d--none'); // Hide the results box
        });

        function fetchSubCategories() {
            $(document).on('change', '.category-checkbox', function () {
                $('#subCategorySelectAll').prop('checked', false);
                var selectedCategories = [];

                $('.category-checkbox:checked').each(function () {
                    selectedCategories.push($(this).val());
                });

                let allChecked = $('.category-checkbox').length > 0 && $('.category-checkbox:checked').length === $('.category-checkbox').length;
                $('#categorySelectAll').prop('checked', allChecked);

                $.ajax({
                    url: '{{ route("admin.pos.subcategories") }}',
                    method: 'GET',
                    data: {
                        category_ids: selectedCategories
                    },
                    success: function (response) {
                        let remainingSubcategories = response.subcategories.length - 6;
                        $('#seeMoreSubcategory').val(remainingSubcategories);
                        if (response.subcategories.length > 0) {
                            $('#subcategory-section').removeClass('d-none');
                            $('#subcategoryFilter').empty();

                            response.subcategories.forEach(function (subcategory) {
                                $('#subcategoryFilter').append(`
                            <div class="col-sm-6">
                                <label class="form-control mb-3">
                                    <div class="check-item">
                                        <div class="d-flex form-group form-check form--check m-0">
                                            <input type="checkbox" name="subcategory_ids[]" value="${subcategory.id}" class="form-check-input subcategory-checkbox">
                                            <span class="align-content-center form-check-label line-limit-1 text-left ml-2 text-dark" title="${subcategory.name}">${subcategory.name}</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        `);
                            });

                            if (remainingSubcategories > 0) {
                                $('#see_more_subcategory_btn').removeClass('d-none');
                                $('#more-sub-category-count').text(` (${remainingSubcategories})`);
                                if ($('#see_more_subcategory').length && $('#subcategoryFilter').length) {
                                    if ($('#subcategoryFilter').hasClass('expanded')) {
                                        $('#see_more_subcategory').html('<strong>See less</strong>');
                                    } else {
                                        $('#see_more_subcategory').html(`<strong>See more <span>(${remainingSubcategories})</span></strong>`);
                                    }
                                }
                            } else {
                                $('#see_more_subcategory_btn').addClass('d-none');
                            }
                            $('#subCategorySelectAll').off('click').on('click', function () {
                                var isChecked = $(this).prop('checked');
                                $('.subcategory-checkbox').prop('checked', isChecked);

                                // Call toggleFilter only if the necessary elements exist
                                if ($('#see_more_subcategory').length && $('#subcategoryFilter').length && $('#seeMoreSubcategory').length) {
                                    if (!isChecked) {
                                        $('#subcategoryFilter').removeClass('expanded');
                                        $('#see_more_subcategory').html(`<strong> See more (${$('#seeMoreSubcategory').val()})</strong>`);
                                    } else {
                                        $('#subcategoryFilter').addClass('expanded');
                                        $('#see_more_subcategory').html('<strong>See less</strong>');
                                    }
                                }
                            });
                            $(document).on('change', '.subcategory-checkbox', function () {
                                let allChecked = $('.subcategory-checkbox').length > 0 && $('.subcategory-checkbox:checked').length === $('.subcategory-checkbox').length;
                                $('#subCategorySelectAll').prop('checked', allChecked);
                            });
                        } else {
                            $('#subcategory-section').addClass('d-none');
                        }
                    }
                });
            });
        }

        fetchSubCategories();

        function updateSelectAllCheckbox() {
            let allChecked = $('.category-checkbox').length > 0 && $('.category-checkbox:checked').length === $('.category-checkbox').length;
            $('#categorySelectAll').prop('checked', allChecked);
            if (allChecked) {
                $('#categoryFilter').addClass('expanded');
                $('#see_more_category').html('<strong>See less</strong>');
            } else {
                $('#categoryFilter').removeClass('expanded');
                $('#see_more_category').html(`<strong> See more (${$('#seeMoreCategory').val()})</strong>`);
            }
        }

        updateSelectAllCheckbox();

        function updateSelectAllSubcategoryCheckbox() {
            let allChecked = $('.subcategory-checkbox').length > 0 && $('.subcategory-checkbox:checked').length === $('.subcategory-checkbox').length;
            $('#subCategorySelectAll').prop('checked', allChecked);
            if (allChecked) {
                $('#subcategoryFilter').addClass('expanded');
                $('#see_more_subcategory').html('<strong>See less</strong>');
            } else {
                $('#subcategoryFilter').removeClass('expanded');
                $('#see_more_subcategory').html(`<strong> See more (${$('#seeMoreSubcategory').val()})</strong>`);
            }
        }

        updateSelectAllSubcategoryCheckbox();

        function updateSelectAllBrandCheckbox() {
            let allChecked = $('.brand-checkbox').length > 0 && $('.brand-checkbox:checked').length === $('.brand-checkbox').length;
            $('#brandSelectAll').prop('checked', allChecked);
            if (allChecked) {
                $('#brandFilter').addClass('expanded');
                $('#see_more_brand').html('<strong>See less</strong>');
            } else {
                $('#brandFilter').removeClass('expanded');
                $('#see_more_brand').html(`<strong> See more (${$('#seeMoreBrand').val()})</strong>`);
            }
        }

        updateSelectAllBrandCheckbox();

        $('#categorySelectAll').on('click', function () {
            var isChecked = $(this).prop('checked');
            $('.category-checkbox').prop('checked', isChecked);
            $('.category-checkbox').trigger('change');
            if (!isChecked) {
                $('#subCategorySelectAll').prop('checked', false);
                $('#categoryFilter').removeClass('expanded');
                $('#see_more_category').html(`<strong> See more (${$('#seeMoreCategory').val()})</strong>`);
            } else {
                $('#categoryFilter').addClass('expanded');
                $('#see_more_category').html('<strong>See less</strong>');
            }
        })

        $('#subCategorySelectAll').off('click').on('click', function () {
            var isChecked = $(this).prop('checked');
            $('.subcategory-checkbox').prop('checked', isChecked);

            // Call toggleFilter only if the necessary elements exist
            if ($('#see_more_subcategory').length && $('#subcategoryFilter').length && $('#seeMoreSubcategory').length) {
                if (!isChecked) {
                    $('#subcategoryFilter').removeClass('expanded');
                    $('#see_more_subcategory').html(`<strong> See more (${$('#seeMoreSubcategory').val()})</strong>`);
                } else {
                    $('#subcategoryFilter').addClass('expanded');
                    $('#see_more_subcategory').html('<strong>See less</strong>');
                }
            }
        });

        $(document).on('change', '.subcategory-checkbox', function () {
            let allChecked = $('.subcategory-checkbox').length > 0 && $('.subcategory-checkbox:checked').length === $('.subcategory-checkbox').length;
            $('#subCategorySelectAll').prop('checked', allChecked);
        });

        $('#brandSelectAll').on('click', function () {
            var isChecked = $(this).prop('checked');
            $('.brand-checkbox').prop('checked', isChecked);
            if (!isChecked) {
                $('#brandFilter').removeClass('expanded');
                $('#see_more_brand').html(`<strong> See more (${$('#seeMoreBrand').val()})</strong>`);
            } else {
                $('#brandFilter').addClass('expanded');
                $('#see_more_brand').html('<strong>See less</strong>');
            }
        })

        $(document).on('change', '.brand-checkbox', function () {
            let allChecked = $('.brand-checkbox').length > 0 && $('.brand-checkbox:checked').length === $('.brand-checkbox').length;
            $('#brandSelectAll').prop('checked', allChecked);
        });

        // filter category show / hide
        function toggleFilter(buttonId, filterId, valueId) {
            console.log('toggle')
            const button = document.getElementById(buttonId);
            const filter = document.getElementById(filterId);
            const value = document.getElementById(valueId);

            filter.classList.toggle('expanded');

            if (filter.classList.contains('expanded')) {
                button.innerHTML = '<strong>See less</strong>';
            } else {
                button.innerHTML = `<strong>See more <span>(${value.value})</span></strong>`;
            }
        }

        const seeMoreCategory = document.getElementById('see_more_category');
        if (seeMoreCategory) {
            seeMoreCategory.addEventListener('click', function () {
                toggleFilter('see_more_category', 'categoryFilter', 'seeMoreCategory');
            });
        }


        const seeMoreSubcategory = document.getElementById('see_more_subcategory');
        if (seeMoreSubcategory) {
            seeMoreSubcategory.addEventListener('click', function () {
                toggleFilter('see_more_subcategory', 'subcategoryFilter', 'seeMoreSubcategory');
            });
        }

        const seeMoreBrand = document.getElementById('see_more_brand');
        if (seeMoreBrand) {
            seeMoreBrand.addEventListener('click', function () {
                toggleFilter('see_more_brand', 'brandFilter', 'seeMoreBrand');
            });
        }
    });


    // Toggle all the off-canvas logic
    $('#filterMenuToggle').on('click', function () {
        closeAllOffcanvas();
        $('#offcanvasFilterMenu').toggleClass('open');
        $('#overlay').toggleClass('active');
        toggleBodyScroll();
    });

    $('#holdMenuToggle').on('click', function () {
        closeAllOffcanvas();
        $('#offcanvasHoldMenu').toggleClass('open');
        $('#overlay').toggleClass('active');
        toggleBodyScroll();
    });

    $('#overlay').on('click', function () {
        closeAllOffcanvas();
        $(this).removeClass('active');
        toggleBodyScroll();
    });

    document.getElementById('cancel_filter').addEventListener('click', function () {
        $('#offcanvasFilterMenu').removeClass('open');
        $('#overlay').removeClass('active');
        toggleBodyScroll();
    });

    document.getElementById('cancel_hold').addEventListener('click', function () {
        $('#offcanvasHoldMenu').removeClass('open');
        $('#overlay').removeClass('active');
        toggleBodyScroll();
    });

    $(document).on('click', '.close-print-invoice', function () {
        $('#print-invoice').removeClass('open');
        $('#overlay').removeClass('active');
        toggleBodyScroll();
    });

    $(document).on('ready', function () {
        @if($order)
        closeAllOffcanvas();
        $('#print-invoice').addClass('open');
        $('#overlay').addClass('active');
        toggleBodyScroll();
        @endif
    });

    function closeAllOffcanvas() {
        $('#offcanvasFilterMenu, #offcanvasHoldMenu, #print-invoice').removeClass('open');
    }

    function toggleBodyScroll() {
        const isAnyOpen = $('#offcanvasFilterMenu').hasClass('open') ||
            $('#offcanvasHoldMenu').hasClass('open') ||
            $('#print-invoice').hasClass('open');

        $('body').toggleClass('modal-open', isAnyOpen);
    }

    // Min-max range slider
    const minPrice = document.getElementById('minPrice');
    const maxPrice = document.getElementById('maxPrice');
    const minPriceValue = document.getElementById('minPriceValue');
    const maxPriceValue = document.getElementById('maxPriceValue');
    const rangeTrack = document.getElementById('rangeTrack');

    function updateTrackBackground() {
        const minVal = parseInt(minPrice.value);
        const maxVal = parseInt(maxPrice.value);
        const minPercent = (minVal / minPrice.max) * 100;
        const maxPercent = (maxVal / maxPrice.max) * 100;

        rangeTrack.style.insetInlineStart = `${minPercent}%`;
        rangeTrack.style.width = `${maxPercent - minPercent}%`;
    }

    function updatePriceValues() {
        minPriceValue.textContent = `${minPrice.value} {{\App\CPU\Helpers::currency_symbol() }}`;
        maxPriceValue.textContent = `${maxPrice.value} {{\App\CPU\Helpers::currency_symbol() }}`;
    }

    minPrice.addEventListener('input', function () {
        if (parseInt(minPrice.value) >= parseInt(maxPrice.value)) {
            minPrice.value = maxPrice.value - 1;
        }
        updatePriceValues();
        updateTrackBackground();
    });

    maxPrice.addEventListener('input', function () {
        if (parseInt(maxPrice.value) <= parseInt(minPrice.value)) {
            maxPrice.value = parseInt(minPrice.value) + 1;
        }
        updatePriceValues();
        updateTrackBackground();
    });

    updatePriceValues();
    updateTrackBackground();

    // Hold card table show / hide
    document.addEventListener("DOMContentLoaded", function () {
        let currentlyOpenTable = null;

        const productButtons = document.querySelectorAll('.hold-product-list-btn');

        productButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                event.stopPropagation();

                const index = this.id.split('_').pop();
                const tableId = 'table_' + index;
                const table = document.getElementById(tableId);

                // const tableId = 'table_' + this.id.split('_')[3];
                // const table = document.getElementById(tableId);

                if (currentlyOpenTable && currentlyOpenTable !== table) {
                    currentlyOpenTable.classList.add('hidden');
                }

                table.classList.toggle('hidden');

                currentlyOpenTable = table.classList.contains('hidden') ? null : table;
            });
        });

        document.addEventListener('click', function (event) {
            if (currentlyOpenTable && !currentlyOpenTable.contains(event.target)) {
                currentlyOpenTable.classList.add('hidden');
                currentlyOpenTable = null;
            }
        });
    });

    // Quatity button
    (function () {
        const quantityContainer = document.querySelector(".quantity");
        const minusBtn = quantityContainer.querySelector(".minus");
        const plusBtn = quantityContainer.querySelector(".plus");
        const inputBox = quantityContainer.querySelector(".input-box");

        updateButtonStates();

        quantityContainer.addEventListener("click", handleButtonClick);
        inputBox.addEventListener("input", handleQuantityChange);

        function updateButtonStates() {
            const value = parseInt(inputBox.value);
            minusBtn.disabled = value <= 1;
            plusBtn.disabled = value >= parseInt(inputBox.max);
        }

        function handleButtonClick(event) {
            if (event.target.classList.contains("minus")) {
                decreaseValue();
            } else if (event.target.classList.contains("plus")) {
                increaseValue();
            }
        }

        function decreaseValue() {
            let value = parseInt(inputBox.value);
            value = isNaN(value) ? 1 : Math.max(value - 1, 1);
            inputBox.value = value;
            updateButtonStates();
            handleQuantityChange();
        }

        function increaseValue() {
            let value = parseInt(inputBox.value);
            value = isNaN(value) ? 1 : Math.min(value + 1, parseInt(inputBox.max));
            inputBox.value = value;
            updateButtonStates();
            handleQuantityChange();
        }

        function handleQuantityChange() {
            let value = parseInt(inputBox.value);
            value = isNaN(value) ? 1 : value;
        }
    })();

    $('.change-counter').on('change', function () {
        let counterId = $(this).val()
        $.post({
            url: '{{route('admin.pos.change-counter')}}',
            data: {
                _token: '{{csrf_token()}}',
                counter_id: counterId
            },
            beforeSend: function () {
                $('#loading').removeClass('d-none');
            },
            success: function (data) {

            },
            complete: function () {
                $('#loading').addClass('d-none');
            }
        });
    });

    $(document).ready(function () {
        $('#hold-order-search').on('keyup', function () {
            var searchTerm = $(this).val().toLowerCase();

            $('.single-hold-card').each(function () {
                var holdId = $(this).find('h5 strong').text().toLowerCase();
                var customerName = $(this).find('h6').text().toLowerCase();
                var customerMobile = $(this).find('a').text().toLowerCase();

                if (holdId.includes(searchTerm) || customerName.includes(searchTerm) || customerMobile.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });

</script>

<script>
    function printFilterCount() {
        let currentUrl = new URL(window.location.href);
        let params = currentUrl.searchParams;
        let filteredParams = Array.from(params.keys()).filter(param =>
            !['type', 'search', 'start_date', 'end_date', 'page'].includes(param)
        );
        let hasPriceRange = filteredParams.includes('min_price') && filteredParams.includes('max_price');
        if (hasPriceRange) {
            filteredParams = filteredParams.filter(param => param !== 'min_price' && param !== 'max_price');
            filteredParams.push('price_range');
        }
        let paramCount = filteredParams.length;
        if (paramCount > 0) {
            $('.show-filter-count').append('<span class="position-absolute badge badge-primary filter-count">' + paramCount + '</span>');
        } else {
            $('.show-filter-count').find('.filter-count').remove();
        }
    }

    printFilterCount();
</script>
