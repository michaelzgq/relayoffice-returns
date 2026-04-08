@extends('layouts.admin.app')

@section('title', \App\CPU\translate($type) . ' Orders')

@push('css_or_js')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" href="{{ asset('assets/admin') }}/css/pos.css"/>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-sm">
                <h1 class="page-header-title text-capitalize">
                    {{ \App\CPU\translate($type) . ' ' . \App\CPU\translate('Order List') }}
                    <span
                        class="badge bg-primary ml-2 rounded-full text-white fs-10 count-order-details">{{ $orderCount }}</span>
                </h1>
            </div>
        </div>
        <div class="card">
            <div class="card-header border-0">
                <div class="d-flex justify-content-between align-items-start flex-wrap flex-grow-1 gap-2">
                    <div class="d-flex flex-wrap flex-grow-1 flex-lg-grow-0 gap-2">
                        <div class="input-group-overlay input-group-merge input-group-custom flex-grow-1">
                            <div class="input-group-prepend">
                                <div class="input-group-text lh-1 mt-1">
                                    <i class="fi fi-rr-search"></i>
                                </div>
                            </div>
                            <input id="search" autocomplete="off" type="text" name="search"
                                   class="form-control search-bar-input bg-fbfdfe"
                                   placeholder="{{ \App\CPU\translate('Search by Order id') . ', ' . \App\CPU\translate('customer info') }}"
                                   aria-label="Search here" value="{{ request()->search }}">
                            <diV class="pos-search-card w-4 position-absolute z-index-1">
                                <div id="search-box" class="card card-body search-result-box d--none p-2"></div>
                            </diV>
                        </div>
                        <?php
                        $startDateTime = request()->get('start_date', now()->subDays(29)->toDateString());
                        $endDateTime = request()->get('end_date', now()->toDateString());
                        ?>
                        <button type="button" class="btn btn-white flex-grow-1 d-flex gap-10 align-items-center dateRange">
                            <img class="svg" src="{{ asset('assets/admin/img/clock.svg') }}" alt="">
                            <span></span>
                        </button>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-end justify-content-xl-between align-items-start gap-2">
                            <div class="d-flex flex-end position-relative show-filter-count">
                                <button
                                    class="btn btn-white d-flex align-items-center justify-content-center gap-3 px-4 flex-grow-1 h-44px-mobile"
                                    type="button" id="filterMenuToggle"
                                    aria-controls="offcanvasFilterMenu" aria-expanded="false"
                                    aria-label="Toggle filter menu">
                                   <span class="d-none d-sm-block"> {{ \App\CPU\translate('Filters') }}</span>
                                    <i class="fi fi-rr-bars-filter fs-16 lh-1"></i>
                                </button>
                            </div>
                            <div class="d-flex gap-2 flex-wrap flex-end">
                                <a href="javascript:" onclick="exportOrders()"
                                   class="btn btn-white text-primary d-flex align-items-center justify-content-center gap-2 flex-grow-1"
                                   type="button">
                                   <span class="d-none d-sm-block fs-13"> {{ \App\CPU\translate('Export') }}</span>
                                    <img src="{{ asset('assets/admin/img/download-new.svg') }}" alt="" class="svg">
                                </a>
                                <a href="{{ route('admin.order.list', ['type' => $type]) }}"
                                   class="btn btn-soft-primary d-flex align-items-center justify-content-center gap-2 flex-grow-1 lh-1"
                                   type="button">
                                    <span class="d-none d-sm-block"> {{ \App\CPU\translate('Refresh') }}</span>
                                    <i class="fi fi-rr-refresh fs-16"></i>
                                </a>
                                <div class="hs-unfold">
                                    <a class="js-hs-unfold-invoker btn btn-soft-danger p-2 w-40" href="javascript:void(0)"
                                       data-hs-unfold-options='{
                                            "target": "#showHideDropdown",
                                            "type": "css-animation"
                                        }'>
                                        <img width="20" class="svg"
                                             src="{{ asset('assets/admin/img/column.svg') }}" alt=""/>
                                    </a>

                                    <div id="showHideDropdown"
                                         class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right dropdown-card min-w-340">
                                        <div class="card card-sm">
                                            <div class="card-header">
                                                <div>
                                                    <h5 class="modal-title">{{ \App\CPU\translate('Colum View') }}</h5>
                                                    <p class="fs-12 mb-0">{{ \App\CPU\translate('You can control the column view by turning the
                                                        toggle on or off.') }}</p>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="overflow-y-auto max-h-100vh-500px max-h-lg-100vh-400px">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="mr-2 fs-13 title text-capitalize">Order ID</span>

                                                        <!-- Checkbox Switch -->
                                                        <label class="toggle-switch toggle-switch-sm"
                                                               for="toggleColumn_order_id">
                                                            <input type="checkbox" class="toggle-switch-input"
                                                                   id="toggleColumn_order_id" checked>
                                                            <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                            </span>
                                                        </label>
                                                        <!-- End Checkbox Switch -->
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="mr-2 fs-13 title text-capitalize">Order Date</span>

                                                        <!-- Checkbox Switch -->
                                                        <label class="toggle-switch toggle-switch-sm"
                                                               for="toggleColumn_order_date">
                                                            <input type="checkbox" class="toggle-switch-input"
                                                                   id="toggleColumn_order_date" checked>
                                                            <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                            </span>
                                                        </label>
                                                        <!-- End Checkbox Switch -->
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span
                                                            class="mr-2 fs-13 title text-capitalize">Customer Info</span>

                                                        <!-- Checkbox Switch -->
                                                        <label class="toggle-switch toggle-switch-sm"
                                                               for="toggleColumn_customer_info">
                                                            <input type="checkbox" class="toggle-switch-input"
                                                                   id="toggleColumn_customer_info" checked>
                                                            <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                            </span>
                                                        </label>
                                                        <!-- End Checkbox Switch -->
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span
                                                            class="mr-2 fs-13 title text-capitalize">Counter Info</span>

                                                        <!-- Checkbox Switch -->
                                                        <label class="toggle-switch toggle-switch-sm"
                                                               for="toggleColumn_counter_info">
                                                            <input type="checkbox" class="toggle-switch-input"
                                                                   id="toggleColumn_counter_info" checked>
                                                            <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                            </span>
                                                        </label>
                                                        <!-- End Checkbox Switch -->
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span
                                                            class="mr-2 fs-13 title text-capitalize">Items</span>

                                                        <!-- Checkbox Switch -->
                                                        <label class="toggle-switch toggle-switch-sm"
                                                               for="toggleColumn_items">
                                                            <input type="checkbox" class="toggle-switch-input"
                                                                   id="toggleColumn_items" checked>
                                                            <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                            </span>
                                                        </label>
                                                        <!-- End Checkbox Switch -->
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span
                                                            class="mr-2 fs-13 title text-capitalize">Order Amount</span>

                                                        <!-- Checkbox Switch -->
                                                        <label class="toggle-switch toggle-switch-sm"
                                                               for="toggleColumn_order_amount">
                                                            <input type="checkbox" class="toggle-switch-input"
                                                                   id="toggleColumn_order_amount" checked>
                                                            <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                            </span>
                                                        </label>
                                                        <!-- End Checkbox Switch -->
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="mr-2 fs-13 title text-capitalize">Discount</span>

                                                        <!-- Checkbox Switch -->
                                                        <label class="toggle-switch toggle-switch-sm"
                                                               for="toggleColumn_discount">
                                                            <input type="checkbox" class="toggle-switch-input"
                                                                   id="toggleColumn_discount" checked>
                                                            <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                            </span>
                                                        </label>
                                                        <!-- End Checkbox Switch -->
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="mr-2 fs-13 title text-capitalize">Vat/Tax</span>

                                                        <!-- Checkbox Switch -->
                                                        <label class="toggle-switch toggle-switch-sm"
                                                               for="toggleColumn_vat_tax">
                                                            <input type="checkbox" class="toggle-switch-input"
                                                                   id="toggleColumn_vat_tax" checked>
                                                            <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                            </span>
                                                        </label>
                                                        <!-- End Checkbox Switch -->
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span
                                                            class="mr-2 fs-13 title text-capitalize">Total Amount</span>

                                                        <!-- Checkbox Switch -->
                                                        <label class="toggle-switch toggle-switch-sm"
                                                               for="toggleColumn_total_amount">
                                                            <input type="checkbox" class="toggle-switch-input"
                                                                   id="toggleColumn_total_amount" checked>
                                                            <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                            </span>
                                                        </label>
                                                        <!-- End Checkbox Switch -->
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="mr-2 fs-13 title text-capitalize">Paid By</span>

                                                        <!-- Checkbox Switch -->
                                                        <label class="toggle-switch toggle-switch-sm"
                                                               for="toggleColumn_paid_by">
                                                            <input type="checkbox" class="toggle-switch-input"
                                                                   id="toggleColumn_paid_by" checked>
                                                            <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                            </span>
                                                        </label>
                                                        <!-- End Checkbox Switch -->
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span
                                                            class="mr-2 fs-13 title text-capitalize">order status</span>

                                                        <!-- Checkbox Switch -->
                                                        <label class="toggle-switch toggle-switch-sm"
                                                               for="toggleColumn_order_status">
                                                            <input type="checkbox" class="toggle-switch-input"
                                                                   id="toggleColumn_order_status" checked>
                                                            <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                            </span>
                                                        </label>
                                                        <!-- End Checkbox Switch -->
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="mr-2 fs-13 title text-capitalize">Action</span>

                                                        <!-- Checkbox Switch -->
                                                        <label class="toggle-switch toggle-switch-sm"
                                                               for="toggleColumn_action">
                                                            <input type="checkbox" class="toggle-switch-input"
                                                                   id="toggleColumn_action" checked>
                                                            <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                            </span>
                                                        </label>
                                                        <!-- End Checkbox Switch -->
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
            </div>
            <div class="list-table-data">
                @include('admin-views.orders.partials._list-table-data')
            </div>
        </div>
    </div>

    <div class="overlay" id="overlay"></div>

    <div class="show-order-items-menu">
        @include('admin-views.orders.partials._show-order-items-menu')
    </div>

    @include('admin-views.orders.partials._list-filter-box')

    <div class="loading-overlay text-center">
        <div id="cartloader">
            <img width="50" src="{{asset('assets/admin/img/loader.gif')}}">
        </div>
    </div>
    @include('admin-views.orders.partials._print-invoice-modal')

    <span class="data-to-js"
          data-type="{{ $type }}"
          data-date-placeholder="{{ \App\CPU\translate('Select Date') }}"
          data-export-route="{{ route('admin.order.export') }}"
          data-search-route="{{ route('admin.order.search') }}"
    >

    </span>
@endsection

@push('script_2')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{ asset('assets/admin/js/orders/list.js') }}"></script>
    <script src={{asset("assets/admin/js/global.js")}}></script>
    <script>
        printFilterCount(['type', 'search', 'start_date', 'end_date', 'page']);

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

    </script>
@endpush
