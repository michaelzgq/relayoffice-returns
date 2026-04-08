@extends('layouts.admin.app')

@section('title', \App\CPU\translate('add_new_category'))

@push('css_or_js')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" href="{{ asset('assets/admin') }}/css/custom.css" />
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="">
            <div class="row align-items-center mb-3">
                <div class="col-sm">
                    <h1 class="page-header-title d-flex align-items-center g-2px text-capitalize mb-0">
                        <span>{{ \App\CPU\translate('category_setup') }}</span>
                    </h1>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <div class="mb-4">
                    <h3 class="mb-0">{{ \App\CPU\translate('add_new_category') }}</h3>
                    <p class="fs-12 mb-0">{{ \App\CPU\translate('Create a new product category to keep your items organized and easy to manage.') }}</p>
                </div>
                <form action="{{ route('admin.category.store') }}" method="post" enctype="multipart/form-data" id="store-or-update-data">
                    <input type="hidden" name="type" value="category">
                    <div class="row gy-3">
                        <div class="col-lg-6">
                            <div class="bg-fafafa p-3 p-lg-4 rounded-10 h-100">
                                <div class="form-group">
                                    <label for="" class="title d-flex g-2px">{{ \App\CPU\translate('category_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="{{ \App\CPU\translate('type_category_name') }}" maxlength="251" >
                                    <input name="position" value="0" class="d-none">
                                    <span class="error-text" data-error="name"></span>
                                </div>
                                <div class="form-group mb-0">
                                    <label for="" class="title">{{ \App\CPU\translate('Description') }}</label>
                                    <textarea name="description" id="" class="form-control" placeholder="{{ \App\CPU\translate('Type_description') }}"></textarea>
                                    <span class="error-text" data-error="description"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="bg-fafafa p-3 p-lg-4 rounded-10 d-flex justify-content-center align-items-center h-100">
                                <div class="text-center">
                                    <h4 class="mb-3">{{ \App\CPU\translate('Upload_Image') }}</h4>
                                    <label class="upload-file">
                                        <input type="file" name="image" id="customFileEg1" class="upload-file-input"
                                               accept="{{ IMAGE_ACCEPTED_EXTENSIONS }}"
                                               data-max-upload-size="{{ readableUploadMaxFileSize('image') }}">
                                        <button type="button" class="remove_btn btn btn-danger">
                                            <i class="fi fi-sr-cross"></i>
                                        </button>
                                        <div class="upload-file-wrapper w-100px">
                                            <div class="upload-file-textbox p-3 rounded bg-white border-dashed w-100 h-100">
                                                <div class="d-flex flex-column justify-content-center align-items-center gap-1 h-100">
                                                    <i class="fi fi-sr-camera lh-1 fs-16 text-primary"></i>
                                                    <p class="fs-10 mb-0">{{ \App\CPU\translate('Add_image') }}</p>
                                                </div>
                                            </div>
                                            <img class="upload-file-img" loading="lazy" src="" data-default-src="" alt="">
                                        </div>
                                    </label>
                                    <p class="mb-0 title fs-12 mt-4">{{ getFileFormatSizeTranslatedText(IMAGE_ACCEPTED_EXTENSIONS) }} <span class="fw-bold">(1:1)</span></p>
                                    <span class="error-text justify-content-center" data-error="image"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-end flex-wrap gap-3">
                                <button type="reset" class="btn btn-light fw-semibold min-w-120px">{{ \App\CPU\translate('reset') }}</button>
                                <button type="submit" class="btn btn-primary fw-semibold min-w-120px">{{ \App\CPU\translate('submit') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div>
            <h4 class="align-items-center d-flex gap-2 mb-3">
                {{ \App\CPU\translate('category_table') }}
                <span class="badge badge-primary rounded">{{$resources->total()}}</span>
            </h4>
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
                                       placeholder="{{ \App\CPU\translate('search_by_category_name') }}"
                                       aria-label="Search orders" value="{{ request()->input('search') }}">
                                <button type="submit" class="btn btn-primary">{{ \App\CPU\translate('search') }}</button>
                            </div>
                        </form>
                        <div class="d-flex flex-wrap gap-2">
                            <div class="dropdown">
                                <button  type="button" id="dropdownMenuButton"
                                    class="btn btn-white text-primary d-flex align-items-center justify-content-center gap-2 flex-grow-1 h-100"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="d-none d-sm-block fs-13"> {{ \App\CPU\translate('Export') }}</span>
                                    <img src="{{ asset('assets/admin/img/download-new.svg') }}" alt="" class="svg">
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item mb-2" href="javascript:void(0)" onclick="exportList(this)" id="csv">
                                        <img class="" src="{{ asset('assets/admin/img/csv.png') }}" alt=""/>
                                        CSV
                                    </a>
                                    <a class="dropdown-item mb-2" href="javascript:void(0)" onclick="exportList(this)" id="xlsx">
                                        <img class="" src="{{ asset('assets/admin/img/excel.png') }}" alt=""/>
                                        Excel
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="exportList(this)" id="pdf">
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
                            <a href="{{ route('admin.category.add') }}"
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
                                                    <span class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('SL') }}</span>
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
                                                    <span class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Category Name') }}</span>
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

                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Description') }}</span>
                                                    <label class="toggle-switch toggle-switch-sm"
                                                           for="toggleColumn_description">
                                                        <input type="checkbox"
                                                               class="toggle-switch-input update-column-visibility"
                                                               id="toggleColumn_description" checked>
                                                        <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span
                                                        class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Total Product') }}</span>
                                                    <label class="toggle-switch toggle-switch-sm"
                                                           for="toggleColumn_product_count">
                                                        <input type="checkbox"
                                                               class="toggle-switch-input update-column-visibility"
                                                               id="toggleColumn_product_count" checked>
                                                        <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center mb-3">
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
                                                    <span class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Action') }}</span>
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
                        <th data-column="name">{{ \App\CPU\translate('category_name') }}</th>
                        <th data-column="description">{{ \App\CPU\translate('Description') }}</th>
                        <th data-column="product_count"
                            class="text-center">{{ \App\CPU\translate('total_product') }}</th>
                        <th data-column="status">{{ \App\CPU\translate('status') }}</th>
                        <th data-column="action" class="text-center">{{ \App\CPU\translate('action') }}</th>
                    </tr>

                    </thead>

                    <tbody>
                    @foreach($resources as $key => $resource)
                        @php
                            $isLastWithProducts = $resource['product_count'] > 0 && $resources->total() == 1;
                        @endphp
                        <tr>
                            <td data-column="sl">{{ $resources->firstitem() + $key }}</td>
                            <td data-column="name">
                                <div class="d-flex gap-2 align-items-center">
                                    <img src="{{ $resource['image_fullpath'] }}" class="img-two-cati object-cover rounded" alt="">
                                    <div>
                                        <div class="text-truncate max-w-180">
                                            {{$resource['name']}}
                                        </div>
                                        <div>ID #{{ $resource['id'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td data-column="description">
                                <div class="line-limit-2 text-wrap max-w-300">
                                    {{ $resource['description'] ?? 'N/A' }}
                                </div>
                            </td>
                            <td data-column="product_count" class="text-center">
                                {{ $resource['product_count'] }}
                            </td>
                            <td data-column="status">
                                <label class="toggle-switch toggle-switch-sm">
                                    <input type="checkbox" class="toggle-switch-input global-change-status"
                                           data-route="{{ route('admin.category.status', [$resource['id'], $resource->status ? 0 : 1]) }}"
                                           data-target="#globalChangeStatusModal"
                                           data-id="{{ $resource['id'] }}"
                                           data-title="{{ \App\CPU\translate('Are you sure') }}?"
                                           data-description="{{ $resource['status'] == 1 ? \App\CPU\translate('Want to turn off the status') : \App\CPU\translate('Want to turn on the status') }}"
                                           data-image="{{ asset('assets/admin/img/info.svg') }}"
                                        {{ $resource->status ? 'checked' : '' }}>
                                    <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                </label>
                            </td>
                            <td data-column="action">
                                <div class="d-flex justify-content-center align-items-center gap-3">
                                    <button type="button"
                                            class="btn btn-outline-info icon-btn offcanvas-toggle view-resource"
                                            data-id="{{ $resource['id'] }}"
                                            data-target="#offcanvasView"
                                            aria-label="View">
                                        <i class="fi fi-sr-eye"></i>
                                    </button>
                                    <button type="button"
                                            class="btn btn-outline-theme icon-btn offcanvas-toggle edit-resource"
                                            data-id="{{ $resource['id'] }}"
                                            data-target="#offcanvasEdit"
                                            aria-label="Edit">
                                        <i class="fi fi-sr-pencil"></i>
                                    </button>
                                    <button type="button"
                                            class="btn btn-outline-danger icon-btn
                                            {{ $isLastWithProducts ? 'disabled' : ($resource['product_count'] > 0 ? 'delete-resource-after-shifting' : 'delete-resource') }}"
                                            data-id="{{ $resource['id'] }}"
                                            @if ($isLastWithProducts)
                                                data-toggle="tooltip"
                                                data-placement="left"
                                                data-original-title="{{ \App\CPU\translate('This category contains products, so you cannot delete the last category') }}"
                                            @else
                                                data-target="{{ $resource['product_count'] > 0 ? '#deleteModalWithShift' : '#deleteModal' }}"
                                                data-toggle="modal"
                                                data-title="{{ \App\CPU\translate('Are you sure to delete this category') }}?"
                                                data-subtitle="{{ \App\CPU\translate('If once you delete this category, you will lost this category data permanently.') }}"
                                                data-cancel-text="{{ \App\CPU\translate('No') }}"
                                                data-confirm-text="{{ \App\CPU\translate('Delete') }}"
                                            @endif
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
                        {!! $resources->links() !!}
                    </tfoot>
                </table>
            </div>
            @if (count($resources) == 0)
                <div class="text-center p-4">
                    <img class="mb-3 w-one-cati"
                            src="{{ asset('assets/admin') }}/svg/illustrations/sorry.svg"
                            alt="Image Description">
                    <p class="mb-0">{{ \App\CPU\translate('No_data_to_show') }}</p>
                </div>
            @endif
        </div>
    </div>

    @include('admin-views.category.partials.offcanvas-filter')

    <div class="overlay" id="overlayEdit"></div>
    <div class="offcanvas-filter" id="offcanvasEdit" data-overlay="#overlayEdit">
    </div>

    <div class="overlay " id="overlayView"></div>
    <div class="offcanvas-filter" id="offcanvasView" data-overlay="#overlayView">
    </div>

    <span class="data-to-js"
          data-title="category-list"
          data-export-route="{{ route('admin.category.export') }}"
          data-view-route="{{ route('admin.category.render-view-canvas') }}"
          data-edit-route="{{ route('admin.category.render-edit-canvas') }}"
          data-delete-after-shifting-route="{{ route('admin.category.delete-after-shifting-modal') }}"
          data-delete-route="{{ route('admin.category.delete', ':id') }}"
    ></span>

@endsection

@push('script_2')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src={{ asset('assets/admin/js/global.js') }}></script>
    <script src={{ asset('assets/admin/js/text-showhide.js') }}></script>
    <script src={{ asset('assets/admin/js/custom-daterange.js') }}></script>
    <script>
        printFilterCount(['search', 'page']);

        initializeCanvasAjax(
            '.edit-resource',
            $('.data-to-js').data('edit-route'),
            '#offcanvasEdit',
            function () {
                initFileUpload();
                checkPreExistingImages();
                handleFormSubmit();
            }
        );

        initializeCanvasAjax(
            '.view-resource',
            $('.data-to-js').data('view-route'),
            '#offcanvasView',
            function () {
                $.getScript('{{ asset('assets/admin/js/text-showhide.js') }}');
                handleModalBackdrop('#view-update-status, #view-delete-resource', '#offcanvasView .edit-resource');
            }
        );

        initializeModalWithAjax(
            '.delete-resource-after-shifting',
            $('.data-to-js').data('delete-after-shifting-route'),
            '#deleteModalWithShift',
            true
        );

        restoreOverlayAfterModalClose();
    </script>
@endpush
