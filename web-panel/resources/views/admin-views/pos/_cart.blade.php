@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('assets/admin') }}/css/custom.css"/>
@endpush
@if(count($items) > 0)
    <div class="card-body py-0">
        <div class="pos-cart-table p-3 rounded bg-soft-secondary">
            <div class="d-flex flex-column gap-3">
                @foreach ($items as $key => $item)
                    @php
                        $productSubtotal = $item['price'] * $item['quantity'];
                    @endphp
                    <div class="cart-product-item">
                        <img class="cart-product-item__img"
                             src="{{ onErrorImage($item['image'],asset('storage/product/' . $item['image']) ,asset('assets/admin/svg/components/product-default.svg') ,'product/') }}"
                             alt="{{ $item['name'] ?? 'Product Image' }}">
                        <div class="cart-product-item__content">
                            <h5 class="name">{{ $item['name'] ?? '' }}</h5>
                            <h6 class="font-weight-bold">
                                <span>{{ number_format($productSubtotal, 2) }} {{ \App\CPU\Helpers::currency_symbol() }}</span>
                            </h6>
                            <div class="counter-container counter-container-div" data-id="{{ $item['id'] }}">
                                <button class="btn-decrement">-</button>
                                <input type="number" data-key="{{ $key }}" class="counter-input qty-width"
                                       value="{{ $item['quantity'] }}" min="1">
                                <button class="btn-increment">+</button>
                            </div>
                            <a href="javascript:removeFromCart({{ $item['id'] }})" class="remove-button">
                                <i class="tio-clear"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

<div class="box p-3">
    <div class="card bg-soft-primary shadow-none border-0 mb-3">
        <div class="card-body">
            <dl class="row">
                <dt class="col-6">{{ \App\CPU\translate('sub_total') }} :</dt>
                <dd class="col-6 text-right">{{ number_format($subtotal, 2) }} {{ \App\CPU\Helpers::currency_symbol() }}</dd>

                <dt class="col-6">{{ \App\CPU\translate('product_discount') }} :</dt>
                <dd class="col-6 text-right">{{ number_format($discountOnProduct, 2) }} {{ \App\CPU\Helpers::currency_symbol() }}</dd>

                <dt class="col-6">{{ \App\CPU\translate('coupon_discount') }}:</dt>
                <dd class="col-6 text-right">
                    <button id="coupon_discount" class="btn p-0 text-blue text-underline" type="button"
                            data-toggle="collapse" data-target=".coupon-modal">
                        <i class="tio-edit"></i> {{ number_format($couponDiscount, 2) }} {{ \App\CPU\Helpers::currency_symbol() }}
                    </button>
                </dd>

                <dt class="col-6">{{ \App\CPU\translate('extra_discount') }}:</dt>
                <dd class="col-6 text-right">
                    <div class="d-flex justify-content-end">
                        <div class="dropdown" dir="ltr">
                            <button id="extra_discount" class="btn p-0 text-blue text-underline" type="button"
                                    data-toggle="dropdown" data-placement="bottom">
                                <i class="tio-edit"></i> {{ number_format($discountAmount, 2) }} {{ \App\CPU\Helpers::currency_symbol() }}
                            </button>
                            <div class="dropdown-menu p-0 shadow-none m-0 bg-transparent extra-discount-dropdown-menu">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">{{ \App\CPU\translate('extra_discount') }}</h5>
                                        <button type="button" class="close text-dark" data-dismiss="dropdown"
                                                aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="card-body prevent-default">
                                        <div class="form-group">
                                            <label class="text-dark">{{ \App\CPU\translate('discount') }}</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="discount"
                                                       value="{{$extDiscountAmount>0 ?$extDiscountAmount : ""}}"
                                                       id="dis_amount" step="0.01" min="0" placeholder="Ex: 10">
                                                <div class="select2-parent min-w-150px">
                                                    <select name="discountType" class="dropdown-Select2"
                                                            id="type_ext_dis">
                                                        <option
                                                            value="amount" {{ $extDiscountType == 'amount' ? 'selected' : '' }}>
                                                            {{ \App\CPU\translate('amount') }}
                                                            ({{ \App\CPU\Helpers::currency_symbol() }})
                                                        </option>
                                                        <option
                                                            value="percent" {{ $extDiscountType == 'percent' ? 'selected' : '' }}>
                                                            {{ \App\CPU\translate('percent') }} (%)
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button class="btn font-weight-bold px-4 py-2 btn-primary extra-discount"
                                                    type="button">{{ \App\CPU\translate('Apply') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </dd>

                <dt class="col-6">{{ \App\CPU\translate('tax') }} :</dt>
                <dd class="col-6 text-right">{{ number_format($productTax, 2) }} {{ \App\CPU\Helpers::currency_symbol() }}</dd>

                <dt class="col-6 h4 b">{{ \App\CPU\translate('total') }} :</dt>
                <dd class="col-6 text-right h4 b">
                    <span
                        id="total_price">{{ number_format($totalAmount, 2) }}</span> {{ \App\CPU\Helpers::currency_symbol() }}
                </dd>
            </dl>
        </div>
    </div>

    {{-- Payment method & comments... (keep as you had it) --}}
    <div>
        <h5 class="mb-2">{{ \App\CPU\translate('Payment Method') }}</h5>
        <div class="swiper-js mb-3 swiper">
            <div class="swiper-prev">
                <i class="tio-arrow-backward"></i>
            </div>
            <div class="swiper-next">
                <i class="tio-arrow-forward"></i>
            </div>
            <ul class="nav flex-nowrap nav-pills nav--pills swiper-wrapper">
                <li class="nav-item swiper-slide" role="presentation">
                    <label class="nav-link active change_payment_method" data-toggle="pill" data-target="#PaymentCash"
                           aria-selected="true">
                        <span>{{ \App\CPU\translate('Cash') }}</span>
                        <input type="radio" name="payment_method" value="1" class="payment-method-radio d-none "
                               checked>
                    </label>
                </li>
                <li class="nav-item swiper-slide wallet-payment-section" role="presentation"
                    id="wallet-payment-section">
                    <label class="nav-link change_payment_method" data-toggle="pill" data-target="#PaymentWallet"
                           aria-selected="false">
                        <span>{{ \App\CPU\translate('Wallet') }}</span>
                        <input type="radio" name="payment_method" value="0" class="payment-method-radio d-none">
                    </label>
                </li>
                @foreach (\App\Models\Account::all() as $account)
                    @if ($account['id'] != 1 && $account['id'] != 2 && $account['id'] != 3)
                        <li class="nav-item swiper-slide" role="presentation">
                            <label class="nav-link change_payment_method" data-toggle="pill" data-target="#PaymentCard"
                                   aria-selected="false">
                                <span>{{ $account['account'] }}</span>
                                <input type="radio" name="payment_method" value="{{ $account['id'] }}"
                                       class="payment-method-radio d-none">
                            </label>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
        <div class="tab-content fs-13 text-dark">
            {{-- Cash Payment --}}
            <div class="tab-pane show active" id="PaymentCash">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div class="text-text">{{ \App\CPU\translate('Paid Amount') }}</div>
                    <div>
                        <input type="number" class="form-control h-35px text-sm w-120px" value="{{$totalAmount}}"
                               id="paid_amount" onkeyup="calculateChange();">
                    </div>
                </div>
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div>{{ \App\CPU\translate('Change Amount') }}</div>
                    <div class="fs-15 font-weight-medium" id="change_amount">
                        0 {{ \App\CPU\Helpers::currency_symbol() }}</div>
                </div>
            </div>
            {{-- Card Payment --}}
            <div class="tab-pane" id="PaymentCard">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div class="text-text">{{ \App\CPU\translate('Paid Amount') }}</div>
                    <div>
                        <input type="number" class="form-control h-35px text-sm w-120px" value="{{$totalAmount}}"
                               readonly>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold">{{ \App\CPU\translate('Card No') }}</label>
                    <input type="text" class="form-control text-sm" id="card_number" placeholder="Ex: 0000-0000-0000">
                </div>
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div>{{ \App\CPU\translate('Transaction Reference') }}</div>
                    <div>
                        <input type="text" class="form-control h-35px text-sm w-200px" placeholder="Transaction Ref"
                               id="transaction_ref">
                    </div>
                </div>
            </div>
            {{-- Wallet Payment --}}
            <div class="tab-pane" id="PaymentWallet">
                <div class="mb-3">
                    <label class="form-label font-weight-bold">{{ \App\CPU\translate('Phone No or Email') }}</label>
                    <input type="text" class="form-control text-sm" id="phone_or_email"
                           placeholder="Ex: 0987654321 / acb@gmail.com">
                </div>
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div
                        class="text-text">{{ \App\CPU\translate('Customer Balance') }} {{ \App\CPU\Helpers::currency_symbol() }}</div>

                    <div class="text-right">
                        <input type="number" class="form-control text-sm w-120px" id="customer_balancee" readonly>
                    </div>
                    {{--                    <div class="fs-15 font-weight-medium" id="customer_balancee">0 {{ \App\CPU\Helpers::currency_symbol() }}</div>--}}
                </div>
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div
                        class="text-text">{{ \App\CPU\translate('Remaining Balance') }} {{ \App\CPU\Helpers::currency_symbol() }}</div>
                    <div class="text-right">
                        <input type="number" class="form-control text-sm w-120px" id="remaining_balancee" readonly>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <label class="form-label text-dark font-weight-bold">{{ \App\CPU\translate('Comments') }}</label>
            <textarea rows="2" name="comment" class="form-control" id="commentTextArea"></textarea>
        </div>
    </div>
</div>


<div class="position-sticky bottom-0 bg-white z-100 p-3">
    <div class="pos-order-group-button">
        <a class="btn btn-soft-danger" href="{{route('admin.pos.clear-cart-ids')}}">
            <i class="tio-clear-circle"></i>
            {{ \App\CPU\translate('Cancel') }}
        </a>
        <a type="button" class="btn btn-soft-warning" href="{{route('admin.pos.new-cart-id')}}" data-toggle="offcanvas"
           data-target="#hold-sidebar">
            <i class="tio-pause "></i>
            {{ \App\CPU\translate('Hold') }}
        </a>
        <form action="{{ route('admin.pos.order') }}" method="POST" id="orderForm">
            @csrf

            <input type="hidden" name="type" id="payment_method_input">
            <input type="hidden" name="collected_cash" id="paid_amount_input">
            <input type="hidden" name="card_number" id="cardNumber">
            <input type="hidden" name="transaction_reference" id="transactionReference">
            <input type="hidden" name="email_or_phone" id="phoneOrEmail">
            <input type="hidden" name="comment" id="comment">
            <input type="hidden" name="remaining_balance" id="remainingBalance">

            <button type="button" class="btn btn-primary" onclick="submitOrder();">
                {{ \App\CPU\translate('Place_Order') }}
            </button>
        </form>
    </div>
</div>

{{-- Coupon Modal --}}
<div class="coupon-modal collapse">
    <div class="modal-scroll">
        <div class="card-header d-flex justify-content-end border-0 pb-0">
            <button type="button" class="close text-dark" data-toggle="collapse" data-target=".coupon-modal">
                <i class="tio-clear-circle"></i>
            </button>
        </div>
        <div class="card-body px-4 pb-4 pt-0">
            <div class="text-center">
                <h3 class="card-title flex-grow-1 text-center">{{ \App\CPU\translate('Coupon Discount') }}</h3>
                <p>{{ \App\CPU\translate('Select any available coupon or input code') }}</p>
            </div>
            <div>
                <form class="coupon-discount-modal">
                    <div class="mb-4">
                        <h5 class="">{{ \App\CPU\translate('Available Coupons') }}</h5>
                        <div class="coupon-slider">
                            <div class="swiper-prev">
                                <i class="tio-arrow-backward"></i>
                            </div>
                            <div class="swiper-next">
                                <i class="tio-arrow-forward"></i>
                            </div>
                            <div class="swiper-wrapper get-coupon">

                            </div>
                        </div>
                    </div>
                    <h5 class="">{{  \App\CPU\translate('Coupon Code') }}</h5>
                    <input type="text" class="form-control" name="coupon_code" value="{{$couponCode ?? ""}}"
                           id="coupon_code" placeholder="Coupon Code">
                    <div class="d-flex flex-wrap gap-3 justify-content-end mt-3">
                        <button class="btn btn-soft-danger" type="button" data-toggle="collapse"
                                data-target=".coupon-modal">{{ \App\CPU\translate('Cancel') }}</button>
                        <button class="btn btn-primary px-4 apply-coupon-btn"
                                type="submit">{{ \App\CPU\translate('Apply') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add-customer" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-header px-0 pt-0 pb-3 border-bottom mb-4">
                    <h5 class="modal-title">{{ \App\CPU\translate('New Customer Info') }}</h5>
                    <button type="button" class="text-dark close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            <i class="tio-clear"></i>
                        </span>
                    </button>
                </div>
                <form action="{{ route('admin.customer.store') }}" method="post" id="product_form">
                    @csrf
                    <input type="hidden" class="form-control" name="balance" value=0>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label
                                    class="input-label font-weight-medium text-capitalize">{{ \App\CPU\translate('customer_name') }}
                                    <span
                                        class="input-label-secondary text-danger">*</span></label>
                                <input type="text" name="name" class="form-control h-44px" value="{{ old('name') }}"
                                       placeholder="{{ \App\CPU\translate('customer_name') }}" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label
                                    class="input-label font-weight-medium text-capitalize">{{ \App\CPU\translate('mobile_no') }}
                                    <span
                                        class="input-label-secondary text-danger">*</span></label>
                                <input type="tel" id="mobile" name="mobile" class="form-control h-44px"
                                       value="{{ old('mobile') }}"
                                       pattern="[+0-9]+"
                                       title="Please enter a valid phone number with only numbers and the plus sign (+)"
                                       placeholder="{{ \App\CPU\translate('mobile_no') }}" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label
                                    class="input-label font-weight-medium text-capitalize">{{ \App\CPU\translate('email address') }}</label>
                                <input type="email" name="email" class="form-control h-44px"
                                       value="{{ old('email') }}"
                                       placeholder="{{ \App\CPU\translate('Ex_:_ex@example.com') }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label
                                    class="input-label font-weight-medium text-capitalize">{{ \App\CPU\translate('state') }}</label>
                                <input type="text" name="state" class="form-control h-44px"
                                       value="{{ old('state') }}" placeholder="{{ \App\CPU\translate('state') }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label
                                    class="input-label font-weight-medium text-capitalize">{{ \App\CPU\translate('city') }} </label>
                                <input type="text" name="city" class="form-control h-44px"
                                       value="{{ old('city') }}" placeholder="{{ \App\CPU\translate('city') }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label
                                    class="input-label font-weight-medium text-capitalize">{{ \App\CPU\translate('zip_code') }} </label>
                                <input type="text" name="zip_code" class="form-control h-44px"
                                       value="{{ old('zip_code') }}"
                                       placeholder="{{ \App\CPU\translate('zip_code') }}">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label
                                    class="input-label font-weight-medium text-capitalize">{{ \App\CPU\translate('address') }} </label>
                                <textarea name="address" class="form-control"
                                          placeholder="{{ \App\CPU\translate('address') }}"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end">
                        <button type="reset" class="btn btn-soft-dark px-4 font-weight-bold min-w-94px"
                                data-dismiss="modal">{{ \App\CPU\translate('Cancel') }}</button>
                        <button type="submit"
                                class="btn btn-primary px-4 font-weight-bold min-w-94px">{{ \App\CPU\translate('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add-tax" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ \App\CPU\translate('update_tax') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.pos.tax') }}" method="POST" class="row">
                    @csrf
                    <div class="form-group col-12">
                        <label for="">{{ \App\CPU\translate('tax') }} (%)</label>
                        <input type="number" class="form-control" name="tax" min="0">
                    </div>

                    <div class="form-group col-sm-12">
                        <button class="btn btn-sm btn-primary"
                                type="submit">{{ \App\CPU\translate('submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="short-cut-keys" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ \App\CPU\translate('short_cut_keys') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <span>{{ \App\CPU\translate('to_click_order') }} : alt + O</span><br>
                <span>{{ \App\CPU\translate('to_click_payment_submit') }} : alt + S</span><br>
                <span>{{ \App\CPU\translate('to_close_payment_submit') }} : alt + Z</span><br>
                <span>{{ \App\CPU\translate('to_click_cancel_cart_item_all') }} : alt + C</span><br>
                <span>{{ \App\CPU\translate('to_click_add_new_customer') }} : alt + A</span> <br>
                <span>{{ \App\CPU\translate('to_submit_add_new_customer_form') }} : alt + N</span><br>
                <span>{{ \App\CPU\translate('to_click_short_cut_keys') }} : alt + K</span><br>
                <span>{{ \App\CPU\translate('to_print_invoice') }} : alt + P</span> <br>
                <span>{{ \App\CPU\translate('to_cancel_invoice') }} : alt + B</span> <br>
                <span>{{ \App\CPU\translate('to_focus_search_input') }} : alt + Q</span> <br>
                <span>{{ \App\CPU\translate('to_click_extra_discount') }} : alt + E</span> <br>
                <span>{{ \App\CPU\translate('to_click_coupon_discount') }} : alt + D</span> <br>
            </div>
        </div>
    </div>
</div>

<script>
    "use strict";

    $('#coupon_discount').on('click', function () {
        let url = '{{ route('admin.pos.get-coupon') }}';
        $.ajax({
            url: url,
            type: 'GET',
            success: function (data) {
                $('.get-coupon').empty().html(data.view);
                if (data.total_coupon < 1) {
                    $('.apply-coupon-btn').attr('disabled', true);
                }
                if ($('#coupon_code').val()) {
                    $('.coupon-' + $('#coupon_code').val()).addClass('active');
                }
                $('.coupon-slider-item').on('click', function () {
                    $('.coupon-slider-button').removeClass('active');
                    $(this).find('.coupon-slider-button').addClass('active');
                    $('input[name="coupon_code"]').val($(this).find('.left h6').text().split(' ')[2]);
                })
            }
        });
    })

    $(".submit-order").on('click', function () {
        submit_order();
    });

    $(".coupon-discount-modal").on('submit', function (e) {
        e.preventDefault();
        coupon_discount();
    });

    $(".extra-discount").on('click', function () {
        extra_discount();
    });

    $('.type_ext_dis').on('change', function () {
        limit(this);
    });

    $('.payment-opp').on('change', function () {
        payment_option(this);
    });
    $('.prevent-default').on('click', function (e) {
        if (e.target.tagName == 'BUTTON') {
            return;
        }
        e.stopPropagation();
    })

    function calculateChange() {
        let paidAmount = parseFloat(document.getElementById('paid_amount').value) || 0;
        let totalAmount = parseFloat({{ $totalAmount }});
        let change = paidAmount - totalAmount;
        let changeAmount = change.toFixed(2) + '{{ \App\CPU\Helpers::currency_symbol() }}';

        document.getElementById('change_amount').textContent = changeAmount;
    }

    var slider = new Swiper('.swiper-js', {
        slidesPerView: 'auto',
        spaceBetween: 10,
        nav: true,
        navigation: {
            nextEl: '.swiper-next',
            prevEl: '.swiper-prev',
        },
    });
    var coupon_slider = new Swiper('.coupon-slider', {
        slidesPerView: 'auto',
        spaceBetween: 10,
        nav: true,
        navigation: {
            nextEl: '.swiper-next',
            prevEl: '.swiper-prev',
        },
    });


    $('.coupon-slider-item').on('click', function () {
        $('.coupon-slider-button').removeClass('active');
        $(this).find('.coupon-slider-button').addClass('active');
        $('input[name="coupon_code"]').val($(this).find('.left h6').text().split(' ')[2]);
    })

    $(document).on('click', function () {
        if (!$(event.target).closest('.coupon-modal').length) {
            $('.coupon-modal').removeClass('show');
        }
    })

    $('.counter-container-div').each(function () {

        let container = $(this);

        container.find('.btn-increment').click(function () {
            console.log(213)
            let input = container.find('.counter-input');
            let currentVal = parseInt(input.val());
            if (!isNaN(currentVal)) {
                input.val(currentVal + 1);
                updateQuantity(container.data('id'), input.val());
            }
        });

        container.find('.btn-decrement').click(function () {
            let input = container.find('.counter-input');
            let currentVal = parseInt(input.val());
            if (!isNaN(currentVal) && currentVal > 1) {
                input.val(currentVal - 1);
                updateQuantity(container.data('id'), input.val());
            }
        });

        container.find('.counter-input').on('input', function () {
            let input = $(this);
            let value = parseInt(input.val());
            if (isNaN(value) || value < 1) {
                input.val(1);
            }
            updateQuantity(container.data('id'), input.val());
        });

    });

    $(document).ready(function () {
        $('.dropdown-Select2').select2({
            dropdownParent: $('.prevent-default'),
            minimumResultsForSearch: -1
        });

        function showClearCartButton() {
            let section = `
                                        <div class="text-dark fs-13 font-weight-bold cart-product-list">Cart Product List</div>
                                        <button type="button" class="btn btn-soft-danger rounded empty-cart">
                                            Clear Cart
                                        </button>
            `

            if ($('.pos-cart-table').find('.cart-product-item').length > 0) {
                if ($('.cart-section').find('.empty-cart').length == 0 && $('.cart-section').find('.cart-product-list').length == 0) {
                    $('.cart-section').append(section);
                }
                $(".empty-cart").on('click', function () {
                    emptyCart();
                });
            } else {
                $('.cart-section').find('.empty-cart').remove();
                $('.cart-section').find('.cart-product-list').remove();
            }
        }


        showClearCartButton();

    })

    $(document).ready(function () {

        function initializedCustomer() {
            $.ajax({
                url: '{{route('admin.pos.selected-customer')}}',
                type: 'GET',

                dataType: 'json',

                success: function (data) {
                    if (data.user_type == 1) {
                        $('#customer').val(data.customer_id); // This will select the correct option by value
                        $('#wallet-payment-section').removeClass('d-none')
                    } else {
                        $('#wallet-payment-section').addClass('d-none')
                    }
                },

            });
        }

        initializedCustomer();

        function updatePaymentMethod(selectedMethod) {
            $('#payment_method_input').val(selectedMethod);

            if (selectedMethod == 0) {
                let customerId = $('#customer').val();

                $.ajax({
                    url: '{{route('admin.pos.customer-balance')}}',
                    type: 'GET',
                    data: {
                        customer_id: customerId
                    },
                    dataType: 'json',
                    success: function (data) {
                        let balance = parseFloat(data.customer_balance);

                        // Sanitize order total
                        let order_total = parseFloat($('#total_price').text().replace(/[^0-9.-]+/g, ''));

                        let remain_balance = balance - order_total;

                        $('#customer_balancee').val(balance.toFixed(2));
                        $('#remaining_balancee').val(remain_balance.toFixed(2));
                    },
                });
            }
        }

        let initialMethod = $('input[name="payment_method"]:checked').val();
        if (initialMethod) {
            updatePaymentMethod(initialMethod);
        }

        $('.change_payment_method').on('click', function () {
            let selectedMethod = $(this).find('input[type="radio"]').val();
            updatePaymentMethod(selectedMethod);
        });
    });


    function submitOrder() {
        let paymentMethod = $('#payment_method_input').val();

        let paidAmount = 0;
        if (paymentMethod == 1) {
            paidAmount = $('#paid_amount').val();
        } else {
            paidAmount = $('#total_price').text();
        }
        let cardNumber = $('#card_number').val();
        let transactionReference = $('#transaction_ref').val();
        let phoneOrEmail = $('#phone_or_email').val();
        let comment = $('#commentTextArea').val();
        let remainingBalance = $('#remaining_balancee').val();

        // Check if the payment method is "Cash" (assuming value "1" is Cash) and paid_amount is required
        if (paymentMethod == 1 && !paidAmount) {
            Swal.fire({
                icon: "error",
                title: "Paid Amount is empty",
                text: "Please enter the paid amount"
            });
            return;
        }
        if (paymentMethod == 1 && (paidAmount < {{ $totalAmount }})) {
            Swal.fire({
                icon: "error",
                title: "Paid Amount is less than order amount",
                text: "Paid Amount can't be less than order amount"
            });
            return;
        }

        if (paymentMethod != 1 && paymentMethod != 0) {
            if (!cardNumber) {
                Swal.fire({
                    icon: "error",
                    title: "Card No is empty",
                    text: "Please enter the Card Number"
                });
                return;
            }
        }

        $('#paid_amount_input').val(paidAmount);
        $('#cardNumber').val(cardNumber);
        $('#transactionReference').val(transactionReference);
        $('#phoneOrEmail').val(phoneOrEmail);
        $('#comment').val(comment);
        $('#remainingBalance').val(remainingBalance);

        $('#orderForm').submit();
    }

    document.getElementById('dis_amount').addEventListener('keypress', function (event) {
        if (event.key === '-' || event.key === 'e') {
            event.preventDefault();
        }
    });

</script>
