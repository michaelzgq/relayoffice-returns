@extends('layouts.admin.app')

@section('title', $product->name . ' ' . \App\CPU\translate('barcode'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/barcode.css"/>
@endpush

@section('content')
<div class="content container-fluid">
    @include('admin-views.product.partials.inline-details')
    <div class="row gy-2 h-100">
        <div class="col-md-6 d-flex">
            <div class="card flex-fill pos-barcode">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{$product['image_fullpath']}}" alt="Product Image">
                        <div class="ml-3">
                            <h5 class="fs-16 mb-1">{{ \App\CPU\translate('Product') }} # {{ $product->id }}</h5>
                            <h5 class="fs-16 mb-2">{{ Str::limit($product->name,30) }}</h5>
                            <p class="mb-0 fs-13">{{ \App\CPU\translate('Barcode SKU') }} : {{ $product->product_code }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 d-flex">
            <div class="card flex-fill pos-barcode">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <h5 class="fs-16 mb-0">{{ \App\CPU\translate('Generate Barcode') }}</h5>
                            <span class="fs-12">{{ \App\CPU\translate('Input number to print barcode. You can print') }} <strong class="text-dark">{{ \App\CPU\translate('maximum 270 copies') }}</strong>{{ \App\CPU\translate(' at a time.') }}</span>
                        </div>
                        <a class="btn text-primary reset-btn text-nowrap" href="{{ route('admin.product.barcode-generate',[$product['id']]) }}">
                            <i class="tio-refresh"></i>
                            {{\App\CPU\translate('reset')}}
                        </a>
                    </div>
                    <form action="{{ url()->current() }}" method="GET" class="mt-3">
                        <div>
                            <label class="fs-12 font-weight-semibold text-dark">{{ \App\CPU\translate('Number of Barcode') }}</label>
                            <div class="d-flex justify-content-between gap-2 align-items-start">
                                <div class="flex-grow-1">
                                    <input type="number" class="form-control me-2" name="limit" value="{{ $limit }}">
                                </div>
                                <button type="submit" class="btn btn-primary float-right ml-3">{{ \App\CPU\translate('Generate') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12">
            <h1 class="style-one-br p-4 show-div2">
                {{\App\CPU\translate("This page is for A4 size page printer, so it won't be visible in smaller devices")}}.
            </h1>
            <div class="card pos-barcode">
                <div class="card-body">
                    <div id="printareaTop" class="d-flex justify-content-between align-items-center pb-3 pt-2">
                        <h5 class="fs-16 mb-0">{{ \App\CPU\translate('A4 Size Paper Preview') }}</h5>
                        <button type="button" id="print_bar" data-name="printarea" data-title="{{ 'barcode-' . $product->product_code }}" class="btn btn-primary print-div"><i class="tio-print"></i> {{\App\CPU\translate('print')}}</button>
                    </div>
                    <div id="printarea" class="show-div">
                        <div class="card">
                            <div class="card-body">
                                @if ($limit)
                                    <div class="barcodea4-new m-0 p-2">
                                        <div class="item-wrapper">
                                            @for ($i = 0; $i <$limit; $i++)
                                            @if($i%21==0 && $i!=0)
                                        </div>
                                    </div>
                                    <div class="barcodea4-new m-0 p-2">
                                        <div class="item-wrapper">
                                            @endif
                                            <div class="item">
                                                <span class="barcode_site text-capitalize">{{ \App\Models\BusinessSetting::where('key','shop_name')->first()->value }}</span>
                                                <span class="barcode_name text-capitalize">{{Str::limit($product->name,30)}}</span>
                                                <span class="barcode_price text-capitalize mb-2">{{ $product['selling_price'] . ' ' . \App\CPU\Helpers::currency_symbol() }}</span>
                                                <div>
                                                    <div class="barcode_image">{!! DNS1D::getBarcodeHTML($product->product_code, "C128") !!}</div>
                                                </div>
                                                <span class="barcode_code text-capitalize mt-2">{{$product->product_code}}</span>
                                            </div>
                                            @endfor
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



@push('script_2')
    <script src={{asset("assets/admin/js/global.js")}}></script>
@endpush
