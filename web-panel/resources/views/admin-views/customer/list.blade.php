@extends('layouts.admin.app')

@section('title',\App\CPU\translate('customer_list'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/custom.css"/>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="">
            <div class="d-flex align-items-cente justify-content-between flex-wrap gap-2 mb-3">
                <h1 class="page-header-title d-flex align-items-center g-2px text-capitalize mb-0 h3">
                    {{\App\CPU\translate('customer_list')}}
                    <span class="badge bg-primary text-white ml-2">{{$resources->total()}}</span>
                </h1>
                <div>
                    <a href="{{route('admin.customer.add')}}"
                       class="btn btn-primary lh-1 d-flex gap-2 align-items-center"><i
                            class="fi fi-rr-add"></i> {{\App\CPU\translate('add_new_customer')}}
                    </a>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-start flex-wrap flex-grow-1 gap-2">
                    <div class="d-flex flex-wrap flex-grow-1 flex-lg-grow-0 gap-2">
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
                                       placeholder="{{ \App\CPU\translate('search_by_name_or_number') }}"
                                       aria-label="Search orders" value="{{ request()->input('search') }}">
                                <button type="submit"
                                        class="btn btn-primary">{{ \App\CPU\translate('search') }}</button>
                            </div>
                        </form>
                    </div>
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
                        <a href="{{ route('admin.customer.list') }}"
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
                                                                class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Image') }}</span>
                                                <label class="toggle-switch toggle-switch-sm"
                                                       for="toggleColumn_image">
                                                    <input type="checkbox"
                                                           class="toggle-switch-input update-column-visibility"
                                                           id="toggleColumn_image" checked>
                                                    <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                </label>
                                            </div>

                                            <div
                                                class="d-flex justify-content-between align-items-center mb-3">
                                                            <span
                                                                class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Name') }}</span>
                                                <label class="toggle-switch toggle-switch-sm"
                                                       for="toggleColumn_name">
                                                    <input type="checkbox"
                                                           class="toggle-switch-input update-column-visibility"
                                                           id="toggleColumn_name" checked>
                                                    <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                </label>
                                            </div>

                                            <div
                                                class="d-flex justify-content-between align-items-center mb-3">
                                                    <span
                                                        class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Phone') }}</span>
                                                <label class="toggle-switch toggle-switch-sm"
                                                       for="toggleColumn_phone">
                                                    <input type="checkbox"
                                                           class="toggle-switch-input update-column-visibility"
                                                           id="toggleColumn_phone" checked>
                                                    <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                </label>
                                            </div>

                                            <div
                                                class="d-flex justify-content-between align-items-center mb-3">
                                                    <span
                                                        class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Orders') }}</span>
                                                <label class="toggle-switch toggle-switch-sm"
                                                       for="toggleColumn_orders">
                                                    <input type="checkbox"
                                                           class="toggle-switch-input update-column-visibility"
                                                           id="toggleColumn_orders" checked>
                                                    <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                </label>
                                            </div>

                                            <div
                                                class="d-flex justify-content-between align-items-center mb-3">
                                                    <span
                                                        class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Balance') }}</span>
                                                <label class="toggle-switch toggle-switch-sm"
                                                       for="toggleColumn_balance">
                                                    <input type="checkbox"
                                                           class="toggle-switch-input update-column-visibility"
                                                           id="toggleColumn_balance" checked>
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
            <div class="table-responsive datatable-custom">
                <table
                    class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                    <tr>
                        <th data-column="sl">{{\App\CPU\translate('SL')}}</th>
                        <th data-column="image">{{ \App\CPU\translate('image') }}</th>
                        <th data-column="name">{{\App\CPU\translate('name')}}</th>
                        <th data-column="phone">{{\App\CPU\translate('phone')}}</th>
                        <th data-column="orders">{{ \App\CPU\translate('orders') }}</th>
                        <th data-column="balance" class="text-center">{{ \App\CPU\translate('balance') }}</th>
                        <th data-column="action" class="text-center">{{\App\CPU\translate('action')}}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($resources as $key => $resource)
                        <tr>
                            <td data-column="sl">{{ $resources->firstItem() + $key }}</td>
                            <td data-column="image">
                                <a href="{{ route('admin.customer.view', [$resource['id']]) }}">
                                    <img class="img-one-cl" src="{{ $resource['image_fullpath'] }}" alt="">
                                </a>
                            </td>
                            <td data-column="name">
                                <a class="text-primary"
                                   href="{{ route('admin.customer.view', [$resource['id']]) }}">
                                    {{ $resource->name }}
                                </a>
                            </td>
                            <td data-column="phone">
                                @if ($resource->id != 0)
                                    {{ $resource->mobile }}
                                @else
                                    {{ \App\CPU\translate('no_phone') }}
                                @endif
                            </td>
                            <td data-column="orders">{{ $resource->orders->count() }}</td>
                            <td data-column="balance" class="text-center p-5">
                                @if($resource->id == 0)
                                    <span>{{ \App\CPU\translate('No Balance') }}</span>
                                @else
                                    <div class="row justify-content-center">
                                        <div class="col-3">
                                            {{ $resource->balance . ' ' . \App\CPU\Helpers::currency_symbol() }}
                                        </div>
                                        <div class="col-3">
                                            <a class="btn btn-info p-1 badge update-customer-balance"
                                               id="{{ $resource->id }}" data-id="{{ $resource->id }}"
                                               type="button" data-toggle="modal"
                                               data-target="#update-customer-balance">
                                                <i class="tio-add-circle"></i>
                                                {{ \App\CPU\translate('add_balance') }}
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td data-column="action" class="text-center">
                                <div class="d-flex justify-content-center align-items-center">
                                    <a class="btn btn-outline-info icon-btn mr-1"
                                       href="{{ route('admin.customer.view', [$resource['id']]) }}"><span
                                            class="tio-visible"></span></a>
                                    @if($resource->id != 0)
                                        <a class="btn btn-outline-theme icon-btn mr-1"
                                           href="{{ route('admin.customer.edit', [$resource['id']]) }}">
                                            <span class="tio-edit"></span>
                                        </a>
                                        <button type="button"
                                                class="btn btn-outline-danger icon-btn delete-resource"
                                                data-id="{{ $resource['id'] }}"
                                                data-target="#deleteModal"
                                                data-toggle="modal"
                                                data-title="{{ \App\CPU\translate('Are you sure to delete this supplier') }}?"
                                                data-subtitle="{{ $resource['balance'] < 0 ? \App\CPU\translate('This customer has Payable amount. Current balance is') . ' ' . $resource->balance . ' .' .  \App\CPU\translate('Do you want to delete this customer') . '?' :  \App\CPU\translate('If once you delete this supplier, you will lost this supplier data permanently.') }}"
                                                data-cancel-text="{{ \App\CPU\translate('No') }}"
                                                data-confirm-text="{{ \App\CPU\translate('Delete') }}"
                                        >
                                            <i class="fi fi-rr-trash"></i>
                                        </button>
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
                    <tfoot class="border-top">
                    {!! $resources->links() !!}
                    </tfoot>
                </table>
            </div>
            @if(count($resources)==0)
                <div class="text-center p-4">
                    <img class="mb-3 w-one-cl"
                         src="{{asset('assets/admin')}}/svg/illustrations/sorry.svg"
                         alt="{{\App\CPU\translate('Image Description')}}">
                    <p class="mb-0">{{ \App\CPU\translate('No_data_to_show')}}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="update-customer-balance" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{\App\CPU\translate('update_customer_balance')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('admin.customer.update-balance')}}" method="post" class="row">
                        @csrf
                        <input type="hidden" id="customer_id" name="customer_id">
                        <div class="form-group col-12 col-sm-6">
                            <label for="">{{\App\CPU\translate('balance')}}</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="amount" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label class="input-label"
                                       for="exampleFormControlInput1">{{\App\CPU\translate('balance_receive_account')}}</label>
                                <select name="account_id" class="form-control js-select2-custom" required>
                                    <option value="">---{{\App\CPU\translate('select')}}---</option>
                                    @foreach ($accounts as $account)
                                        @if ($account['id']!=2 && $account['id']!=3)
                                            <option value="{{$account['id']}}">{{$account['account']}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{\App\CPU\translate('description')}} </label>
                                <input type="text" name="description" class="form-control"
                                       placeholder="{{\App\CPU\translate('description')}}">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label class="input-label"
                                       for="exampleFormControlInput1">{{\App\CPU\translate('date')}} </label>
                                <input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control"
                                       required>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <button class="btn btn-sm btn-primary"
                                    type="submit">{{\App\CPU\translate('submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('admin-views.customer.partials.offcanvas-filter')
    <span class="data-to-js"
          data-title="customer-list"
          data-export-route="{{ route('admin.customer.export') }}"
          data-delete-route="{{ route('admin.customer.delete', ':id') }}"
    ></span>
@endsection

@push('script_2')
    <script src={{asset("assets/admin/js/global.js")}}></script>
    <script src={{ asset('assets/admin/js/custom-daterange.js') }}></script>
    <script>
        "use strict";
        printFilterCount(['search', 'page']);
    </script>
@endpush
