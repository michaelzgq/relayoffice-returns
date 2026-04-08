<div id="product-id-{{ $product->id }}" class="position-relative">

    <input type="hidden" id="product_id" name="id" value="{{ $product->id }}">
    <input type="hidden" id="product_qty" name="quantity" value="1">

    @php
        use Carbon\Carbon;

        $cartItem = $cartItemsById[$product->id] ?? null;
        $isInCart = !is_null($cartItem);
        $quantity = $isInCart ? $cartItem['quantity'] : 0;
        $inputDisabled = $isInCart ? '' : 'disabled';
        $inputKey = $isInCart ? $loop->index : -1;
        $discountedPrice = $product['selling_price'] - \App\CPU\Helpers::discount_calculate($product, $product['selling_price']);

        $now = Carbon::now();
        $startTime = $product->available_time_started_at ? Carbon::parse($product->available_time_started_at) : null;
        $endTime = $product->available_time_ended_at ? Carbon::parse($product->available_time_ended_at) : null;

        $isAvailable = true;
        if ($startTime && $endTime) {
            $isAvailable = $now->between($startTime, $endTime);
        }
    @endphp

    <div class="pos-product-item {{ $isInCart ? 'active' : '' }}" id="single-product-card-{{ $product->id }}">

        <div class="pos-product-item_thumb single-cart-data" data-id="{{ $product->id }}">
            <img
                src="{{ onErrorImage($product['image'],asset('storage/product/' . $product['image']) ,asset('assets/admin/svg/components/product-default.svg') ,'product/') }}"
                class="img-fit" alt="{{ $product['name'] }}">

            <div class="hover-content">
                <div class="counter-container increment-decrement-section" data-id="{{ $product->id }}">
                    <button class="btn-decrement btn-number" data-type="minus" data-field="quantity">-</button>

                    <input type="number"
                           name="quantity"
                           id="quantity"
                           value="{{ $quantity }}"
                           min="1"
                           max="100"
                           data-key="{{ $inputKey }}"
                           class="counter-input qty-width single_card_input_{{ $product->id }}"
                        {{ $inputDisabled }}>

                    <button class="btn-increment btn-number" data-type="plus" data-field="quantity">+</button>
                </div>
            </div>


            @if($isAvailable)
                <div class="hover-content-text">
                    <div class="text">
                        {{ \App\CPU\translate('Add to Cart') }}
                    </div>
                </div>
            @else
                <div class="unavailable-overlay position-absolute top-0 start-0 w-100 h-100  d-flex align-items-center justify-content-center text-white rounded">
                    <div class="overlay-content p-3">
                        {{ \App\CPU\translate('Product Not Available') }}
                    </div>
                </div>
            @endif
        </div>

        <div class="pos-product-item_content">
            <div class="pos-product-item_title {{ $isAvailable ? 'pos-single-product-card' : '' }} " data-id="{{ $product->id }}">
                {{ $product['name'] }}
            </div>
            <div class="pos-product-item_unit">
                <span class="text-muted">
                    {{ $product->unit_value . ' ' . $product?->unit?->unit_type }}
                </span>
            </div>
            <div class="pos-product-item_price">
                @if($product->discount > 0)
                    <div class="fz-10 text-muted text-decoration">
                        {{ $product['selling_price'] . ' ' . \App\CPU\Helpers::currency_symbol() }}
                    </div>
                @endif
                <div>
                    {{ $discountedPrice . ' ' . \App\CPU\Helpers::currency_symbol() }}
                </div>
            </div>
        </div>
    </div>

</div>
