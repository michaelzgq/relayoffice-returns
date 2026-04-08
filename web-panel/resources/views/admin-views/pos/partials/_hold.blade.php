<div class="offcanvas-filter" id="offcanvasHoldMenu">
    <div class="offcanvas-filter__header">
        <h4>{{ \App\CPU\translate('Hold_Orders') }} <span class="badge badge-danger ml-2">{{ count($holdCarts) }}</span></h4>
        <p>{{ \App\CPU\translate('Your hold orders') }}</p>
        <div class="input-group-overlay input-group-merge input-group-custom">
            <div class="input-group-prepend">
                <div class="input-group-text"><i class="tio-search"></i></div>
            </div>
            <input id="hold-order-search" type="text" class="form-control search-bar-input" placeholder="{{ \App\CPU\translate('search_by_hold_id_or_customer_info') }}" aria-label="Search here" autocomplete="off">
            <div class="pos-search-card position-absolute z-index-1 w-100">
                <div id="search-box" class="card card-body search-result-box d--none p-2"></div>
            </div>
        </div>
    </div>

    @if(count($holdCarts) > 0)
        <div class="offcanvas-filter__body pt-3">
            <div class="hold-card">
                @foreach($holdCarts as $index => $cartData)
                    <div class="single-hold-card">
                        <a href="{{ route('admin.pos.cancel-hold-order', ['cart_id' => $cartData['id']]) }}">
                            <i class="tio-clear-circle card_close_btn"></i>
                        </a>
                        <div class="px-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>{{ \App\CPU\translate('Hold ID') }}: <strong>{{ $cartData['id'] }}</strong></h5>
                                <div>
                                    <h6 class="mb-0 text-capitalize">{{ $cartData['customer']?->name ?? __('Guest') }}</h6>
                                    @if($cartData['user_type'] === 'sc')
                                        <a href="javascript:void(0)">{{ $cartData['customer']?->mobile ?? '' }}</a>
                                    @endif
                                </div>
                            </div>

                            <hr>
                            <div class="d-flex justify-content-between align-items-center pb-4">
                                <button type="button" class="btn text-primary hold-product-list-btn" id="hold_product_list_{{ $index }}">
                                    {{ count($cartData['items']) }} {{ \App\CPU\translate('items') }}
                                </button>
                                <a href="{{ route('admin.pos.change-cart', ['cart_id' => $cartData['id']]) }}" class="btn btn-primary text-white">
                                    {{ \App\CPU\translate('Resume') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="table-container hidden mt-3" id="table_{{ $index }}">
                        <table class="table table-dark table-head-borderless table-nowrap table-align-middle mb-0">
                            <thead>
                            <tr><th colspan="4" class="text-center text-white">{{ \App\CPU\translate('Product_List') }}</th></tr>
                            </thead>
                            <tbody>
                            @foreach($cartData['items'] as $item)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.product.barcode-generate', [$item['id']]) }}" target="_blank">
                                            <img class="img-one-cl" src="{{ asset('storage/product/' . $item['image']) }}" alt="">
                                        </a>
                                    </td>
                                    <td>
                                        <a class="text-white product-name" href="{{ route('admin.product.barcode-generate', [$item['id']]) }}" target="_blank">
                                            {{ $item['name'] }}
                                        </a>
                                    </td>
                                    <td class="text-white">{{ $item['quantity'] }}</td>
                                    <td class="text-white">{{ $item['price'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <p class="text-center mt-4">{{ \App\CPU\translate('No Hold Orders') }}</p>
    @endif

    <div class="offcanvas-filter__footer bg-white py-2 d-flex align-items-center">
        <div class="d-flex justify-content-center align-items-center w-100">
            <button type="button" class="btn btn-soft-primary mr-2 px-4" id="cancel_hold">
                {{ \App\CPU\translate('Cancel') }}
            </button>
        </div>
    </div>
</div>
