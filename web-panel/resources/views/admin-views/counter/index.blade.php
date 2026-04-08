@extends('layouts.admin.app')

@section('title', \App\CPU\translate('counter setup'))

@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('assets/admin') }}/css/custom.css" />
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="">
            <div class="row align-items-center mb-3 ">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title text-capitalize">
                        <span>{{ \App\CPU\translate('counter setup') }}</span>
                    </h1>
                </div>
            </div>
        </div>
        <div class="card mb-20">
            <div class="card-body">
                <form action="{{ route('admin.counter.store') }}" method="post" id="store-or-update-data">
                     <div class="mb-20">
                        <h3 class="mb-1">{{\App\CPU\translate('Add New Counter')}}</h3>
                        <p class="mb-0 text-clr fs-12">{{\App\CPU\translate('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam odio tellus, laoreet ')}}</p>
                    </div>
                    <div class="bg-fafafa rounded p-xl-20 p-3">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group mb-20">
                                    <label class="input-label text-capitalize text-black fs-14" for="name">{{ \App\CPU\translate('Counter_Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" maxlength="255"
                                        placeholder="{{ \App\CPU\translate('Ex: Fast Lanee') }}" >
                                    <span class="error-text" data-error="name"></span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group mb-20">
                                    <label class="input-label text-capitalize text-black fs-14" for="number">{{ \App\CPU\translate('Counter_number') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="number" class="form-control" value="{{ old('number') }}"
                                        placeholder="{{ \App\CPU\translate('Ex: 01') }}" >
                                    <span class="error-text" data-error="number"></span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12">
                                <div class="form-group mb-0">
                                    <label class="input-label text-capitalize text-black fs-14" for="description">{{ \App\CPU\translate('Short_Description') }}</label>
                                    <textarea class="form-control" name="description" id="" cols="30" rows="3" maxlength="100">{{ old('description') }}</textarea>
                                    <p class="counting-box text-end text-black-50 mb-0 mt-1">0/100</p>
                                </div>
                                <span class="error-text" data-error="description"></span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-20 gap-3">
                        <button type="reset" class="btn btn-secondary min-w-90 min-w-lg-120" href="javascript:">{{ \App\CPU\translate('reset') }}</button>
                        <button type="submit" class="btn btn-primary min-w-90 min-w-lg-120">{{ \App\CPU\translate('submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
        <div>
            <h5>{{ \App\CPU\translate('counter_list') }}
                <span class="btn btn-primary fs-10 lh-1 py-1 px-2">{{$counters->total()}}</span>
            </h5>
            <div class="card mt-3">
                <div class="card-header border-0">
                    <div class="d-flex w-100 align-items-center gap-3 flex-wrap justify-content-between">
                        <div class="max-w-400 d-flex justify-content-end">
                            <form action="{{ url()->current() }}" method="GET">
                                <div class="input-group input-group-merge input-group-flush">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="tio-search"></i>
                                        </div>
                                    </div>
                                    <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{ \App\CPU\translate('search_by_counter_name_or_number') }}"
                                            aria-label="Search orders" value="{{ $queryParam['search'] ?? '' }}" required>
                                    <button type="submit"
                                            class="btn btn-primary">{{ \App\CPU\translate('search') }}</button>
                                </div>
                            </form>
                        </div>
                        <div class="d-flex align-items-center gap-2 justify-content-end">
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
                            <a href="{{ url()->current() }}" class="btn btn-secondary-custom w-40 h-40 d-center p-2">
                                <i class="tio-refresh"></i>
                            </a>
                            <div class="hs-unfold">
                                <a class="js-hs-unfold-invoker btn badge-soft-danger w-40 h-40 d-center p-2" href="javascript:void(0)"
                                    data-hs-unfold-options='{
                                        "target": "#showHideDropdown",
                                        "type": "css-animation"
                                    }'>
                                    <img width="20" class="svg" src="{{ asset('assets/admin/img/column.svg') }}" alt=""/>
                                </a>
                                <div id="showHideDropdown" class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right dropdown-card min-w-340">
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
                                                    <span class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('SL')}}</span>
                                                    <label class="toggle-switch toggle-switch-sm" for="toggleColumn_sl">
                                                        <input type="checkbox" class="toggle-switch-input update-column-visibility"
                                                                id="toggleColumn_sl" checked>
                                                        <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('Counter_Name')}}</span>
                                                    <label class="toggle-switch toggle-switch-sm" for="toggleColumn_name">
                                                        <input type="checkbox" class="toggle-switch-input update-column-visibility"
                                                                id="toggleColumn_name" checked>
                                                        <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('Counter_Number')}}</span>
                                                    <label class="toggle-switch toggle-switch-sm" for="toggleColumn_number">
                                                        <input type="checkbox" class="toggle-switch-input update-column-visibility"
                                                                id="toggleColumn_number" checked>
                                                        <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('Description')}}</span>
                                                    <label class="toggle-switch toggle-switch-sm" for="toggleColumn_description">
                                                        <input type="checkbox" class="toggle-switch-input update-column-visibility"
                                                                id="toggleColumn_description" checked>
                                                        <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('status')}}</span>
                                                    <label class="toggle-switch toggle-switch-sm" for="toggleColumn_status">
                                                        <input type="checkbox" class="toggle-switch-input update-column-visibility"
                                                                id="toggleColumn_status" checked>
                                                        <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('action')}}</span>
                                                    <label class="toggle-switch toggle-switch-sm" for="toggleColumn_action">
                                                        <input type="checkbox" class="toggle-switch-input update-column-visibility"
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
                <div class="table-responsive datatable-custom counter-table">
                    <table
                        class="table table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th data-column="sl">{{ \App\CPU\translate('Serial_no') }}</th>
                                <th data-column="name">{{ \App\CPU\translate('Counter Name') }}</th>
                                <th data-column="number" class="text-center">{{ \App\CPU\translate('Counter Number') }}</th>
                                <th data-column="description">{{ \App\CPU\translate('Description') }}</th>
                                <th data-column="status">{{ \App\CPU\translate('status') }}</th>
                                <th data-column="action" class="text-center">{{ \App\CPU\translate('action') }}</th>
                            </tr>
                        </thead>

                        <tbody id="set-rows">
                        @foreach($counters as $key => $counter)
                            <tr>
                                <td data-column="sl">{{ $counters->firstitem()+$key }}</td>
                                <td data-column="name">
                                    <span class="d-block font-size-sm text-body">{{ $counter->name }}</span>
                                </td>
                                <td data-column="number" class="text-center text-black">{{ $counter->number }}</td>
                                <td data-column="description">
                                    <p class="m-0 line-limit-2 max-w-320 text-black min-w-150px text-wrap">
                                        {{ $counter->description ?? 'N/A' }}
                                    </p>
                                </td>
                                <td data-column="status">
                                    <label class="toggle-switch toggle-switch-sm">
                                        <input type="checkbox" class="toggle-switch-input change-status"
                                               data-route="{{ route('admin.counter.status',[$counter['id'],$counter->status?0:1]) }}"
                                               class="toggle-switch-input" {{$counter->status?'checked':''}}>
                                        <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                    </label>
                                </td>
                                <td data-column="action">
                                    <div class="d-flex justify-content-center gap-2">
                                        <div class="d-inline">
                                            <a class="btn btn-outline-info icon-btn mr-1" data-toggle="tooltip" data-placement="top" title="" href="{{ route('admin.counter.details', [$counter['id']]) }}" data-original-title="view">
                                                <span class="tio-visible"></span>
                                            </a>
                                        </div>
                                        <div class="d-inline">
                                            <a class="btn btn-outline-theme icon-btn mr-1" href="{{ route('admin.counter.edit', [$counter['id']]) }}"> <span class="tio-edit"></span></a>
                                        </div>
                                        <button type="button"
                                                class="btn btn-outline-danger icon-btn delete-resource"
                                                data-id="{{ $counter['id'] }}"
                                                data-target="#deleteModal"
                                                data-toggle="modal"
                                                data-title="{{ \App\CPU\translate('Are_you_sure_delete_to_this_counter') }}?"
                                                data-subtitle="{{ \App\CPU\translate('If once you delete this counter, you will lost this counter data permanently.') }}"
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
                {!! $counters->links('layouts/admin/pagination/_pagination', ['perPage' => request()->get('per_page')]) !!}
                @if (count($counters) == 0)
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

    @include('admin-views.counter.partials.offcanvas-filter')
    <span class="data-to-js"
          data-title="counter-list"
          data-export-route="{{ route('admin.counter.export-list') }}"
          data-delete-route="{{ route('admin.counter.delete', ':id') }}"
    ></span>
@endsection

@push('script_2')
    <script src={{ asset('assets/admin/js/global.js') }}></script>
    <script src={{ asset('assets/admin/js/custom-daterange.js') }}></script>
    <script>
        "use strict";
        printFilterCount(['search', 'page', 'per_page']);
    </script>
@endpush
