@extends('layouts.admin.app')

@section('title',\App\CPU\translate('add_new_product'))

@section('content')
    <div class="content container-fluid">
        <div class="">
            <div class="row align-items-center mb-3">
                <div class="col-sm">
                    <h1 class="page-header-title d-flex align-items-center g-2px text-capitalize mb-0">
                        <span>{{\App\CPU\translate('add_new_product')}}</span>
                    </h1>
                </div>
            </div>
        </div>
        <form action="{{route('admin.product.store')}}" method="post" id="store-or-update-data" enctype="multipart/form-data" >
            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-4">
                        <h3 class="mb-0">{{ \App\CPU\translate('Basic_Setup') }}</h3>
                    </div>
                    <div class="row gy-3">
                        <div class="col-lg-8">
                            <div class="bg-fafafa p-3 p-lg-4 rounded-10 h-100">
                                <div class="form-group">
                                    <label for="" class="title d-flex g-2px">
                                        {{ \App\CPU\translate('product_name') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="{{\App\CPU\translate('product_name')}}">
                                    <span class="error-text" data-error="name"></span>
                                </div>
                                <div class="form-group">
                                    <label for="" class="title d-flex g-2px">
                                        {{ \App\CPU\translate('Description') }}
                                    </label>
                                    <textarea name="description" id="" class="form-control"
                                        placeholder="{{ \App\CPU\translate('Type_description') }}"></textarea>
                                    <span class="error-text" data-error="description"></span>
                                </div>
                                <div class="form-group mb-0">
                                    <div class="d-flex align-items-center gap-3 justify-content-between">
                                        <label class="title d-flex g-2px"
                                            for="exampleFormControlSelect1">
                                            {{\App\CPU\translate('product_code_SKU')}}
                                            <span class="text-danger">*</span>
                                            <i class="fi fi-sr-info cursor-pointer ml-1 text-body" data-toggle="tooltip"
                                            title="{{ \App\CPU\translate('product_code_SKU') }}"></i>
                                        </label>
                                        <a class="cursor-pointer text-primary" id="generateCodeLink">{{\App\CPU\translate('generate_code')}}</a>
                                    </div>
                                    <input type="text" minlength="5" id="generate_number" name="product_code"
                                               class="form-control" value="{{ old('product_code') }}"
                                               placeholder="{{\App\CPU\translate('product_code')}}" >
                                    <span class="error-text" data-error="product_code"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div
                                class="bg-fafafa p-3 p-lg-4 rounded-10 d-flex justify-content-center align-items-center h-100">
                                <div class="text-center">
                                    <h4 class="mb-3">{{ \App\CPU\translate('Upload_Image') }}</h4>
                                    <label class="upload-file">
                                        <input type="file" name="image" id="customFileEg1" class="upload-file-input"
                                               accept="{{ IMAGE_ACCEPTED_EXTENSIONS }}" data-max-upload-size="{{ readableUploadMaxFileSize('image') }}"
                                            >
                                        <button type="button" class="remove_btn btn btn-danger">
                                            <i class="fi fi-sr-cross"></i>
                                        </button>
                                        <div class="upload-file-wrapper w-200px h-auto">
                                            <div class="upload-file-textbox p-3 rounded bg-white border-dashed w-100 h-100">
                                                <div
                                                    class="d-flex flex-column justify-content-center align-items-center gap-1 h-100">
                                                    <i class="fi fi-sr-camera lh-1 fs-16 text-primary"></i>
                                                    <p class="fs-10 mb-0">{{ \App\CPU\translate('Add_image') }}</p>
                                                </div>
                                            </div>
                                            <img class="upload-file-img" loading="lazy" src="" data-default-src="" alt="">
                                        </div>
                                    </label>
                                    <p class="mb-0 title fs-12 mt-4">{{ getFileFormatSizeTranslatedText(IMAGE_ACCEPTED_EXTENSIONS) }}<span
                                            class="fw-bold">(1:1)</span></p>
                                    <span class="error-text justify-content-center" data-error="image"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-4">
                        <h3 class="mb-0">{{ \App\CPU\translate('General_Setup') }}</h3>
                    </div>
                    <div class="bg-fafafa p-3 p-lg-4 rounded-10 h-100">
                        <div class="row gy-3">
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="" class="title d-flex g-2px">
                                        {{ \App\CPU\translate('category') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="category_id" class="form-control js-select2-custom" >
                                        <option value="" selected disabled>{{\App\CPU\translate('select_category')}}</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category['id']}}" {{ old('category_id')==$category['id']?'selected':''}}>{{$category['name']}}</option>
                                        @endforeach
                                    </select>
                                    <span class="error-text" data-error="category_id"></span>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="" class="title">{{ \App\CPU\translate('sub_category') }}</label>
                                    <select name="sub_category_id" id="sub-categories" class="form-control js-select2-custom">
                                        <option value="" selected disabled>{{\App\CPU\translate('select_sub_category')}}</option>
                                    </select>
                                    <span class="error-text" data-error="sub_category_id"></span>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="" class="title">{{ \App\CPU\translate('brand') }}</label>
                                    <select name="brand_id" class="form-control js-select2-custom">
                                        <option value="" selected disabled>{{\App\CPU\translate('select_brand')}}</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{$brand['id']}}" {{ old('brand_id')==$brand['id']?'selected':''}}>{{$brand['name']}}</option>
                                        @endforeach
                                    </select>
                                    <span class="error-text" data-error="brand_id"></span>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="" class="title d-flex g-2px">{{ \App\CPU\translate('quantity') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="number" min="0" name="quantity" class="form-control"
                                               value="{{ old('quantity') }}"
                                               placeholder="{{\App\CPU\translate('quantity')}}" >
                                    <span class="error-text" data-error="quantity"></span>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="" class="title d-flex g-2px">
                                        {{ \App\CPU\translate('Reorder_Level') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" min="1" name="reorder_level" class="form-control"
                                               value="{{old('reorder_level')}}"
                                               placeholder="{{\App\CPU\translate('Ex : 4593')}}" >
                                    <span class="error-text" data-error="reorder_level"></span>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="" class="title d-flex g-2px">
                                        {{ \App\CPU\translate('unit_type') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group-wrapper border rounded">
                                        <input type="number" min="0" step="0.01" name="unit_value" class="form-control"
                                               value="{{ old('unit_value') }}"
                                         placeholder="{{\App\CPU\translate('unit_value')}}" >
                                        <div class="min-w-100px">
                                            <select name="unit_type" class="form-control js-select2-custom">
                                                @foreach($units as $unit)
                                                    <option value="{{$unit['id']}}" {{ old('unit_type')==$unit['id']?'selected':''}}>{{$unit['unit_type']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <span class="error-text" data-error="unit_value"></span>
                                    <span class="error-text" data-error="unit_type"></span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-4">
                        <h3 class="mb-0">{{ \App\CPU\translate('price_&_discount') }}</h3>
                    </div>
                    <div class="bg-fafafa p-3 p-lg-4 rounded-10 h-100">
                        <div class="row gy-3">
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="" class="title d-flex g-2px">
                                        {{ \App\CPU\translate('Selling_Price') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="0.01" name="selling_price" class="form-control"
                                               value="{{ old('selling_price') }}"
                                               placeholder="{{\App\CPU\translate('selling_price')}}" >
                                    <span class="error-text" data-error="selling_price"></span>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="" class="title d-flex g-2px">{{ \App\CPU\translate('purchase_price') }} <span
                                            class="text-danger">*</span></label>
                                   <input type="number" step="0.01" name="purchase_price" class="form-control"
                                               value="{{ old('purchase_price') }}"
                                               placeholder="{{\App\CPU\translate('purchase_price')}}" >
                                    <span class="error-text" data-error="purchase_price"></span>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="" class="title d-flex g-2px">
                                        {{ \App\CPU\translate('discount_type') }}
                                    </label>
                                    <div class="input-group-wrapper border rounded">
                                        <input type="number" min="0" name="discount" class="form-control"
                                               value=""
                                               placeholder="{{\App\CPU\translate('amount')}}">
                                        <div class="min-w-100px">
                                            <select name="discount_type"
                                                class="form-control js-select2-custom">
                                                <option
                                                    value="percent" {{ old('discount_type')=='percent'?'selected':''}}>{{\App\CPU\translate('percent')}}</option>
                                                <option
                                                    value="amount" {{ old('discount_type')=='amount'?'selected':''}}>{{\App\CPU\translate('amount')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <span class="error-text" data-error="discount"></span>
                                    <span class="error-text" data-error="discount_type"></span>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="" class="title d-flex g-2px">
                                        {{ \App\CPU\translate('tax_in_percent') }} (%)
                                    </label>
                                    <input type="number" min="0" name="tax" class="form-control"
                                               value="{{ old('tax') }}" placeholder="{{\App\CPU\translate('tax_amount')}}">
                                    <span class="error-text" data-error="tax"></span>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="" class="title">{{ \App\CPU\translate('supplier') }}</label>
                                    <select class="form-control js-select2-custom" name="supplier_id"
                                                id="supplier_id">
                                        <option value="" selected disabled>{{\App\CPU\translate('select_supplier')}}</option>
                                        @foreach ($suppliers as $supplier)
                                            <option
                                                value="{{$supplier['id']}}" {{ old('supplier_id')==$supplier['id']?'selected':''}}>{{$supplier['name']}}
                                                    ({{ $supplier['mobile'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="error-text" data-error="supplier_id"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="mb-4">
                        <h3 class="mb-0">{{ \App\CPU\translate('Availability') }}</h3>
                    </div>
                    <div class="bg-fafafa p-3 p-lg-4 rounded-10 h-100">
                        <div class="row gy-3">
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="" class="title d-flex g-2px">
                                        {{ \App\CPU\translate('available_time_starts') }}
                                    </label>
                                    <div class="position-relative">
                                        <input type="text" name="available_time_started_at" id="available_time_starts" class="form-control pr-6 timePicker" autocomplete="off" readonly>
                                        <span class="time-picker-icon position-absolute right-0 top-0 px-3 h-100 d-flex justify-content-center align-items-center"><i class="fi fi-rr-clock"></i></span>
                                    </div>
                                </div>
                                <span class="error-text" data-error="available_time_started_at"></span>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="" class="title d-flex g-2px">
                                        {{ \App\CPU\translate('available_time_ends') }}
                                    </label>
                                    <div class="position-relative">
                                        <input type="text" name="available_time_ended_at" id="available_time_ends" class="form-control pr-6 timePicker" autocomplete="off" readonly>
                                        <span class="time-picker-icon position-absolute right-0 top-0 px-3 h-100 d-flex justify-content-center align-items-center"><i class="fi fi-rr-clock"></i></span>
                                    </div>
                                </div>
                                <span class="error-text" data-error="available_time_ended_at"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end flex-wrap gap-3 mt-4">
                <button type="reset" class="btn btn-light fw-semibold min-w-120px">{{ \App\CPU\translate('reset') }}</button>
                <button type="submit"  class="btn btn-primary fw-semibold min-w-120px">{{ \App\CPU\translate('submit') }}</button>
            </div>
        </form>
    </div>
@endsection

@push('script_2')
    <script src={{asset("assets/admin/js/global.js")}}></script>

    <script>
        "use strict";

        $(document).ready(function() {

            $('#generateCodeLink').on('click', function(e) {
                e.preventDefault();
                document.getElementById('generate_number').value = getRndInteger();
            });

            $('select[name="category_id"]').on('change', function() {
                getRequest('{{url('/')}}/admin/product/get-categories?parent_id=' + $(this).val(), 'sub-categories');
            });

            $('select[name="sub_category_id"]').on('change', function() {
                getRequest('{{url('/')}}/admin/product/get-categories?parent_id=' + $(this).val(), 'sub-sub-categories');
            });

            $('select[name="discount_type"]').on('change', function() {
                discount_option(this);
            });
        });


    </script>

@endpush
