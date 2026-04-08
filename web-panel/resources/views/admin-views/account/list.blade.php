@extends('layouts.admin.app')

@section('title',\App\CPU\translate('account_list'))

@section('content')
    <div class="content container-fluid">
        <div class="">
            <div class="d-flex align-items-cente justify-content-between flex-wrap gap-2 mb-3">
                <h1 class="page-header-title d-flex align-items-center g-2px text-capitalize mb-0 h3">
                    {{\App\CPU\translate('Account_List')}}
                    <span class="badge bg-primary text-white ml-2">{{$resources->total()}}</span>
                </h1>
                <div>
                    <a href="{{route('admin.account.add')}}"
                       class="btn btn-primary lh-1 d-flex gap-2 align-items-center"><i
                            class="fi fi-rr-add"></i> {{\App\CPU\translate('Add New Account')}}
                    </a>
                </div>
            </div>
        </div>
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
                                       placeholder="{{ \App\CPU\translate('search_by_account_or_account_number') }}"
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
                                    <span class="d-none d-sm-block fs-13"> {{ \App\CPU\translate('Export') }}</span>
                                    <img src="{{ asset('assets/admin/img/download-new.svg') }}" alt=""
                                         class="svg">
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item mb-2" href="javascript:void(0)" onclick="exportList(this)"
                                       id="csv">
                                        <img class="" src="{{ asset('assets/admin/img/csv.png') }}" alt=""/>
                                        CSV
                                    </a>
                                    <a class="dropdown-item mb-2" href="javascript:void(0)" onclick="exportList(this)"
                                       id="xlsx">
                                        <img class="" src="{{ asset('assets/admin/img/excel.png') }}" alt=""/>
                                        Excel
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="exportList(this)"
                                       id="pdf">
                                        <img class="" src="{{ asset('assets/admin/img/pdf.png') }}" alt=""/>
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
                            <a href="{{ route('admin.account.add') }}"
                               class="btn btn-soft-primary d-flex align-items-center justify-content-center gap-2 flex-grow-1 lh-1"
                               type="button">
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

                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span
                                                        class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Account Info') }}</span>
                                                    <label class="toggle-switch toggle-switch-sm"
                                                           for="toggleColumn_account_info">
                                                        <input type="checkbox"
                                                               class="toggle-switch-input update-column-visibility"
                                                               id="toggleColumn_account_info" checked>
                                                        <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span
                                                        class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Balance Info') }}</span>
                                                    <label class="toggle-switch toggle-switch-sm"
                                                           for="toggleColumn_balance_info">
                                                        <input type="checkbox"
                                                               class="toggle-switch-input update-column-visibility"
                                                               id="toggleColumn_balance_info" checked>
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
                    class="table table-thead-bordered table-nowrap table-align-middle card-table title">
                    <thead class="thead-light">
                    <tr>
                        <th data-column="sl">{{ \App\CPU\translate('SL') }}</th>
                        <th data-column="account_info">{{ \App\CPU\translate('Account Info') }}</th>
                        <th data-column="balance_info">{{ \App\CPU\translate('Balance Info') }}</th>
                        <th data-column="action" class="text-center">{{ \App\CPU\translate('action') }}</th>
                    </tr>

                    </thead>

                    <tbody>
                    @foreach($resources as $key => $resource)
                        <tr>
                            <td data-column="sl">{{ $resources->firstitem() + $key }}</td>
                            <td data-column="account_info">
                                <div class="max-w450 text-wrap">
                                    {{ $resource->account }} <br>

                                    @if ($resource->id !=1 && $resource->id !=2 && $resource->id !=3)
                                        {{ $resource->account_number }} <br>
                                        {{ $resource->description }}
                                    @endif
                                </div>
                            </td>
                            <td data-column="balance_info">
                                {{\App\CPU\translate('balance')}}
                                : {{ $resource->balance . ' ' . \App\CPU\Helpers::currency_symbol()}} <br>
                                {{\App\CPU\translate('total_in')}}
                                : {{ $resource->total_in ? $resource->total_in . ' ' . \App\CPU\Helpers::currency_symbol() : 0   . ' ' . \App\CPU\Helpers::currency_symbol()}}
                                <br>
                                {{ \App\CPU\translate('total_out') }}
                                : {{ $resource->total_out ? $resource->total_out . ' ' . \App\CPU\Helpers::currency_symbol() : 0 . ' ' . \App\CPU\Helpers::currency_symbol()}}
                                <br>
                            </td>
                            <td data-column="action">
                                <div class="d-flex justify-content-center align-items-center gap-3">
                                    @if ($resource->id !=1 && $resource->id !=2 && $resource->id !=3)
                                        <a href="{{route('admin.account.edit',[$resource['id']])}}"
                                           class="btn btn-outline-theme icon-btn offcanvas-toggle"
                                           data-id="{{ $resource['id'] }}"
                                           aria-label="Edit">
                                            <i class="fi fi-sr-pencil"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-outline-danger icon-btn delete-resource"
                                                data-id="{{ $resource['id'] }}"
                                                data-target="#deleteModal"
                                                data-toggle="modal"
                                                data-title="{{ \App\CPU\translate('Are you sure to delete this account') }}?"
                                                data-subtitle="{{ \App\CPU\translate('If once you delete this account, you will lost this account data permanently.') }}"
                                                data-cancel-text="{{ \App\CPU\translate('No') }}"
                                                data-confirm-text="{{ \App\CPU\translate('Delete') }}"
                                        >
                                            <i class="fi fi-rr-trash"></i>
                                        </button>
                                    @else
                                        <span>{{\App\CPU\translate('default')}}</span>
                                    @endif
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
                    {!! $resources->links() !!}
                    </tfoot>
                </table>
            </div>
            @if (count($resources) == 0)
                <div class="text-center p-4">
                    <img class="mb-3 w-one-cati"
                         src="{{ asset('assets/admin/svg/illustrations/sorry.svg') }}"
                         alt="Image Description">
                    <p class="mb-0">{{ \App\CPU\translate('No_data_to_show') }}</p>
                </div>
            @endif
        </div>
    </div>

    @include('admin-views.account.partials.offcanvas-filter')
    <span class="data-to-js"
          data-title="account-list"
          data-delete-route="{{ route('admin.account.delete', ':id') }}"
          data-export-route="{{ route('admin.account.export') }}"
    >
    </span>
@endsection

@push('script_2')
    <script src={{ asset('assets/admin/js/global.js') }}></script>
    <script src={{ asset('assets/admin/js/custom-daterange.js') }}></script>
    <script>
        "use strict";
        printFilterCount(['search', 'page']);
    </script>
@endpush
