@extends('layouts.admin.app')

@section('title',\App\CPU\translate('stock_limit_products_list'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h1 class="page-header-title d-flex align-items-center gap-2 text-capitalize h3">
                <span>{{\App\CPU\translate('stock_limit_product_list')}}</span>
                <span class="badge bg-primary text-white">{{$products->total()}}</span>
            </h1>
            <p class="fs-12 mb-0">{{ \App\CPU\translate('the_products_are_shown_in_this_list,_which_quantity_is_below_reorder_level') }}</p>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="w-100">
                    <div class="d-flex flex-wrap justify-content-between gap-3">
                        <form action="{{ route('admin.stock.stock-limit') }}" method="GET">
                            @foreach(request()->except(['search', 'page']) as $key => $value)
                                @if(is_array($value))
                                    @foreach($value as $v)
                                        <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <div class="input-group input-group-merge input-group-flush">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="tio-search"></i>
                                    </div>
                                </div>
                                <input id="datatableSearch_" type="search" name="search" class="form-control"
                                       placeholder="{{\App\CPU\translate('search_by_product')}}..."
                                       aria-label="{{\App\CPU\translate('Search')}}" value="{{ $search }}">
                                <button type="submit" class="btn btn-primary">{{\App\CPU\translate('search')}}</button>
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
                                    <a class="dropdown-item mb-2" href="javascript:" onclick="exportList(this)"
                                       id="csv">
                                        <img class="" src="{{ asset('assets/admin/img/csv.png') }}" alt=""/>
                                        CSV
                                    </a>
                                    <a class="dropdown-item mb-2" href="javascript:" onclick="exportList(this)"
                                       id="xlsx">
                                        <img class="" src="{{ asset('assets/admin/img/excel.png') }}" alt=""/>
                                        Excel
                                    </a>
                                    <a class="dropdown-item" href="javascript:" onclick="exportList(this)"
                                       id="pdf">
                                        <img class="" src="{{ asset('assets/admin/img/pdf.png') }}" alt=""/>
                                        PDF
                                    </a>
                                </div>
                            </div>
                            <div class="d-flex flex-end position-relative show-filter-count">
                                <button
                                    type="button"
                                    class="offcanvas-toggle btn btn-white d-flex align-items-center justify-content-center gap-3 flex-grow-1 h-44px-mobile"
                                    data-target="#offcanvasFilterProduct"
                                    aria-label="Toggle filter menu"
                                >
                                    <i class="fi fi-rr-bars-filter fs-16 lh-1"></i>
                                </button>
                            </div>
                            <a href="{{ route('admin.stock.stock-limit') }}"
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
                                                        class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('SL')}}</span>

                                                    <label class="toggle-switch toggle-switch-sm"
                                                           for="toggleColumn_sl">
                                                        <input type="checkbox" class="toggle-switch-input update-column-visibility"
                                                               id="toggleColumn_sl" checked>
                                                        <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span
                                                        class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('product_name')}}</span>

                                                    <label class="toggle-switch toggle-switch-sm"
                                                           for="toggleColumn_name">
                                                        <input type="checkbox" class="toggle-switch-input update-column-visibility"
                                                               id="toggleColumn_name" checked>
                                                        <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span
                                                        class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('supplier_info')}}</span>

                                                    <label class="toggle-switch toggle-switch-sm"
                                                           for="toggleColumn_supplier_info">
                                                        <input type="checkbox" class="toggle-switch-input update-column-visibility"
                                                               id="toggleColumn_supplier_info" checked>
                                                        <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span
                                                        class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('category')}}</span>

                                                    <label class="toggle-switch toggle-switch-sm"
                                                           for="toggleColumn_category">
                                                        <input type="checkbox" class="toggle-switch-input update-column-visibility"
                                                               id="toggleColumn_category" checked>
                                                        <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span
                                                        class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('quantity')}}</span>

                                                    <label class="toggle-switch toggle-switch-sm"
                                                           for="toggleColumn_quantity">
                                                        <input type="checkbox" class="toggle-switch-input update-column-visibility"
                                                               id="toggleColumn_quantity" checked>
                                                        <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span
                                                        class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('orders')}}</span>

                                                    <label class="toggle-switch toggle-switch-sm"
                                                           for="toggleColumn_total_ordered">
                                                        <input type="checkbox" class="toggle-switch-input update-column-visibility"
                                                               id="toggleColumn_total_ordered" checked>
                                                        <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span
                                                        class="mr-2 fs-13 title text-capitalize">{{\App\CPU\translate('status')}}</span>

                                                    <label class="toggle-switch toggle-switch-sm"
                                                           for="toggleColumn_stock_status">
                                                        <input type="checkbox" class="toggle-switch-input update-column-visibility"
                                                               id="toggleColumn_stock_status" checked>
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
                        <th data-column="sl">{{\App\CPU\translate('SL')}}</th>
                        <th data-column="name">{{\App\CPU\translate('product_name')}}</th>
                        <th data-column="supplier_info">{{ \App\CPU\translate('supplier_info') }}</th>
                        <th data-column="category">{{\App\CPU\translate('category')}}</th>
                        <th data-column="quantity">{{\App\CPU\translate('quantity')}}</th>
                        <th data-column="total_ordered" class="text-end">{{ \App\CPU\translate('orders') }}</th>
                        <th data-column="stock_status">{{\App\CPU\translate('status')}}</th>
                    </tr>

                    </thead>

                    <tbody id="set-rows">
                    @foreach($products as $key=>$product)
                        <tr>
                            <td data-column="sl">{{$products->firstitem()+$key}}</td>
                            <td data-column="name">
                                <div class="d-flex gap-2 align-items-center">
                                    <img src="{{ $product['image_fullpath'] }}"
                                         class="img-two-cati object-cover rounded">
                                    <div>
                                        <a href="{{ route('admin.product.show', [$product['id']]) }}">
                                            <div class="text-truncate max-w-180">
                                                {{ $product['name'] }}
                                            </div>
                                        </a>
                                        <div>ID # {{ $product['product_code'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td data-column="supplier_info">
                                @if($product?->supplier)
                                    {{ $product->supplier->name }} <br> {{ $product->supplier->mobile }}
                                @else
                                    {{ \App\CPU\translate('N/A') }}
                                @endif
                            </td>
                            <td data-column="category">{{$product?->category?->name}}</td>
                            <td data-column="quantity">
                                <button
                                    class="btn btn-sm update-quantity-btn title border min-w-120px d-flex justify-content-between align-items-center gap-2"
                                    data-id="{{ $product->id }}" type="button"
                                    data-toggle="modal" data-target="#update-quantity">
                                    {{ $product['quantity'] }}
                                    <i class="tio-add-circle"></i>
                                </button>
                            </td>
                            <td data-column="total_ordered" class="text-end">{{ $product->order_count ?? 0 }}</td>
                            <td data-column="stock_status">
                                    <span
                                        class="badge  {{ $product->quantity < 1 ? 'badge-soft-danger' : 'badge-soft-warning' }} font-weight-normal fs-13 px-3 py-2">
                                        {{ $product->quantity < 1 ? 'Out of Stock' : 'Low Stock' }}
                                    </span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="page-area d-flex justify-content-end">
                <table>
                    <tfoot>
                    {!! $products->links() !!}
                    </tfoot>
                </table>
            </div>
            @if(count($products)==0)
                <div class="text-center p-4">
                    <img class="mb-3 img-two-sto" src="{{asset('assets/admin')}}/svg/illustrations/sorry.svg"
                         alt="{{ \App\CPU\translate('Image Description')}}">
                    <p class="mb-0">{{ \App\CPU\translate('No_data_to_show')}}</p>
                </div>
            @endif
        </div>
    </div>

    @include('admin-views.stock.partials.offcanvas-filter')

    <span class="data-to-js"
          data-title="stock-limit-product-list"
          data-export-route="{{ route('admin.stock.export') }}"
          data-render-update-quantity-modal-route="{{ route('admin.stock.render-update-quantity-modal') }}"
    >

    </span>
@endsection

@push('script_2')
    <script src={{asset("assets/admin/js/global.js")}}></script>
    <script src={{asset("assets/admin/js/range-slider-init.js")}}></script>

    <script>
        "use strict";

        $(document).ready(function () {
            function fetchSubCategories() {
                $(document).on('change', '.category-checkbox', function () {
                    $('#subCategorySelectAll').prop('checked', false);
                    var selectedCategories = [];

                    $('.category-checkbox:checked').each(function () {
                        selectedCategories.push($(this).val());
                    });

                    let allChecked = $('.category-checkbox').length > 0 && $('.category-checkbox:checked').length === $('.category-checkbox').length;
                    $('#categorySelectAll').prop('checked', allChecked);

                    $.ajax({
                        url: '{{ route("admin.pos.subcategories") }}',
                        method: 'GET',
                        data: {
                            category_ids: selectedCategories
                        },
                        success: function (response) {
                            let remainingSubcategories = response.subcategories.length - 6;
                            $('#seeMoreSubcategory').val(remainingSubcategories);
                            if (response.subcategories.length > 0) {
                                $('#subcategory-section').removeClass('d-none');
                                $('#subcategoryFilter').empty();

                                response.subcategories.forEach(function (subcategory) {
                                    $('#subcategoryFilter').append(`
                            <div class="col-sm-6">
                                <label class="form-control mb-3">
                                    <div class="check-item">
                                        <div class="d-flex form-group form-check form--check m-0">
                                            <input type="checkbox" name="subcategory_ids[]" value="${subcategory.id}" class="form-check-input subcategory-checkbox">
                                            <span class="align-content-center form-check-label line-limit-1 text-left ml-2 text-dark fs-12" title="${subcategory.name}">${subcategory.name}</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        `);
                                });

                                if (remainingSubcategories > 0) {
                                    $('#see_more_subcategory_btn').removeClass('d-none');
                                    $('#more-sub-category-count').text(` (${remainingSubcategories})`);
                                    if ($('#see_more_subcategory').length && $('#subcategoryFilter').length) {
                                        if ($('#subcategoryFilter').hasClass('expanded')) {
                                            $('#see_more_subcategory').html('<strong>See less</strong>');
                                        } else {
                                            $('#see_more_subcategory').html(`<strong>See more <span>(${remainingSubcategories})</span></strong>`);
                                        }
                                    }
                                } else {
                                    $('#see_more_subcategory_btn').addClass('d-none');
                                }
                                $('#subCategorySelectAll').off('click').on('click', function () {
                                    var isChecked = $(this).prop('checked');
                                    $('.subcategory-checkbox').prop('checked', isChecked);

                                    // Call toggleFilter only if the necessary elements exist
                                    if ($('#see_more_subcategory').length && $('#subcategoryFilter').length && $('#seeMoreSubcategory').length) {
                                        if (!isChecked) {
                                            $('#subcategoryFilter').removeClass('expanded');
                                            $('#see_more_subcategory').html(`<strong> See more (${$('#seeMoreSubcategory').val()})</strong>`);
                                        } else {
                                            $('#subcategoryFilter').addClass('expanded');
                                            $('#see_more_subcategory').html('<strong>See less</strong>');
                                        }
                                    }
                                });
                                $(document).on('change', '.subcategory-checkbox', function () {
                                    let allChecked = $('.subcategory-checkbox').length > 0 && $('.subcategory-checkbox:checked').length === $('.subcategory-checkbox').length;
                                    $('#subCategorySelectAll').prop('checked', allChecked);
                                });
                            } else {
                                $('#subcategory-section').addClass('d-none');
                            }
                        }
                    });
                });
            }

            fetchSubCategories();

            function updateSelectAllCheckbox() {
                let allChecked = $('.category-checkbox').length > 0 && $('.category-checkbox:checked').length === $('.category-checkbox').length;
                $('#categorySelectAll').prop('checked', allChecked);
                if (allChecked) {
                    $('#categoryFilter').addClass('expanded');
                    $('#see_more_category').html('<strong>See less</strong>');
                } else {
                    $('#categoryFilter').removeClass('expanded');
                    $('#see_more_category').html(`<strong> See more (${$('#seeMoreCategory').val()})</strong>`);
                }
            }

            updateSelectAllCheckbox();

            function updateSelectAllSubcategoryCheckbox() {
                let allChecked = $('.subcategory-checkbox').length > 0 && $('.subcategory-checkbox:checked').length === $('.subcategory-checkbox').length;
                $('#subCategorySelectAll').prop('checked', allChecked);
                if (allChecked) {
                    $('#subcategoryFilter').addClass('expanded');
                    $('#see_more_subcategory').html('<strong>See less</strong>');
                } else {
                    $('#subcategoryFilter').removeClass('expanded');
                    $('#see_more_subcategory').html(`<strong> See more (${$('#seeMoreSubcategory').val()})</strong>`);
                }
            }

            updateSelectAllSubcategoryCheckbox();

            function updateSelectAllBrandCheckbox() {
                let allChecked = $('.brand-checkbox').length > 0 && $('.brand-checkbox:checked').length === $('.brand-checkbox').length;
                $('#brandSelectAll').prop('checked', allChecked);
                if (allChecked) {
                    $('#brandFilter').addClass('expanded');
                    $('#see_more_brand').html('<strong>See less</strong>');
                } else {
                    $('#brandFilter').removeClass('expanded');
                    $('#see_more_brand').html(`<strong> See more (${$('#seeMoreBrand').val()})</strong>`);
                }
            }

            updateSelectAllBrandCheckbox();

            $('#categorySelectAll').on('click', function () {
                var isChecked = $(this).prop('checked');
                $('.category-checkbox').prop('checked', isChecked);
                $('.category-checkbox').trigger('change');
                if (!isChecked) {
                    $('#subCategorySelectAll').prop('checked', false);
                    $('#categoryFilter').removeClass('expanded');
                    $('#see_more_category').html(`<strong> See more (${$('#seeMoreCategory').val()})</strong>`);
                } else {
                    $('#categoryFilter').addClass('expanded');
                    $('#see_more_category').html('<strong>See less</strong>');
                }
            })

            $('#subCategorySelectAll').off('click').on('click', function () {
                var isChecked = $(this).prop('checked');
                $('.subcategory-checkbox').prop('checked', isChecked);

                if ($('#see_more_subcategory').length && $('#subcategoryFilter').length && $('#seeMoreSubcategory').length) {
                    if (!isChecked) {
                        $('#subcategoryFilter').removeClass('expanded');
                        $('#see_more_subcategory').html(`<strong> See more (${$('#seeMoreSubcategory').val()})</strong>`);
                    } else {
                        $('#subcategoryFilter').addClass('expanded');
                        $('#see_more_subcategory').html('<strong>See less</strong>');
                    }
                }
            });

            $(document).on('change', '.subcategory-checkbox', function () {
                let allChecked = $('.subcategory-checkbox').length > 0 && $('.subcategory-checkbox:checked').length === $('.subcategory-checkbox').length;
                $('#subCategorySelectAll').prop('checked', allChecked);
            });

            $('#brandSelectAll').on('click', function () {
                var isChecked = $(this).prop('checked');
                $('.brand-checkbox').prop('checked', isChecked);
                if (!isChecked) {
                    $('#brandFilter').removeClass('expanded');
                    $('#see_more_brand').html(`<strong> See more (${$('#seeMoreBrand').val()})</strong>`);
                } else {
                    $('#brandFilter').addClass('expanded');
                    $('#see_more_brand').html('<strong>See less</strong>');
                }
            })

            $(document).on('change', '.brand-checkbox', function () {
                let allChecked = $('.brand-checkbox').length > 0 && $('.brand-checkbox:checked').length === $('.brand-checkbox').length;
                $('#brandSelectAll').prop('checked', allChecked);
            });

            function toggleFilter(buttonId, filterId, valueId) {
                const button = document.getElementById(buttonId);
                const filter = document.getElementById(filterId);
                const value = document.getElementById(valueId);

                filter.classList.toggle('expanded');

                if (filter.classList.contains('expanded')) {
                    button.innerHTML = '<strong>See less</strong>';
                } else {
                    button.innerHTML = `<strong>See more <span>(${value.value})</span></strong>`;
                }
            }

            const seeMoreCategory = document.getElementById('see_more_category');
            if (seeMoreCategory) {
                seeMoreCategory.addEventListener('click', function () {
                    toggleFilter('see_more_category', 'categoryFilter', 'seeMoreCategory');
                });
            }


            const seeMoreSubcategory = document.getElementById('see_more_subcategory');
            if (seeMoreSubcategory) {
                seeMoreSubcategory.addEventListener('click', function () {
                    toggleFilter('see_more_subcategory', 'subcategoryFilter', 'seeMoreSubcategory');
                });
            }

            const seeMoreBrand = document.getElementById('see_more_brand');
            if (seeMoreBrand) {
                seeMoreBrand.addEventListener('click', function () {
                    toggleFilter('see_more_brand', 'brandFilter', 'seeMoreBrand');
                });
            }
        });
        printFilterCount(['search', 'page']);

        initializeModalWithAjax(
            '.update-quantity-btn',
            $('.data-to-js').data('render-update-quantity-modal-route'),
            '#update-quantity'
        );
    </script>
@endpush
