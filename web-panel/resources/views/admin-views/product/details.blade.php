@extends('layouts.admin.app')

@section('title',\App\CPU\translate('Product details'))

@section('content')
    <div class="content container-fluid">
        @include('admin-views.product.partials.inline-details')
        <div class="card">
            <div class="card-body">
                <div class="d-flex gap-3 align-items-center flex-wrap flex-md-nowrap">
                    <div>
                        <img  width="70" height="70" class="aspect-1 border object-cover rounded" src="{{onErrorImage($product->image,asset('storage/product/' . $product->image) ,asset('assets/admin/img/400x400/img2.jpg') ,'product/')}}" alt="">
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex gap-3 justify-content-between align-items-center flex-wrap mb-3">
                            <div class="">
                                <h2 class="mb-2">{{ $product->name }} #{{ $product->id }}</h2>
                                <p class="fs-12 mb-2">{{ \App\CPU\translate('Selling Price') }} : <span class="fw-bold">
                                        {{ \App\CPU\Helpers::currency_symbol() . ' ' . number_format($product->selling_price ?? 0, 2) }}
                                    </span></p>
                                <p class="fs-12 mb-0">{{ \App\CPU\translate('Product SKU Code') }} : <span class="fw-bold">
                                        {{ $product->product_code }}
                                    </span>
                                </p>
                            </div>
                            <div class="d-flex gap-3 align-items-center flex-wrap">
                                <div class="bg-soft-primary p-3 rounded text-center flex-grow-1">
                                    <h4 class="mb-2 fs-16">{{ $product->orderDetails->count() }}</h4>
                                    <p class="fs-12 font-weight-normal mb-0">{{ \App\CPU\translate('Total_Orders') }}</p>
                                </div>
                                <div class="bg-soft-primary p-3 rounded text-center flex-grow-1">
                                    <h4 class="mb-2 fs-16">{{ $product->orderDetails->sum('quantity') }}</h4>
                                    <p class="fs-12 font-weight-normal mb-0">{{ \App\CPU\translate('Total_Sold') }}</p>
                                </div>
                                <div class="bg-soft-primary p-3 rounded text-center flex-grow-1">
                                    <h4 class="mb-2 fs-16">{{ \App\CPU\Helpers::currency_symbol() . ' ' . number_format($product->orderDetails->sum(function($detail){ return ($detail->price * $detail->quantity) + $detail->tax - $detail->discount;}) ?? 0, 2) }}</h4>
                                    <p class="fs-12 font-weight-normal mb-0">{{ \App\CPU\translate('Total_Sold_Amount') }}</p>
                                </div>
                            </div>
                        </div>
                        <p class="fs-12 mb-0">
                            {{ $product->description ?? \App\CPU\translate('No description available') }}
                        </p>
                    </div>
                </div>
                <hr class="my-5">
                <div class="d-flex gap-3 justify-content-between justify-content-lg-around flex-wrap">
                    <div class="overflow-x-auto">
                        <table class="bg-transparent table table-borderless text-nowrap fs-12">
                            <tbody>
                                <tr>
                                    <td colspan="2"><h5>{{ \App\CPU\translate('General_Information') }}</h5></td>
                                </tr>
                                <tr>
                                    <td class="py-1">{{ \App\CPU\translate('Category') }}</td>
                                    <td class="py-1">: <span class="title ml-2">{{ $product?->category?->name }}</span></td>
                                </tr>
                                <tr>
                                    <td class="py-1">{{ \App\CPU\translate('Subcategory') }}</td>
                                    <td class="py-1">: <span class="title ml-2">{{ $product?->subcategory?->name ?? 'No subcategory' }}</span></td>
                                </tr>
                                <tr>
                                    <td class="py-1">{{ \App\CPU\translate('Band') }}</td>
                                    <td class="py-1">: <span class="title ml-2">{{ $product?->brand?->name ?? 'No brand' }}</span></td>
                                </tr>
                                <tr>
                                    <td class="py-1">{{ \App\CPU\translate('Product_Unit') }}</td>
                                    <td class="py-1">: <span class="title ml-2">{{ $product?->unit?->unit_type }}</span></td>
                                </tr>
                                <tr>
                                    <td class="py-1">{{ \App\CPU\translate('Quantity') }}</td>
                                    <td class="py-1">: <span class="title ml-2">{{ $product->quantity }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="bg-transparent table table-borderless text-nowrap fs-12">
                            <tbody>
                                <tr>
                                    <td colspan="2"><h5>{{ \App\CPU\translate('Price_Information') }}</h5></td>
                                </tr>
                                <tr>
                                    <td class="py-1">{{ \App\CPU\translate('Purchase_Price') }}</td>
                                    <td class="py-1">: <span class="title ml-2">{{ \App\CPU\Helpers::currency_symbol() . ' ' . number_format($product->purchase_price ?? 0, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <td class="py-1">{{ \App\CPU\translate('Selling_price') }}</td>
                                    <td class="py-1">: <span class="title ml-2">{{ \App\CPU\Helpers::currency_symbol() . ' ' . number_format($product->selling_price ?? 0, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <td class="py-1">{{ \App\CPU\translate('Tax') }}</td>
                                    <td class="py-1">: <span class="title ml-2">{{ $product?->tax }}%</span></td>
                                </tr>
                                <tr>
                                    <td class="py-1">{{ \App\CPU\translate('Discount_Type') }}</td>
                                    <td class="py-1">: <span class="title ml-2">{{ $product->discount_type }}</span></td>
                                </tr>
                                <tr>
                                    <td class="py-1">{{ $product->discount_type == 'percent' ? \App\CPU\translate('Discount Percent') : \App\CPU\translate('Discount Amount') }}</td>
                                    <td class="py-1">: <span class="title ml-2">{{ $product->discount_type == 'percent' ? $product->discount . '%': \App\CPU\Helpers::currency_symbol() . ' ' . number_format($product->discount ?? 0, 2) }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="bg-transparent table table-borderless text-nowrap fs-12">
                            <tbody>
                                <tr>
                                    <td colspan="2"><h5>{{ \App\CPU\translate('Supplier_Information') }}</h5></td>
                                </tr>
                                @if($product?->supplier)
                                    <tr>
                                        <td class="py-1">{{ \App\CPU\translate('Name') }}</td>
                                        <td class="py-1">: <span class="title ml-2">{{ $product?->supplier?->name }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="py-1">{{ \App\CPU\translate('Phone_Number') }}</td>
                                        <td class="py-1">: <span class="title ml-2">{{ $product?->supplier?->mobile }}</span></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class="py-1" colspan="2">{{ \App\CPU\translate('No supplier assigned') }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- delete modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <button type="button" class="text-dark bg-f2f2f2 rounded-circle p-1 close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            <i class="tio-clear"></i>
                        </span>
                    </button>
                </div>
                <div class="modal-body pt-0">
                    <form action="" method="post">
                        @csrf
                        @method('delete')
                        <div class="text-center">
                            <img width="80" height="80" src="{{ asset('assets/admin/img/delete.png') }}" alt="" class="mb-4">
                            <h3 class="mb-0">{{ \App\CPU\translate('are_you_sure_to_delete_this_product') }}?</h3>
                            <p class="mt-3">{{ \App\CPU\translate('want_to_delete_this_product') }}?</p>
                        </div>
                        <div class="d-flex gap-3 justify-content-center flex-wrap mt-5">
                            <button type="reset" class="btn btn-soft-dark px-4 font-weight-bold min-w-120px" data-dismiss="modal">{{ \App\CPU\translate('No') }}</button>
                            <button type="submit" class="btn btn-danger px-4 font-weight-bold min-w-120px">{{ \App\CPU\translate('Delete') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <span class="data-to-js"
          data-title="product-details"
          data-delete-route="{{ route('admin.product.delete', ':id') }}"
    >
@endsection

@push('script_2')
     <script src={{asset("assets/admin/js/global.js")}}></script>

@endpush
