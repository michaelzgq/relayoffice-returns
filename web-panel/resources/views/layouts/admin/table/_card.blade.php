@php use function App\CPU\translate; @endphp
<div class="card">
    <div class="card-header">
        <div class="w-100">
            <div class="d-flex flex-wrap justify-content-between gap-3">
                <form action="{{ url()->current() }}" method="GET">
                    @foreach(['start_date', 'end_date', 'sorting_type', 'per_page'] as $filter)
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
                               placeholder="{{ $searchBoxPlaceholder }}"
                               aria-label="Search orders" value="{{ request()->get('search') }}">
                        <button type="submit"
                                class="btn btn-primary">{{ translate('search') }}</button>
                    </div>
                </form>
                <div class="d-flex flex-wrap gap-2">
                    <div class="dropdown">
                        <button type="button" id="dropdownMenuButton"
                                class="btn btn-white text-primary d-flex align-items-center justify-content-center gap-2 flex-grow-1 h-100"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="d-none d-sm-block fs-13"> {{ translate('Export') }}</span>
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
                    <a href="{{ url()->current() }}"
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
                                        <h5 class="modal-title">{{ translate('Colum View') }}</h5>
                                        <p class="fs-12 mb-0">{{ translate('You can control the column view by turning the
                                                    toggle on or off.') }}</p>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="overflow-y-auto max-h-100vh-500px max-h-lg-100vh-400px">
                                        @foreach($columns as $key => $column)
                                            <div
                                                class="d-flex justify-content-between align-items-center {{ $loop->last ? '' : 'mb-3' }}">
                                                <span
                                                    class="mr-2 fs-13 title text-capitalize">{{ translate($column) }}</span>
                                                <label class="toggle-switch toggle-switch-sm"
                                                       for="toggleColumn_{{ $column }}">
                                                    <input type="checkbox"
                                                           class="toggle-switch-input update-column-visibility"
                                                           id="toggleColumn_{{ $column }}" checked>
                                                    <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                </label>
                                            </div>
                                        @endforeach
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
            class="table table-thead-bordered border-bottom table-nowrap table-align-middle card-table title">
            <thead class="thead-light">
            <tr>
                @foreach($columns as $column)
                    <th data-column="{{ $column }}" class="{{ $column == 'action' ? 'text-center' : '' }}">{{ translate($column) }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            {!! $tableRows !!}
            </tbody>
        </table>
    </div>
    {!! $resources->links('layouts/admin/pagination/_pagination', ['perPage' => request()->get('per_page')]) !!}
    @include('layouts.admin.offcanvas._offcanvas-filter')
</div>
<span class="data-to-js"
      data-title="{{ $tableFor }}-list"
      data-export-route="{{ $exportRoute }}"
></span>

@push('script_2')
    <script src={{ asset('assets/admin/js/global.js') }}></script>
    <script src={{ asset('assets/admin/js/custom-daterange.js') }}></script>
    <script>
        printFilterCount(['search', 'page', 'per_page']);
    </script>
@endpush
