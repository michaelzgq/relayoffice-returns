<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>{{\App\CPU\translate('Add to cart page')}}</title>
    @php($favIcon=\App\Models\BusinessSetting::where(['key'=>'fav_icon'])->first()->value)
    <link rel="shortcut icon" href="{{asset('storage/shop').'/' . $favIcon }}">
    <link rel="stylesheet" href="{{ asset('assets/admin') }}/vendor/icon-set/style.css">
    <link rel="stylesheet" href="{{ asset('assets/admin') }}/flaticon-font/css/uicons-regular-rounded.css">
    <link rel="stylesheet" href="{{ asset('assets/admin') }}/flaticon-font/css/uicons-solid-rounded.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/google-fonts.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/plugins/swiper.min.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/theme.minc619.css?v=1.0">
    @stack('css_or_js')
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/custom.css"/>
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/pos.css"/>
    <link rel="stylesheet" href="{{ asset('assets/admin') }}/css/custom-2.css"/>
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/toastr.css">

    <style>
        .text-decoration {
            text-decoration: line-through;
        }
    </style>

    <script defer>
        document.addEventListener('DOMContentLoaded', function () {

            function isMiniSidebarHandle() {
                if (!localStorage.getItem('isMiniSidebar')) {
                    $("body").addClass('footer-offset m-0 footer-offset has-navbar-vertical-aside navbar-vertical-aside-show-xl navbar-vertical-aside-mini-mode');
                } else {
                    $("body").addClass('footer-offset m-0 footer-offset has-navbar-vertical-aside navbar-vertical-aside-show-xl');
                }
            }

            isMiniSidebarHandle();
        });
    </script>
</head>
<body class="footer-offset navbar-vertical-aside-mini-mode m-0">
<div class="direction-toggle">
    <i class="tio-settings"></i>
    <span></span>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="loading" class="d-none">
                <div class="style-i1">
                    <img width="200" src="{{asset('assets/admin/img/loader.gif')}}"
                         alt="{{\App\CPU\translate('loader gif')}}">
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.admin.partials._header')
@include('layouts.admin.partials._sidebar')

<main id="content" role="main" class="main pointer-event">
    <section class="section-content pt-2">
        <div class="container-fluid px-3">
            <div class="d-flex flex-wrap gap-2">
                <div class="order--pos-left gap-2">
                    <div class="card">
                        <div class="card-header pb-0 border-0 px-3">
                            <h4 class="m-0">Search or Scan</h4>
                            <div class="text-dark">
                                {{ \App\CPU\translate('Product isn’t in the List') }}? <a
                                    href="{{ route('admin.product.add') }}" target="_blank"
                                    class="text-danger">{{ \App\CPU\translate('Add New') }}<i class="tio-add"></i></a>
                            </div>
                        </div>
                        <div class="card-body p-3 pt-2">
                            <div class="d-flex flex-wrap gap-3">
                                <div class="input-group-overlay input-group-merge input-group-custom flex-grow-1">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="tio-search"></i>
                                        </div>
                                    </div>
                                    <input id="search" autocomplete="off" type="text" name="search"
                                           class="form-control search-bar-input pos-search"
                                           placeholder="{{\App\CPU\translate('Search product  name or scan barcode')}}"
                                           aria-label="Search here">
                                    <diV class="pos-search-card w-4 position-absolute z-index-1 w-100">
                                        <div id="search-box" class="card card-body search-result-box d--none p-2"></div>
                                    </diV>
                                </div>
                                <div class="d-flex flex-end position-relative show-filter-count">
                                    <button class="btn btn-white" type="button" id="filterMenuToggle"
                                            aria-controls="offcanvasFilterMenu" aria-expanded="false"
                                            aria-label="Toggle filter menu">
                                        {{\App\CPU\translate('Filters')}}
                                        <i class="tio-filter-list"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card flex-grow-1">
                        <div class="card-body d-flex flex-column px-2 py-3" id="items">
                            @if(count($products)==0)
                                <div
                                    class="text-center p-4 d-flex flex-column justify-content-center align-items-center flex-grow-1">
                                    <img class="mb-3 w-i5"
                                         src="{{asset('assets/admin')}}/svg/illustrations/sorry.svg"
                                         alt="{{\App\CPU\translate('Image Description')}}">
                                    <p class="mb-0">{{ \App\CPU\translate('No_data_to_show')}}</p>
                                </div>
                            @else
                                <div class="pos-item-wrap">
                                    @foreach($products as $product)
                                        @include('admin-views.pos._single_product',['product'=>$product])
                                    @endforeach
                                </div>
                            @endif

                            <div class="table-responsive mt-3">
                                <div class="px-4 d-flex justify-content-lg-end">
                                    {!!$products->withQueryString()->links()!!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @php($customers = \App\Models\Customer::get())
                <div class="order--pos-right">
                    <div class="card billing-section-wrap">
                        <div class="billing-section-wrap-title">
                            <h4 class="p-3 bg-light text-center">{{\App\CPU\translate('Billing_Section')}}</h4>
                        </div>
                        <div>
                            <div class="card-body pt-1">
                                <div class="mb-3">
                                    <label
                                        class="input-label font-weight-medium text-capitalize">{{ \App\CPU\translate('counter number') }}</label>
                                    <select id='counter_number' name="counter_number"
                                            class="form-control js-select2-custom change-counter">
                                        @if(count($counters) > 0)
                                            @foreach($counters as $counter)
                                                <option
                                                    value="{{$counter->id}}" {{ session('counter_id') == $counter->id ? 'selected' : '' }}>{{ $counter->number }}
                                                    ({{ $counter->name }})
                                                </option>
                                            @endforeach
                                        @else
                                            <option value=""> {{\App\CPU\translate('No Counter Found')}}</option>
                                        @endif

                                    </select>
                                </div>
                                <div class="mb-2 text-right">
                                    <a class="text-primary text-decoration-underline" id="add_new_customer"
                                       type="button" data-toggle="modal" data-target="#add-customer"><i
                                            class="tio-add"></i>{{ \App\CPU\translate('Add New Customer') }}</a>
                                </div>
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="flex-grow-1">
                                        <select id='customer' name="customer_id"
                                                class="form-control js-data-example-ajax customer-change">
                                            <option>{{\App\CPU\translate('--select-customer--')}}</option>
                                            <option value="0">{{\App\CPU\translate('Walk-In_Customer')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="card bg-soft-primary shadow-none border-0 mb-3">
                                        <div class="card-body text-dark fs-13 d-flex flex-column gap-2 py-2">
                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                <img src="{{asset('assets/admin/img/fi-rr-user.svg')}}" alt="">
                                                <span id="current_customer"></span>
                                            </div>
                                            <div class="customer-phone-section d-none">
                                                <div class="d-flex flex-wrap align-items-center gap-2">
                                                    <img src="{{asset('assets/admin/img/phone-flip.svg')}}"
                                                         alt="">
                                                    <span id="current_customer_phone"></span>
                                                </div>
                                            </div>
                                            <div class="customer-wallet-section d-none">
                                                <div class="d-flex flex-wrap align-items-center gap-2">
                                                    <img src="{{asset('assets/admin/img/wallet-icon.svg')}}"
                                                         alt="">
                                                    <span id="current_customer_balance">{{ \App\CPU\translate('Wallet Balance') }}: <strong></strong></span>{{\App\CPU\Helpers::currency_symbol() }}
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <div class="d-flex align-items-center justify-content-between cart-section">

                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <div id="cartloader" class="d-none">
                                <img width="50" src="{{asset('assets/admin/img/loader.gif')}}">
                            </div>
                        </div>
                        <div id="cart">
                            {!! $cartView !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="overlay" id="overlay"></div>

    {{-- Hold Offcancvas --}}
    @include('admin-views.pos.partials._hold')


    {{-- Filter Offcancvas --}}
    @include('admin-views.pos.partials._filter')

    {{-- Product Details Modal --}}
    <div class="modal fade product-details-modal " id="quick-view" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" id="quick-view-modal">

            </div>
        </div>
    </div>
    @php($order=\App\Models\Order::find(session('last_order')))
    @if($order)
        @php(session(['last_order'=> false]))
        {{-- Print Invoice Offcanvas --}}
        <div class="overlay" id="overlay"></div>
        <div class="offcanvas-filter filter-offcanvas" id="print-invoice">
            <div
                class="offcanvas-filter__header border-bottom px-3 d-flex justify-content-between align-items-center gap-3">
                <h4>{{\App\CPU\translate('Print_Invoice')}}</h4>
                <button type="button" class="btn btn-light icon-btn rounded-circle close-print-invoice"
                        aria-label="Close">
                    <i class="fi fi-rr-cross-small fs-16"></i>
                </button>
            </div>
            <form action="{{ url()->current() }}" method="GET">
                <div class="offcanvas-filter__body">
                    <div class="row m-auto" id="printableArea">
                        @include('admin-views.pos.invoice')
                    </div>
                </div>
                <div class="offcanvas-filter__footer bg-white py-3 d-flex align-items-center h-auto">
                    <div class="d-flex justify-content-center align-items-center flex-wrap gap-3 w-100">
                        <a id="invoice_close" data-route="{{url()->previous()}}"
                           class="btn btn-light min-w-120px fw-semibold non-printable invoice-close close-print-invoice">{{\App\CPU\translate('back')}}</a>

                        <button id="print_invoice" type="button"
                                class="btn btn-primary min-w-120px fw-semibold non-printable print-div"
                                data-name="printableArea">
                            {{\App\CPU\translate('Proceed, If thermal printer is ready.')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @endif
    <input type="hidden" id="seeMoreCategory" name="see-more-category" value="{{ $categories->count() - 6 }}">
    <input type="hidden" id="seeMoreBrand" name="see-more-brand" value="{{ $brands->count() - 6 }}">
    <input type="hidden" id="seeMoreSubcategory" name="see-more-subcategory" value="{{ $subcategories->count() - 6 }}">
    <input type="hidden" name="walking_customer" id="currentCustomer"
           value="{{\App\CPU\translate('Walk-In_Customer')}}">
</main>

<script src="{{asset('assets/admin')}}/js/custom.js"></script>

<script src="{{asset('assets/admin')}}/js/vendor.min.js"></script>
<script src="{{asset('assets/admin')}}/js/theme.min.js"></script>
<script src="{{asset('assets/admin')}}/js/sweet_alert.js"></script>
<script src="{{asset('assets/admin')}}/js/toastr.js"></script>
<script src="{{asset('assets/admin')}}/plugins/swiper.min.js"></script>
<script src="{{asset('assets/admin')}}/js/pos.js"></script>
<script src="{{asset('assets/admin')}}/js/app-page.js"></script>
{!! Toastr::message() !!}

@include('admin-views.pos.partials._script')
@stack('script_2')


</body>
</html>
