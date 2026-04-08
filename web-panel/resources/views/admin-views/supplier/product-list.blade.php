@extends('layouts.admin.app')

@section('title',\App\CPU\translate('supplier_product_list'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div>
            <h1 class="page-header-title">{{ $supplier->name }}</h1>
        </div>
        <div class="js-nav-scroller hs-nav-scroller-horizontal">
            <ul class="nav nav-tabs page-header-tabs">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.supplier.view',[$supplier['id']]) }}">{{\App\CPU\translate('details')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('admin.supplier.products',[$supplier['id']]) }}">{{\App\CPU\translate('product_list')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.supplier.transaction-list',[$supplier['id']]) }}">{{\App\CPU\translate('transaction')}}</a>
                </li>
            </ul>

        </div>
    </div>

        <div class="row align-items-center mt-3 mb-3">
            <div class="col-sm  mb-sm-0">
                <h1 class="page-header-title"><i
                        class="tio-filter-list"></i> {{\App\CPU\translate('products_list')}}
                    <span class="badge badge-soft-dark ml-2">{{$products->total()}}</span>
                </h1>
            </div>
            @if (\App\CPU\Helpers::module_permission_check('product_section'))
                <div class="col-md-4">
                    <a href="{{route('admin.product.add')}}" class="btn btn-primary float-right">
                        <i class="tio-add-circle-outlined"></i>  {{\App\CPU\translate('product')}}
                    </a>
                </div>
            @endif
        </div>

    <div class="row gx-2 gx-lg-3">
        <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <div class="card">
                <div class="card-header">
                    <div class="row justify-content-between align-items-center flex-grow-1">
                        <div class="col-md-5  mb-lg-0 mt-2">
                            <form action="{{url()->current()}}" method="GET">
                                <div class="input-group input-group-merge input-group-flush">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="tio-search"></i>
                                        </div>
                                    </div>
                                    <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{\App\CPU\translate('search_by_product_code_or_name')}}" aria-label="{{\App\CPU\translate('Search')}}" value="{{ $search }}" required>
                                    <button type="submit" class="btn btn-primary">{{\App\CPU\translate('search')}}</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 mt-2">
                            <select name="qty_order_sort" class="form-control" id="sortOrderQtySelect">
                                <option value="default" {{ $sortOrderQty== "default"?'selected':''}}>{{\App\CPU\translate('default_sort')}}</option>
                                <option value="quantity_asc" {{ $sortOrderQty== "quantity_asc"?'selected':''}}>{{\App\CPU\translate('quantity_sort_by_(low_to_high)')}}</option>
                                <option value="quantity_desc" {{ $sortOrderQty== "quantity_desc"?'selected':''}}>{{\App\CPU\translate('quantity_sort_by_(high_to_low)')}}</option>
                                <option value="order_asc" {{ $sortOrderQty== "order_asc"?'selected':''}}>{{\App\CPU\translate('order_sort_by_(low_to_high)')}}</option>
                                <option value="order_desc" {{ $sortOrderQty== "order_desc"?'selected':''}}>{{\App\CPU\translate('order_sort_by_(high_to_low)')}}</option>
                            </select>
                       </div>

                    </div>
                </div>
                <div class="table-responsive datatable-custom">
                    <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                        <tr>
                            <th>{{\App\CPU\translate('#')}}</th>
                            <th>{{\App\CPU\translate('image')}}</th>
                            <th >{{\App\CPU\translate('name')}}</th>
                            <th>{{\App\CPU\translate('product_code')}}</th>
                            <th>{{\App\CPU\translate('quantity')}}</th>
                            <th>{{\App\CPU\translate('purchase_price')}}</th>
                            <th>{{\App\CPU\translate('selling_price')}}</th>
                            <th>{{ \App\CPU\translate('orders') }}</th>
                            <th class="text-center">{{\App\CPU\translate('action')}}</th>
                        </tr>
                        </thead>

                        <tbody id="set-rows">
                        @forelse($products as $key=>$product)
                            <tr>
                                <td>{{$products->firstitem()+$key}}</td>
                                <td>
                                    <img src="{{$product['image_fullpath']}}" class="img-one-spl">
                                </td>
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                            <a href="#">
                                            {{substr($product['name'],0,20)}}{{strlen($product['name'])>20?'...':''}}
                                            </a>
                                    </span>
                                </td>
                                <td>{{ $product['product_code'] }}</td>
                                <td>
                                    <button
                                        class="btn btn-sm update-quantity-btn title border min-w-120px d-flex justify-content-between align-items-center gap-2"
                                        data-id="{{ $product->id }}" type="button"
                                        data-toggle="modal"
                                        data-target="#update-quantity">
                                        {{ $product['quantity'] }}
                                        <i class="tio-add-circle"></i>
                                    </button>
                                </td>
                                <td>{{$product['purchase_price'] ." ".\App\CPU\Helpers::currency_symbol()}}</td>
                                <td>{{$product['selling_price'] ." ".\App\CPU\Helpers::currency_symbol()}}</td>
                                <td>{{ $product->order_count??0 }}</td>
                                <td data-column="action">
                                    <div class="d-flex justify-content-center align-items-center gap-3">
                                        <a class="btn btn-outline-primary icon-btn"
                                           href="{{ route('admin.product.edit', [$product['id']]) }}">
                                            <i class="fi fi-sr-pencil"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-outline-danger icon-btn delete-resource"
                                                data-id="{{ $product['id'] }}"
                                                data-target="#deleteModal"
                                                data-toggle="modal"
                                                data-title="{{ \App\CPU\translate('are_you_sure_to_delete_this_product') }}"
                                                data-subtitle="{{ \App\CPU\translate('If once you delete this product, you will lost this product data permanently.') }}"
                                                data-cancel-text="{{ \App\CPU\translate('No') }}"
                                                data-confirm-text="{{ \App\CPU\translate('Delete') }}"
                                        >
                                            <i class="fi fi-rr-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center p-4">
                                    <img class="mb-3 img-one-in"
                                         src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}"
                                         alt="{{\App\CPU\translate('Image Description')}}">
                                    <p class="mb-0">{{ \App\CPU\translate('No_data_to_show')}}</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                    {!! $products->links('layouts/admin/pagination/_pagination', ['perPage' => request()->get('per_page')]) !!}
                </div>
            </div>
        </div>
    </div>
</div>

<span class="data-to-js"
      data-delete-route="{{ route('admin.product.delete', ':id') }}"
      data-render-update-quantity-modal-route="{{ route('admin.stock.render-update-quantity-modal') }}"
>

@endsection

@push('script_2')
    <script src={{asset("assets/admin/js/global.js")}}></script>

    <script>
        "use strict";
        $('.update-quantity-btn').on('click', function() {
            var productId = $(this).data('product-id');
            update_quantity(productId);
        });

        $('#sortOrderQtySelect').on('change', function() {
            var selectedValue = $(this).val();
            var redirectUrl = '{{ url('/') }}/admin/supplier/products/{{ $supplier->id }}?sort_orderQty=' + selectedValue;
            window.location.href = redirectUrl;
        });

        initializeModalWithAjax(
            '.update-quantity-btn',
            $('.data-to-js').data('render-update-quantity-modal-route'),
            '#update-quantity'
        );
    </script>
@endpush
