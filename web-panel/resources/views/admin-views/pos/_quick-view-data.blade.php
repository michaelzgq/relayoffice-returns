<button type="button" class="close position-absolute top-0 right-0 p-2 z-index-99" data-dismiss="modal"
        aria-label="Close">
    <span aria-hidden="true">
        <i class="tio-clear-circle modal-close-btn"></i>
    </span>
</button>

<?php
// Get current cart from session
$cardId = session('current_user');
$cardName = $cardId ? session($cardId) : null;

// Check if the product exists in the cart
$isInCart = $cardName && in_array($product->id, array_column($cardName['items'], 'id'));
$index = $isInCart ? array_search($product->id, array_column($cardName['items'], 'id')) : null;

$productQuantity = $isInCart ? $cardName['items'][$index]['quantity'] : 1;
?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-4">
            <div class="product-image d-flex justify-content-center align-items-center">
                <img src="{{ onErrorImage($product['image'],asset('storage/product/' . $product['image']) ,asset('assets/admin/svg/components/product-default.svg') ,'product/') }}"
                     data-zoom="{{ onErrorImage($product['image'],asset('storage/product/' . $product['image']) ,asset('assets/admin/svg/components/product-default.svg') ,'product/') }}"
                     alt="{{\App\CPU\translate('product_image')}}">
            </div>
            <div class="mt-3">
                <h6>{{\App\CPU\translate('Categories')}}:
                    @foreach($product->getCategories() as $category)
                        <strong>{{ $category->name }}</strong>
                    @endforeach
                </h6>
                @if($product->brands)
                    <h6>{{\App\CPU\translate('Brands')}}: <strong>{{ $product?->brands?->name }}</strong></h6>
                @endif
            </div>
        </div>
        <div class="col-md-8">
            <span class="badge badge-soft-success mb-2">
                <i class="tio-checkmark-circle-outlined"></i> {{\App\CPU\translate('In_Stock')}}: {{ $product->quantity }}
            </span>
            <h4 class="mb-2">{{ Str::limit($product->name, 26) }}</h4>
            <h6><span>{{\App\CPU\translate('SKU')}} : {{ $product->product_code }}</span></h6>
            <h6 class="d-flex align-items-center gap-2">
                <span class="text-muted">
                    {{ \App\CPU\translate('SKU') }} :
                    <span class="fw-semibold text-dark">{{ $product->product_code }}</span>
                </span>

                <span class="custom-vr mx-2"></span>

                <span class="text-muted">
                    {{ \App\CPU\translate('Unit') }} :
                    <span class="fw-semibold text-dark">
                        {{ $product->unit_value . ' ' . $product?->unit?->unit_type }}
                    </span>
                </span>
            </h6>



            <div class="d-flex align-items-center gap-2 mb-4">
                @if($product->discount > 0)
                    <span
                        class="prev-price text-line-through">{{ $product['selling_price'] . ' ' . \App\CPU\Helpers::currency_symbol() }}</span>
                @endif
                <span
                    class="new-price">{{ ($product['selling_price']- \App\CPU\Helpers::discount_calculate($product, $product['selling_price'])) . ' ' . \App\CPU\Helpers::currency_symbol() }}</span>
            </div>
            @if ($isInCart)
                <div class="d-inline-block">
                    <div class="d-flex align-items-center gap-3">
                        <span class="qty">{{\App\CPU\translate('qty')}}</span>
                        <div class="quantity">
                            <button class="minus btn-number" aria-label="Decrease" type="button" data-type="minus"
                                    data-field="quantity">&minus;
                            </button>
                            <input type="number" class="input-box input-number product-updated-quantity" name="quantity"
                                   id="product-quantity" value="{{ $productQuantity }}" min="1"
                                   max="{{ $product->quantity }}">
                            <button class="plus btn-number" aria-label="Increase" type="button" data-type="plus"
                                    data-field="quantity">&plus;
                            </button>
                        </div>
                        <button type="button" class="btn btn-primary add-to-cart"
                                data-id="{{ $product->id }}">{{\App\CPU\translate('Update Cart')}}</button>
                    </div>
                    <span class="total-price_btn w-100 mt-4">
                        <span>{{\App\CPU\translate('Total Price')}}: </span>
                        <span class="text-primary" id="chosen_price">
                            {{ ($productQuantity * ($product['selling_price']- \App\CPU\Helpers::discount_calculate($product, $product['selling_price']))) }}
                        </span> {{ \App\CPU\Helpers::currency_symbol() }}
                        <span class="tax-text">({{\App\CPU\translate('Tax')}}:
                            <span class="tax-text" id="total-tax"></span>
                            {{ \App\CPU\Helpers::currency_symbol() }})
                        </span>
                    </span>
                </div>
            @else
                <div class="d-inline-block">
                    <div class="d-flex align-items-center gap-3">
                        <span class="qty">{{\App\CPU\translate('qty')}}</span>
                        <div class="quantity">
                            <button class="minus btn-number" aria-label="Decrease" type="button" data-type="minus"
                                    data-field="quantity">&minus;
                            </button>
                            <input type="number" class="input-box input-number" name="quantity" id="product-quantity"
                                   value="1" min="1" max="{{ $product->quantity }}">
                            <button class="plus btn-number" aria-label="Increase" type="button" data-type="plus"
                                    data-field="quantity">&plus;
                            </button>
                        </div>
                        <button type="button" class="btn btn-primary add-to-cart"
                                data-id="{{ $product->id }}">{{\App\CPU\translate('Add to Cart')}}</button>
                    </div>
                    <span class="total-price_btn w-100 mt-4">
                        <span>{{\App\CPU\translate('Total Price')}}: </span>
                        <span class="text-primary" id="chosen_price"></span> {{ \App\CPU\Helpers::currency_symbol() }}
                        <span class="tax-text">({{\App\CPU\translate('Tax')}}:
                            <span class="tax-text" id="total-tax"></span>
                            {{ \App\CPU\Helpers::currency_symbol() }})
                        </span>
                    </span>
                </div>
            @endif

        </div>
    </div>
</div>
<?php
$productPrice = ($product['selling_price'] - \App\CPU\Helpers::discount_calculate($product, $product['selling_price']));
$singleProductTax = \App\CPU\Helpers::tax_calculate($product, $product->selling_price);
?>

<script>
    "use strict";

    $(".modal-close-btn").on('click', function () {
        $('#quick-view').modal('hide');
    });


    $(".add-to-cart").on('click', function () {
        let product_id = $(this).data('id');
        let quantity = $('#product-quantity').val();
        addToCartData(product_id, quantity);
        $('#quick-view').modal('hide');
    });

    $(document).ready(function () {
        let productPrice = {{ $productPrice }};
        let tax = {{ $singleProductTax }};

        function updateTotalPrice(quantity) {
            let totalPrice = (productPrice + tax) * quantity;
            let totalTax = tax * quantity;
            $('#chosen_price').text(totalPrice.toFixed(2));
            $('#total-tax').text(totalTax.toFixed(2));
        }

        $('.plus').click(function () {
            let quantity = parseInt($('#product-quantity').val());
            let maxQuantity = parseInt($('#product-quantity').attr('max'));
            if (quantity < maxQuantity) {
                $('#product-quantity').val(quantity + 1);
                updateTotalPrice(quantity + 1);
            }
        });

        $('.minus').click(function () {
            let quantity = parseInt($('#product-quantity').val());
            if (quantity > 1) {
                $('#product-quantity').val(quantity - 1);
                updateTotalPrice(quantity - 1);
            }
        });

        $('#product-quantity').on('input', function () {
            let quantity = parseInt($(this).val());
            let maxQuantity = parseInt($(this).attr('max'));
            if (quantity >= 1 && quantity <= maxQuantity) {
                updateTotalPrice(quantity);
            } else {
                let validQuantity = (quantity < 1) ? 1 : maxQuantity;
                $('#product-quantity').val(validQuantity);
                updateTotalPrice(validQuantity);
            }
        });

        let initialQty = $('.product-updated-quantity').val() ?? 1;
        updateTotalPrice(initialQty);
    });

    function addToCartData(product_id, quantity) {
        let productId = product_id;
        let productQty = quantity;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.post({
            url: '{{ route('admin.pos.add-to-cart-data') }}',
            data: {
                _token: '{{csrf_token()}}',
                id: productId,
                quantity: productQty,
            },
            beforeSend: function () {
                $('#double-click-prevent').removeClass('d-none');
            },
            success: function (data) {
                if (data.qty == 0) {
                    toastr.warning('{{\App\CPU\translate('product_quantity_end!')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                } else {
                    toastr.success('{{\App\CPU\translate('item_has_been_added_in_your_cart!')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }

                $('#cart').empty().html(data.view);
                if (data.user_type === 'sc') {
                    customer_Balance_Append(data.user_id);
                }
                $('#search').val('').focus();
                $('#search-box').addClass('d-none');
                $('#product-id-' + productId).find('.pos-product-item').find('.counter-input').val(productQty);
                $('#product-id-' + productId).find('.pos-product-item').addClass('active')
                handleDecrementButtonDisabled();
                $('#double-click-prevent').addClass('d-none');
            },
        });

    }

</script>

