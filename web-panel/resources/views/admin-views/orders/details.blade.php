@extends('layouts.admin.app')

@php
    use function App\CPU\translate;
    use \App\Enums\Order\OrderStatus;
    $currency = App\CPU\Helpers::currency_symbol();
@endphp

@section('title', translate('order_details'))

@section('content')
    <div class="content container-fluid">
        <h1 class="page-header-title text-capitalize mb-3">
            {{ translate('Order Details') }}
        </h1>
        <div class="row g-2">
            <div class="col-lg-9">
                <div class="card mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex gap-3 flex-wrap justify-content-between align-items-center mb-4">
                            <div class="flex-grow-1">
                                <h3 class="text-primary fw-bold">{{ translate('ID') }} #{{ $order?->id }}</h3>
                                <h6><span
                                        class="text-black-50">{{ translate('Date') }} :</span> {{ $order?->created_at->format('d M, Y') }},
                                    <span class="font-weight-normal">{{ $order?->created_at->format('h:i a') }}</span>
                                </h6>
                            </div>
                            <div class="d-flex gap-10 flex-wrap flex-grow-1 flex-lg-grow-0">
                                @if($order?->order_status != OrderStatus::REFUNDED)
                                    <button class="btn btn-white text-primary flex-grow-1" type="button"
                                            data-toggle="modal"
                                            data-target="#order_refund_modal">
                                        {{ translate('Refund Order') }}
                                    </button>
                                @endif
                                <button
                                    class="btn btn-primary flex-grow-1 d-flex gap-2 align-items-center justify-content-center download-invoice cursor-pointer"
                                    data-url="{{ route('admin.pos.invoice', $order?->id) }}"
                                    type="button">
                                    {{translate('print')}}
                                    <i class="tio-print"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row g-3">
                            @if($refundedOrder)
                                <div class="col-md-12">
                                    <div class="bg-light h-100">
                                        <div class="p-3">
                                            <h5 class="text-uppercase mb-0">{{ translate('Refund Info') }}</h5>
                                        </div>
                                        <hr class="m-0">
                                        <div class="row px-3 pt-3 pb-3">
                                            <div class="col-md-6">
                                                <h6 class="d-flex gap-3 mb-2">
                                            <span class="min-w-120px d-flex gap-2 align-items-center">
                                                {{ translate('Refund Date') }}
                                            </span>
                                                    <span class="d-flex align-items-center gap-3">:</span>
                                                    <span
                                                        class="font-weight-lighter"> {{ $refundedOrder?->created_at->format('d M, Y') . ' ' . $refundedOrder?->created_at->format('h:i a') }}</span>
                                                </h6>

                                                <h6 class="d-flex gap-3 mb-0">
                                            <span class="min-w-120px d-flex gap-2 align-items-center">
                                                <span>{{ translate('Refund Amount') }}</span>
                                            </span>
                                                    <span class="d-flex align-items-center gap-3">:</span>
                                                    <span class="font-weight-lighter">
                                                {{ $currency . ' ' . number_format($refundedOrder?->refund_amount, 2) }}
                                            </span>
                                                </h6>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="d-flex gap-3 mb-2">
                                                    <span class="min-w-220px d-flex gap-2 align-items-center">
                                                        {{ translate('Refund Given From (Admin Account)') }}
                                                    </span>
                                                    <span class="d-flex align-items-center gap-3">:</span>
                                                    <span
                                                        class="font-weight-lighter">
                                                        {{ $refundedOrder?->admin_payment_method_name }}
                                                    </span>
                                                </h6>

                                                <h6 class="d-flex gap-3 mb-2">
                                                    <span class="min-w-220px d-flex gap-2 align-items-center">
                                                        <span>{{ translate('Refund Given To Customer Via') }}</span>
                                                    </span>
                                                    <span class="d-flex align-items-center gap-3">:</span>
                                                    <span class="font-weight-lighter">
                                                        {{ ucwords($refundedOrder?->customer_payout_method_name) }}
                                                    </span>
                                                </h6>
                                                @if($refundedOrder?->customer_payout_method_name === 'other')
                                                    <span class="fs-12">{{ translate('Payment Method') }} - {{ $refundedOrder->other_payment_details['payment_method'] }}</span><br>
                                                @if(!is_null($refundedOrder->other_payment_details['payment_info']))
                                                        <span class="fs-12">{{ translate('Payment Info') }} - {{ $refundedOrder->other_payment_details['payment_info'] }}</span>
                                                @endif
                                                @endif
                                            </div>
                                        </div>
                                        @if($refundedOrder?->refund_reason)
                                            <div class="row px-3 pt-1 pb-3">
                                                <div class="col-md-12">
                                                    <div class="d-flex align-items-center ">
                                                        <div class="bg-white p-3 w-100">
                                                            <h6 class="d-flex gap-3 mb-3">
                                                                <span
                                                                    class="text-primary font-weight-bold">{{ translate('Refund Note:') }}</span>
                                                            </h6>
                                                            <div
                                                                class="d-flex gap-3">{{ $refundedOrder?->refund_reason }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-6">
                                <div class="bg-light h-100">
                                    <div class="p-3">
                                        <h5 class="text-uppercase mb-0">{{ translate('Order Info') }}</h5>
                                    </div>
                                    <hr class="m-0">
                                    <div class="p-3">
                                        <h6 class="d-flex gap-3 mb-3">
                                            <span class="min-w-120px d-flex gap-2 align-items-center">
                                                <span class="text-black-50">
                                                    <img class="svg"
                                                         src="{{ asset('assets/admin/img/counter.svg') }}"
                                                         alt="">
                                                </span>
                                                <span>Counter No</span>
                                            </span>
                                            <span class="d-flex align-items-center gap-3 font-weight-normal">:
                                                <span> {{ $order?->counter ? translate('counter ') . $order?->counter?->number . ' (' . $order?->counter?->name . ')' : 'N/A'  }}</span></span>
                                        </h6>

                                        <h6 class="d-flex gap-3 mb-0">
                                            <span class="min-w-120px d-flex gap-2 align-items-center">
                                                <span class="text-black-50">
                                                    <img class="svg"
                                                         src="{{ asset('assets/admin/img/lists.svg') }}"
                                                         alt="">
                                                </span>
                                                <span>{{ translate('order status') }}</span>
                                            </span>
                                            <span class="d-flex align-items-center gap-3">:
                                                <span
                                                    class="badge {{ $order?->order_status == \App\Enums\Order\OrderStatus::COMPLETED ? 'badge-soft-success' : 'badge-soft-danger' }} px-2 py-1 rounded-full">{{ $order?->order_status->label() }}</span>
                                            </span>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light h-100">
                                    <div class="p-3">
                                        <h5 class="text-uppercase mb-0">{{ translate('PAYMENT INFO') }}</h5>
                                    </div>
                                    <hr class="m-0">
                                    <div class="p-3">
                                        <h6 class="d-flex gap-3 mb-3">
                                            <span class="min-w-120px">
                                                <span>{{ translate('Reference ID') }}</span>
                                            </span>
                                            <span
                                                class="d-flex align-items-center gap-3 font-weight-normal">: <span>{{ $order?->transaction_reference ?? 'N/A' }}</span></span>
                                        </h6>
                                        <h6 class="d-flex gap-3 mb-3">
                                            <span class="min-w-120px">
                                                <span>{{ translate('Payment_Method') }}</span>
                                            </span>
                                            <span class="d-flex align-items-center gap-3">:
                                                <span class="d-flex align-items-center gap-2 flex-wrap">
                                                    <span
                                                        class="badge badge-soft-success px-2 py-1 rounded-full">{{ ($order?->account?->account == 0 ? translate('Wallet') : $order?->account?->account) ?? 'Payment method deleted'}}</span>
                                                </span>
                                            </span>
                                        </h6>
                                        <h6 class="d-flex gap-3 mb-3">
                                            <span class="min-w-120px">
                                                <span>{{ translate('Payment_Status') }}</span>
                                            </span>
                                            <span class="d-flex align-items-center gap-3">:
                                                <span class="d-flex align-items-center gap-2 flex-wrap">
                                                    <span
                                                        class="badge badge-soft-warning px-2 py-1 rounded-full">{{ translate('paid') }}</span>
                                                </span>
                                            </span>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($order?->comment)
                            <div class="bg-light rounded py-3 px-3 mt-3">
                                <h6 class="d-flex gap-2 mb-0">
                                    <span class="text-primary text-nowrap"> # {{ translate('Note') }}: </span>
                                    <span class="font-weight-normal">{{ $order?->comment ?? '' }}</span>
                                </h6>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card">
                    <div class="card-body p-3">
                        <h5 class="mb-10px text-uppercase">{{ translate('Item Summary') }}</h5>
                        <div class="table-responsive">
                            <table
                                class="table table-hover table-thead-bordered table-nowrap table-align-middle card-table mb-0">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('Item details') }}</th>
                                    <th class="text-center">{{ translate('Qty') }}</th>
                                    <th class="text-center">{{ translate('Vat/Tax') }}</th>
                                    <th class="text-center">{{ translate('Discount') }}</th>
                                    <th class="text-right">{{ translate('Total') }} <i class="fi fi-rr-info cursor-pointer" data-toggle="tooltip" title="({{ translate('without VAT/TAX and Discount') }})"></i></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order?->details as $product)
                                    @php
                                        $productInfo = json_decode($product?->product_details, true);
                                        $productImage = \App\Models\Product::find($productInfo['id'])?->image;
                                    @endphp
                                    <tr @if(!\App\Models\Product::find($productInfo['id'])) class="disabled-tr" data-toggle="tooltip" title="{{ translate('this_product_is_unavailable') }}" @endif>
                                            <td>
                                                {{ $loop->iteration }}
                                            </td>
                                            <td>
                                                <div class="media">
                                                    <div class="avatar mr-2">
                                                        <img width="40" height="40" class="avatar-img onerror-image"
                                                             src="{{ onErrorImage($productImage,asset('storage/product/' . $productImage) ,asset('assets/admin/svg/components/product-default.svg') ,'product/') }}"
                                                             alt="Image Description">
                                                    </div>
                                                    <div class="media-body">
                                                        <h6 class="line-limit-1 mb-1 text-wrap min-w-190px">
                                                            <a href="{{ route('admin.product.show', $productInfo['id']) }}" target="_blank">
                                                                {{ $productInfo['name'] }}
                                                            </a>
                                                        </h6>
                                                        <h6 class="mb-1"><span class="text-black-50">{{ translate('Unit Price') }} :</span>
                                                            {{ $currency . ' '. number_format($product['price'], 2) }}
                                                        </h6>
                                                        <h6 class="mb-1"><span class="text-black-50">Unit :</span>
                                                            {{ $productInfo['unit_value'] ?? '' }}
                                                            {{ \App\Models\Unit::find($productInfo['unit_type'])?->unit_type ?? 'N/A' }}
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <h6>{{ $product?->quantity }}</h6>
                                            </td>
                                            <td class="text-center">
                                                <h6>{{ $currency . number_format($product?->tax_amount * $product?->quantity ?? 0, 2) }}</h6>
                                            </td>
                                            <td class="text-center">
                                                <h6>{{ $currency . number_format($product?->discount_on_product * $product?->quantity ?? 0, 2) }}</h6>
                                            </td>
                                            <td class="text-right">
                                                <h6>{{ $currency . number_format(($product?->price * $product?->quantity) , 2) }}
                                                </h6>
                                            </td>
                                        </tr>
                                @endforeach
                                    {{-- disabled tr static design --}}
                                </tbody>
                            </table>
                        </div>
                        <div class="mx-3">
                            <hr>
                        </div>
                        <div class="row justify-content-md-end mx-0">
                            <div class="col-12">
                                <h5 class="mb-0 text-uppercase">{{ translate('Billing Summary') }}</h5>
                            </div>
                            <div class="col-lg-6">
                                <dl class="row pt-2 pt-lg-0 g-lg-0">
                                    <dt class="col-6 font-weight-normal">{{ translate('Subtotal') }}</dt>
                                    <dd class="col-6 h5 text-end">
                                        {{ $currency . ' '. number_format($order?->details->map(function($product){ return  $product?->price * $product?->quantity;})->sum() ?? 0, 2) }}
                                    </dd>
                                    <dt class="col-6 font-weight-normal">
                                        {{ translate('Product Discount') }}
                                    </dt>
                                    <dd class="col-6 text-end">
                                        - {{ $currency . ' '. number_format($order?->details->map(function($product){  return  $product->discount_on_product * $product?->quantity;})->sum() ?? 0, 2) }}
                                    </dd>
                                    <dt class="col-6 font-weight-normal">{{ translate('Coupon Discount') }}</dt>
                                    <dd class="col-6 text-end">
                                        - {{ $currency . ' '. number_format($order?->coupon_discount_amount ?? 0, 2) }}
                                    </dd>
                                    <dt class="col-6 font-weight-normal">{{ translate('Extra Discount') }}</dt>
                                    <dd class="col-6 text-end">
                                        - {{ $currency . ' '. number_format($order?->extra_discount ?? 0, 2) }}
                                    </dd>
                                    <dt class="col-6 font-weight-normal">{{ translate('Vat/Tax') }}</dt>
                                    <dd class="col-6 text-end">
                                        + {{ $currency . ' '. number_format($order?->total_tax, 2)}}
                                    </dd>

                                    <dd class="col-12 border-top-dashed mt-2 mb-4"></dd>

                                    <dt class="col-6 h4">{{ translate('Total') }}</dt>
                                    <dd class="col-6 text-end h4">
                                        {{ $currency . ' ' . number_format($order?->order_amount + $order?->total_tax - ($order?->extra_discount ?? 0) - ($order?->coupon_discount_amount ?? 0), 2) }}</dd>
                                    <dt class="col-6 font-weight-normal">{{ translate('Cash') }}</dt>
                                    <dd class="col-6 text-end">{{ $currency . ' ' . number_format($order?->collected_cash ?? 0, 2) }}</dd>
                                    <dt class="col-6 h4">{{ translate('Paid') }}</dt>
                                    <dd class="col-6 text-end h4">{{ $currency . ' ' . number_format($order?->collected_cash ?? 0, 2) }}</dd>

                                    @if(number_format($order->collected_cash - $order->order_amount - $order->total_tax + ($order->extra_discount ?? 0) + ($order->coupon_discount_amount ?? 0), 2) != 0)
                                        <dt class="col-6 font-weight-normal">{{ translate('Change Amount') }}</dt>
                                        <dd class="col-6 text-end">{{ $currency . ' ' . number_format($order->collected_cash - $order->order_amount - $order->total_tax + ($order->extra_discount ?? 0) + ($order->coupon_discount_amount ?? 0) , 2) }}</dd>
                                    @endif
                                    @if($refundedOrder)
                                        <dt class="col-6 font-weight-bold">{{ translate('Refund Amount') }}</dt>
                                        <dd class="col-6 text-end h4">{{ $currency . ' ' . number_format($refundedOrder?->refund_amount ?? 0, 2) }}</dd>
                                    @endif
                                </dl>
                                <!-- End Row -->
                            </div>
                        </div>
                        <!-- End Row -->
                    </div>
                </div>

            </div>
            <div class="col-lg-3">
                <div class="row g-2">
                    <div class="col-lg-12 col-md-6">
                        <div class="card mt-2 mt-lg-0">
                            <div class="card-header d-flex flex-wrap align-items-center border-0 m-2">
                                <h5 class="card-title">
                                    {{ translate('Customer_Details') }}
                                </h5>
                                @if($order?->customer?->id !=0)
                                    <h6><span class="font-weight-normal">{{ translate('Total Orders') }}</span> <span
                                            class="badge badge-danger rounded-full ml-1">{{ $order?->customer?->orders->count() ?? 0  }}</span>
                                    </h6>
                                @endif

                            </div>
                            <div class="card-body pt-0">
                                <div class="d-flex flex-column gap-2">
                                    <div class="media flex-column gap-3 bg-light rounded p-3">
                                        <div class="avatar avatar-circle avatar-lg">
                                            <img width="50" height="50" class="avatar-img onerror-image"
                                                 src="{{onErrorImage($order?->customer?->image,asset('storage/customer/' . $order?->customer?->image) ,asset('assets/admin/img/160x160/img1.jpg') ,'customer/')}}"

                                                 alt="Image Description">
                                        </div>
                                        <div class="media-body d-flex flex-column gap-3">
                                            <a href="{{ route('admin.customer.view', $order?->customer?->id) }}" class="d-flex align-items-center">
                                                <i class="tio-user mr-2"></i>
                                                <span>{{ $order?->customer?->name ?? 'N/A' }}</span>
                                            </a>
                                            @if($order?->customer?->id !=0)
                                                <a href="#" class="d-flex align-items-center">
                                                    <i class="tio-call-talking-quiet mr-2"></i>
                                                    <span>{{ $order?->customer?->mobile ?? 'N/A' }}</span>
                                                </a>

                                                <a href="#" class="d-flex align-items-center">
                                                    <i class="tio-email-outlined mr-2"></i>
                                                    <span>{{ $order?->customer?->email ?? 'N/A' }}</span>
                                                </a>
                                                <a href="#" class="d-flex align-items-center">
                                                    <i class="tio-city mr-2"></i>
                                                    <span>{{ $order?->customer?->state ?? 'N/A' }}</span>
                                                </a>
                                                <a href="#" class="d-flex align-items-center">
                                                    <img class="svg mr-2"
                                                         src="{{ asset('assets/admin/img/zipcode.svg') }}"
                                                         alt="">
                                                    <span>{{ $order?->customer?->zip_code ?? 'N/A' }}</span>
                                                </a>
                                                <a href="#" class="d-flex align-items-center">
                                                    <i class="tio-poi mr-2"></i>
                                                    <span>{{ $order?->customer?->address ?? 'N/A' }}</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin-views.orders.partials._print-invoice-modal')

    @include('admin-views.orders.partials._refund-modal')
@endsection

@push('script_2')
    <script>
        "use strict";
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
            $('.download-invoice').on('click', function () {
                let url = $(this).data('url');
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (response) {
                        closeAllOffcanvas();
                        $('#print-invoice').addClass('open');
                        $('#overlay').addClass('active');
                        // toggleBodyScroll();
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
                // toggleBodyScroll();
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
        });
        $(document).ready(function () {
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
        $(document).on('click', '.submit-refund-form', function (e) {
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
                    if (response?.errors?.length > 0)
                    {
                        response.errors.forEach(error => {
                            toastr.error(error.message);
                        });
                    } else {
                        toastr.success(response.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                },
            });
        });

        $(document).on('click', '.change_payment_method', function () {
            const input = $(this).find('input[type="radio"]');
            const name = input.attr('name');
            $(`input[name="${name}"]`).prop('checked', false);
            input.prop('checked', true).trigger('change');
            $(`input[name="${name}"]`).closest('label').removeClass('active');
            input.closest('label').addClass('active');
        });
        $(document).ready(function () {
            function toggleOtherPaymentTab() {
                const selectedValue = $('input[name="customer_payout_method"]:checked').val();
                if (selectedValue === 'other') {
                    $('#otherPayment').addClass('active show');
                } else {
                    $('#otherPayment').removeClass('active show');
                }
            }

            // Trigger on page load (in case default is "other")
            toggleOtherPaymentTab();

            // Trigger when a payment method is changed
            $('.payment-method-radio').on('change', function () {
                toggleOtherPaymentTab();
            });
        });
    </script>
    <script src={{asset("assets/admin/js/global.js")}}></script>
@endpush
