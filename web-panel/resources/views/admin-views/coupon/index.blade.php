@extends('layouts.admin.app')

@section('title',\App\CPU\translate('add_new_coupon'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/custom.css"/>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="">
            <h1 class="page-header-title d-flex align-items-center g-2px text-capitalize mb-3">
                <i class="tio-add-circle-outlined"></i>
                <span>{{\App\CPU\translate('add_new_coupon')}}</span>
            </h1>
        </div>
        <div class="row gx-2 gx-lg-3">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.coupon.store')}}" method="post" id="store-or-update-data">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{\App\CPU\translate('title')}}</label>
                                        <input type="text" name="title" value="{{ old('title') }}" class="form-control"
                                               placeholder="{{\App\CPU\translate('new_coupon')}}">
                                        <span class="error-text" data-error="title"></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between">
                                            <label class="input-label"
                                                   for="exampleFormControlInput1">{{\App\CPU\translate('coupon_code')}}</label>
                                            <a href="javascript:void(0)"
                                               class="float-right c1 fz-12 generate-code-link">{{\App\CPU\translate('generate_code')}}</a>
                                        </div>
                                        <input type="text" name="code" class="form-control" value="" id="code"
                                               placeholder="{{\Illuminate\Support\Str::random(8)}}">
                                        <span class="error-text" data-error="code"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{\App\CPU\translate('coupon_type')}} </label>
                                        <select name="coupon_type" class="form-control coupon-type-change">
                                            <option value="default">{{\App\CPU\translate('default')}}</option>
                                            <option value="first_order">{{\App\CPU\translate('first_order')}}</option>
                                        </select>
                                        <span class="error-text" data-error="coupon_type"></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6" id="limit-for-user">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{\App\CPU\translate('limit_for_same_user')}} </label>
                                        <input min="1" type="number" name="user_limit" value="{{ old('user_limit') }}"
                                               class="form-control" placeholder="{{\App\CPU\translate('EX:_10')}}">
                                        <span class="error-text" data-error="user_limit"></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{\App\CPU\translate('start_date')}} </label>
                                        <input id="start_date" type="date" name="start_date"
                                               class="form-control checkstartDate">
                                        <span class="error-text" data-error="start_date"></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{\App\CPU\translate('expire_date')}} </label>
                                        <input id="expire_date" type="date" name="expire_date"
                                               class="form-control check-date">
                                        <span class="error-text" data-error="expire_date"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{\App\CPU\translate('min_purchase')}} </label>
                                        <input type="number" step="0.01" name="min_purchase" value="0" min="0"
                                               max="1000000" class="form-control"
                                               placeholder="{{\App\CPU\translate('100')}}">
                                        <span class="error-text" data-error="min_purchase"></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{\App\CPU\translate('discount')}}</label>
                                        <input type="number" step="0.01" min="1" max="1000000" name="discount"
                                               value="{{ old('discount') }}" class="form-control">
                                        <span class="error-text" data-error="discount"></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{\App\CPU\translate('discount')}} {{\App\CPU\translate('type')}}</label>
                                        <select name="discount_type" class="form-control discount-amount">
                                            <option value="percent">{{\App\CPU\translate('percent')}}</option>
                                            <option value="amount">{{\App\CPU\translate('amount')}}</option>
                                        </select>
                                        <span class="error-text" data-error="discount_type"></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6" id="max_discount">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{\App\CPU\translate('max_discount')}}</label>
                                        <input type="number" step="0.01" min="0" value="0" max="1000000"
                                               name="max_discount" class="form-control">
                                        <span class="error-text" data-error="max_discount"></span>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">{{\App\CPU\translate('submit')}}</button>
                        </form>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="align-items-center d-flex gap-2 mb-3">
                    {{ \App\CPU\translate('coupon_table') }}
                    <span class="badge badge-primary rounded">{{$coupons->total()}}</span>
                </h4>
            </div>
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <div class="w-100">
                            <div class="d-flex flex-wrap justify-content-between gap-3">
                                <form action="{{ url()->current() }}" method="GET">
                                    @foreach(['sorting_type', 'start_date', 'end_date',] as $filter)
                                        @if(request()->filled($filter))
                                            <input type="hidden" name="{{ $filter }}"
                                                   value="{{ request()->get($filter) }}">
                                        @endif
                                    @endforeach
                                    <div class="input-group input-group-merge input-group-flush">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                               placeholder="{{ \App\CPU\translate('search_by_coupon_title_and_code') }}"
                                               aria-label="Search orders" value="{{ request()->input('search') }}">
                                        <button type="submit"
                                                class="btn btn-primary">{{ \App\CPU\translate('search') }}</button>
                                    </div>
                                </form>
                                <div class="d-flex flex-wrap gap-2">
                                    <div class="dropdown">
                                        <button type="button" id="dropdownMenuButton"
                                                class="btn btn-white text-primary d-flex align-items-center justify-content-center gap-2 flex-grow-1 h-100"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="d-none d-sm-block"> {{ \App\CPU\translate('Export') }}</span>
                                            <img src="{{ asset('assets/admin/img/download-new.svg') }}" alt=""
                                                 class="svg">
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item mb-2" href="javascript:void(0)"
                                               onclick="exportList(this)" id="csv">
                                                <img class="" src="{{ asset('assets/admin/img/csv.png') }}"
                                                     alt=""/>
                                                CSV
                                            </a>
                                            <a class="dropdown-item mb-2" href="javascript:void(0)"
                                               onclick="exportList(this)" id="xlsx">
                                                <img class="" src="{{ asset('assets/admin/img/excel.png') }}"
                                                     alt=""/>
                                                Excel
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0)"
                                               onclick="exportList(this)" id="pdf">
                                                <img class="" src="{{ asset('assets/admin/img/pdf.png') }}"
                                                     alt=""/>
                                                PDF
                                            </a>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-end position-relative show-filter-count">
                                        <button type="button"
                                                class="offcanvas-toggle btn btn-soft-secondary d-flex align-items-center justify-content-center gap-3 flex-grow-1 h-44px-mobile"
                                                data-target="#offcanvasFilterCat"
                                                aria-label="Toggle filter menu"
                                        >
                                            <i class="fi fi-rr-bars-filter fs-16 lh-1"></i>
                                        </button>
                                    </div>
                                    <a href="{{ route('admin.coupon.add-new') }}"
                                       class="btn btn-soft-primary d-flex align-items-center justify-content-center gap-2 flex-grow-1 lh-1"
                                       type="button">
                                        <i class="fi fi-rr-refresh fs-16"></i>
                                    </a>
                                    <div class="hs-unfold">
                                        <a class="js-hs-unfold-invoker btn btn-soft-danger p-2 h-44px"
                                           href="javascript:void(0)"
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
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-3">
                                                            <span
                                                                class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('SL') }}</span>
                                                            <label class="toggle-switch toggle-switch-sm"
                                                                   for="toggleColumn_sl">
                                                                <input type="checkbox"
                                                                       class="toggle-switch-input update-column-visibility"
                                                                       id="toggleColumn_sl" checked>
                                                                <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                            </label>
                                                        </div>

                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-3">
                                                            <span
                                                                class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Title') }}</span>
                                                            <label class="toggle-switch toggle-switch-sm"
                                                                   for="toggleColumn_title">
                                                                <input type="checkbox"
                                                                       class="toggle-switch-input update-column-visibility"
                                                                       id="toggleColumn_title" checked>
                                                                <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                            </label>
                                                        </div>

                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-3">
                                                            <span
                                                                class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Code') }}</span>
                                                            <label class="toggle-switch toggle-switch-sm"
                                                                   for="toggleColumn_code">
                                                                <input type="checkbox"
                                                                       class="toggle-switch-input update-column-visibility"
                                                                       id="toggleColumn_code" checked>
                                                                <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                            </label>
                                                        </div>

                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-3">
                                                    <span
                                                        class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Min Purchase') }}</span>
                                                            <label class="toggle-switch toggle-switch-sm"
                                                                   for="toggleColumn_min_purchase">
                                                                <input type="checkbox"
                                                                       class="toggle-switch-input update-column-visibility"
                                                                       id="toggleColumn_min_purchase" checked>
                                                                <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                            </label>
                                                        </div>

                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-3">
                                                    <span
                                                        class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Max Discount') }}</span>
                                                            <label class="toggle-switch toggle-switch-sm"
                                                                   for="toggleColumn_max_discount">
                                                                <input type="checkbox"
                                                                       class="toggle-switch-input update-column-visibility"
                                                                       id="toggleColumn_max_discount" checked>
                                                                <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                            </label>
                                                        </div>

                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-3">
                                                    <span
                                                        class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Discount') }}</span>
                                                            <label class="toggle-switch toggle-switch-sm"
                                                                   for="toggleColumn_discount">
                                                                <input type="checkbox"
                                                                       class="toggle-switch-input update-column-visibility"
                                                                       id="toggleColumn_discount" checked>
                                                                <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                            </label>
                                                        </div>

                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-3">
                                                    <span
                                                        class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Discount Type') }}</span>
                                                            <label class="toggle-switch toggle-switch-sm"
                                                                   for="toggleColumn_discount_type">
                                                                <input type="checkbox"
                                                                       class="toggle-switch-input update-column-visibility"
                                                                       id="toggleColumn_discount_type" checked>
                                                                <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                            </label>
                                                        </div>

                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-3">
                                                    <span
                                                        class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Start Date') }}</span>
                                                            <label class="toggle-switch toggle-switch-sm"
                                                                   for="toggleColumn_start_date">
                                                                <input type="checkbox"
                                                                       class="toggle-switch-input update-column-visibility"
                                                                       id="toggleColumn_start_date" checked>
                                                                <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                            </label>
                                                        </div>

                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-3">
                                                    <span
                                                        class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('End Date') }}</span>
                                                            <label class="toggle-switch toggle-switch-sm"
                                                                   for="toggleColumn_end_date">
                                                                <input type="checkbox"
                                                                       class="toggle-switch-input update-column-visibility"
                                                                       id="toggleColumn_end_date" checked>
                                                                <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                            </label>
                                                        </div>

                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-3">
                                                    <span
                                                        class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Status') }}</span>
                                                            <label class="toggle-switch toggle-switch-sm"
                                                                   for="toggleColumn_status">
                                                                <input type="checkbox"
                                                                       class="toggle-switch-input update-column-visibility"
                                                                       id="toggleColumn_status" checked>
                                                                <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                            </label>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span
                                                                class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Action') }}</span>
                                                            <label class="toggle-switch toggle-switch-sm"
                                                                   for="toggleColumn_action">
                                                                <input type="checkbox"
                                                                       class="toggle-switch-input update-column-visibility"
                                                                       id="toggleColumn_action" checked>
                                                                <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                            </label>
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
                    <div class="table-responsive mb-5">
                        <table
                            class="table table-thead-bordered border-bottom table-nowrap table-align-middle card-table title">
                            <thead class="thead-light">
                            <tr>
                                <th data-column="sl">{{ \App\CPU\translate('SL') }}</th>
                                <th data-column="title">{{ \App\CPU\translate('Title') }}</th>
                                <th data-column="code">{{ \App\CPU\translate('Code') }}</th>
                                <th data-column="min_purchase">{{ \App\CPU\translate('Min Purchase') }}</th>
                                <th data-column="max_discount">{{ \App\CPU\translate('Max Discount') }}</th>
                                <th data-column="discount">{{ \App\CPU\translate('Discount') }}</th>
                                <th data-column="discount_type">{{ \App\CPU\translate('Discount Type') }}</th>
                                <th data-column="start_date">{{ \App\CPU\translate('Start Date') }}</th>
                                <th data-column="end_date">{{ \App\CPU\translate('End Date') }}</th>
                                <th data-column="status">{{ \App\CPU\translate('status') }}</th>
                                <th data-column="action" class="text-center">{{ \App\CPU\translate('action') }}</th>
                            </tr>

                            </thead>

                            <tbody>
                            @foreach($coupons as $key => $coupon)
                                <tr>
                                    <td data-column="sl">{{ $coupons->firstitem() + $key }}</td>
                                    <td data-column="title">
                                        <div class="d-flex gap-2 align-items-center">
                                            <div>
                                                <div class="text-truncate max-w-180">
                                                    {{$coupon['title']}}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-column="code">
                                        {{ $coupon['code'] }}
                                    </td>
                                    <td data-column="min_purchase">{{$coupon['min_purchase']." ".\App\CPU\Helpers::currency_symbol()}}</td>
                                    <td data-column="max_discount">{{ $coupon['discount_type'] == 'percent' ? $coupon['max_discount']." ".\App\CPU\Helpers::currency_symbol() : '-'}}</td>
                                    <td data-column="discount">{{ $coupon['discount_type'] == 'amount' ? $coupon['discount']." ".\App\CPU\Helpers::currency_symbol() : $coupon['discount']."%"}}</td>
                                    <td data-column="discount_type">{{$coupon['discount_type']}}</td>
                                    <td data-column="start_date">{{$coupon['start_date']}}</td>
                                    <td data-column="end_date">{{$coupon['expire_date']}}</td>
                                    <td data-column="status">
                                        <label class="toggle-switch toggle-switch-sm">
                                            <input type="checkbox" class="toggle-switch-input global-change-status"
                                                   data-route="{{ route('admin.coupon.status', [$coupon['id'], $coupon->status ? 0 : 1]) }}"
                                                   data-target="#globalChangeStatusModal"
                                                   data-id="{{ $coupon['id'] }}"
                                                   data-title="{{ \App\CPU\translate('Are you sure') }}?"
                                                   data-description="{{ $coupon['status'] == 1 ? \App\CPU\translate('Want to turn off the status') : \App\CPU\translate('Want to turn on the status') }}"
                                                   data-image="{{ asset('assets/admin/img/info.svg') }}"
                                                {{ $coupon->status ? 'checked' : '' }}>
                                            <span class="toggle-switch-label">
                                                                <span class="toggle-switch-indicator"></span>
                                                            </span>
                                        </label>
                                    </td>
                                    <td data-column="action">
                                        <div
                                            class="d-flex justify-content-center align-items-center gap-3">
                                            <a href="{{route('admin.coupon.edit',[$coupon['id']])}}"
                                               class="btn btn-outline-primary icon-btn offcanvas-toggle"
                                               aria-label="Edit">
                                                <i class="fi fi-sr-pencil"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-outline-danger icon-btn delete-resource"
                                                    data-id="{{ $coupon['id'] }}"
                                                    data-target="#deleteModal"
                                                    data-toggle="modal"
                                                    data-title="{{ \App\CPU\translate('Are_you_sure_delete_to_this_coupon') }}?"
                                                    data-subtitle="{{ \App\CPU\translate('If once you delete this coupon, you will lost this coupon data permanently.') }}"
                                                    data-cancel-text="{{ \App\CPU\translate('No') }}"
                                                    data-confirm-text="{{ \App\CPU\translate('Delete') }}"
                                            >
                                                <i class="fi fi-rr-trash"></i>
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="page-area d-flex justify-content-end">
                        <table>
                            <tfoot>
                            {!! $coupons->links() !!}
                            </tfoot>
                        </table>
                    </div>
                    @if(count($coupons) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-one-cati"
                                 src="{{ asset('assets/admin/svg/illustrations/sorry.svg') }}"
                                 alt="Image Description">
                            <p class="mb-0">{{ \App\CPU\translate('No_data_to_show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('admin-views.coupon.partials.offcanvas-filter')
    <span class="data-to-js"
          data-title="coupon-list"
          data-export-route="{{ route('admin.coupon.export') }}"
          data-delete-route="{{ route('admin.coupon.delete', ':id') }}"
    ></span>
@endsection

@push('script_2')
    <script src={{asset("assets/admin/js/coupon-index.js")}}></script>
    <script src={{ asset('assets/admin/js/global.js') }}></script>
    <script src={{ asset('assets/admin/js/custom-daterange.js') }}></script>

    <script>
        "use strict";
        printFilterCount(['search', 'page']);

        $('.generate-code-link').on('click', function () {
            generateCode();
        });

        function generateCode() {
            let code = Math.random().toString(36).substring(2, 12);
            $('#code').val(code)
        }
    </script>
@endpush
