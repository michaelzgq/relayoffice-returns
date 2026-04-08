@php use function App\CPU\translate; @endphp
@extends('layouts.admin.app')

@section('title',translate('transaction_list'))

@section('content')
    <div class="content container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title d-flex align-items-center g-2px text-capitalize"><i
                        class="tio-files"></i> {{translate('transaction_list')}}
                    <span class="badge badge-soft-dark ml-2">{{$transections->total()}}</span>
                </h1>
            </div>
        </div>
        <div class="row ">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
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
                                               placeholder="{{ \App\CPU\translate('search_by_account') }}"
                                               aria-label="Search orders" value="{{ request()->input('search') }}">
                                        @foreach(request()->except(['search', 'page']) as $key => $input)
                                            @if($input != null && $input != 'all')
                                                <input type="hidden" name="{{ $key }}" value="{{ $input }}">
                                            @endif
                                        @endforeach
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
                                                        <span class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('date')}}</span>
                                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_date">
                                                            <input type="checkbox" class="toggle-switch-input update-column-visibility"
                                                                   id="toggleColumn_date" checked>
                                                            <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                        </label>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('account')}}</span>
                                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_account">
                                                            <input type="checkbox" class="toggle-switch-input update-column-visibility"
                                                                   id="toggleColumn_account" checked>
                                                            <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                        </label>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('Type')}}</span>
                                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_type">
                                                            <input type="checkbox" class="toggle-switch-input update-column-visibility"
                                                                   id="toggleColumn_type" checked>
                                                            <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                        </label>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('Amount')}}</span>
                                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_amount">
                                                            <input type="checkbox" class="toggle-switch-input update-column-visibility"
                                                                   id="toggleColumn_amount" checked>
                                                            <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                        </label>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('description')}}</span>
                                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_description">
                                                            <input type="checkbox" class="toggle-switch-input update-column-visibility"
                                                                   id="toggleColumn_description" checked>
                                                            <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                        </label>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('debit')}}</span>
                                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_debit">
                                                            <input type="checkbox" class="toggle-switch-input update-column-visibility"
                                                                   id="toggleColumn_debit" checked>
                                                            <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                        </label>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('credit')}}</span>
                                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_credit">
                                                            <input type="checkbox" class="toggle-switch-input update-column-visibility"
                                                                   id="toggleColumn_credit" checked>
                                                            <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                        </label>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('balance')}}</span>
                                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_balance">
                                                            <input type="checkbox" class="toggle-switch-input update-column-visibility"
                                                                   id="toggleColumn_balance" checked>
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

                    <div class="table-responsive datatable-custom">
                        <table
                            class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                            <tr>
                                <th data-column="date">{{ translate('date') }}</th>
                                <th data-column="account">{{ translate('account') }}</th>
                                <th data-column="type">{{translate('type')}}</th>
                                <th data-column="amount">{{translate('amount')}}</th>
                                <th data-column="description">{{translate('description')}}</th>
                                <th data-column="debit">{{ translate('debit') }}</th>
                                <th data-column="credit">{{translate('credit')}}</th>
                                <th data-column="balance">{{translate('balance')}}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @forelse($transections as $key => $transection)
                                <tr>
                                    <td data-column="date">{{ $transection->date }}</td>
                                    <td data-column="account">
                                        @if($transection->account)
                                            {{$transection->account->account}}
                                        @else
                                            <span class="badge badge-danger">{{ translate('Account Deleted') }}</span>
                                        @endif
                                    </td>
                                    <td data-column="type">
                                        @if ($transection->tran_type == 'Expense')
                                            <span class="badge badge-danger">
                                                    {{ $transection->tran_type}} <br>
                                                </span>
                                        @elseif($transection->tran_type == 'Deposit')
                                            <span class="badge badge-info">
                                                    {{ $transection->tran_type}} <br>
                                                </span>
                                        @elseif($transection->tran_type == 'Transfer')
                                            <span class="badge badge-warning">
                                                    {{ $transection->tran_type}} <br>
                                                </span>
                                        @elseif($transection->tran_type == 'Income')
                                            <span class="badge badge-success">
                                                    {{ $transection->tran_type}} <br>
                                                </span>
                                        @elseif($transection->tran_type == 'Payable')
                                            <span class="badge badge-soft-warning">
                                                    {{ $transection->tran_type}} <br>
                                                </span>
                                        @elseif($transection->tran_type == 'Receivable')
                                            <span class="badge badge-soft-success">
                                                    {{ $transection->tran_type}} <br>
                                                </span>
                                        @elseif($transection->tran_type == 'Refund')
                                            <span class="badge badge-danger">
                                                    {{ $transection->tran_type}} <br>
                                                </span>
                                        @endif
                                    </td>
                                    <td data-column="amount">
                                        {{ $transection->amount ." ".\App\CPU\Helpers::currency_symbol()}}
                                    </td>
                                    <td data-column="description">
                                        @if(strlen($transection->description) > 30)
                                            <span data-toggle="tooltip" data-placement="top"
                                                  title="{{ $transection->description }}">
                                                    {{ Str::limit($transection->description,30) }}
                                                </span>
                                        @else
                                            {{ $transection->description ?? 'N/A' }}
                                        @endif
                                    </td>
                                    <td data-column="debit">
                                        @if ($transection->debit)
                                            {{ $transection->amount ." ".\App\CPU\Helpers::currency_symbol()}}
                                        @else
                                            {{ 0 ." ".\App\CPU\Helpers::currency_symbol()}}
                                        @endif
                                    </td>
                                    <td data-column="credit">
                                        @if ($transection->credit)
                                            {{ $transection->amount ." ".\App\CPU\Helpers::currency_symbol()}}
                                        @else
                                            {{ 0 ." ".\App\CPU\Helpers::currency_symbol()}}
                                        @endif
                                    </td>
                                    <td data-column="balance">
                                        {{ $transection->balance ." ".\App\CPU\Helpers::currency_symbol()}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center p-4">
                                        <img class="mb-3 img-one-in" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="{{\App\CPU\translate('Image Description')}}">
                                        <p class="mb-0">{{ \App\CPU\translate('No_data_to_show')}}</p>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    {!! $transections->links('layouts/admin/pagination/_pagination', ['perPage' => request()->get('per_page')]) !!}
                </div>
            </div>
        </div>
    </div>
    @include('admin-views.transection.partials.offcanvas-filter')
    <span class="data-to-js"
          data-title="transaction-list"
          data-export-route="{{ route('admin.account.transaction-export') }}"
    ></span>
@endsection

@push('script_2')
    <script src={{asset("assets/admin/js/transaction.js")}}></script>
    <script src={{ asset('assets/admin/js/global.js') }}></script>
    <script src={{ asset('assets/admin/js/custom-daterange.js') }}></script>
    <script>
        printFilterCount(['search', 'page', 'per_page']);
    </script>
@endpush
