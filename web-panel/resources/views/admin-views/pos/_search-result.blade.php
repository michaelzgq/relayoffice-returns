@php use Carbon\Carbon; @endphp
@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('assets/admin') }}/css/custom.css"/>
@endpush

<ul class="list-group list-group-flush product-search-list" id="productList">
    @forelse($products as $product)
        @php

            $now = Carbon::now();
            $startTime = $product->available_time_started_at ? Carbon::parse($product->available_time_started_at) : null;
            $endTime = $product->available_time_ended_at ? Carbon::parse($product->available_time_ended_at) : null;
            $isAvailable = true;

            if ($startTime && $endTime) {
                $isAvailable = $now->between($startTime, $endTime);
            }

            $discountedPrice = $product['selling_price'] - \App\CPU\Helpers::discount_calculate($product, $product['selling_price']);
        @endphp

        <li class="list-group-item position-relative" style="overflow: hidden; border-radius: 8px;">
            <a href="#"
               data-product-id="{{ $product->id }}"
               class="add-to-cart-link d-flex align-items-start gap-3 text-decoration-none"
               style="{{ $isAvailable ? '' : 'pointer-events: none; opacity: 0.6;' }}"
            >
                <img src="{{ $product['image_fullpath'] }}"
                     class="rounded"
                     width="60"
                     height="60"
                     style="object-fit: contain; flex-shrink: 0;">

                <div class="d-flex justify-content-between flex-grow-1 align-items-start">
                    <div class="pe-2">
                        <h6 class="mb-1 fw-semibold text-truncate-2">{{ $product['name'] }}</h6>
                        <div>
                            @if($product->discount > 0)
                                <small class="text-muted">
                                    <del>{{ $product['selling_price'] . ' ' . \App\CPU\Helpers::currency_symbol() }}</del>
                                </small>
                                <span class="ms-2 fw-bold text-dark">
                            {{ $discountedPrice . ' ' . \App\CPU\Helpers::currency_symbol() }}
                        </span>
                            @else
                                <span class="fw-bold text-dark">
                            {{ $product['selling_price'] . ' ' . \App\CPU\Helpers::currency_symbol() }}
                        </span>
                            @endif
                        </div>
                    </div>

                    <div class="text-end fz-12 ps-2">
                        <div class="mb-1">{{ \App\CPU\translate('Sku no') }}: {{ $product['product_code'] }}</div>
                        <div>{{ \App\CPU\translate('Stock') }}:
                            <span class="text-success">{{ $product['quantity'] }}</span>
                        </div>
                    </div>
                </div>
            </a>

            @unless($isAvailable)
                <div class="unavailable-overlay">
                    <div class="overlay-content">
                        {{ \App\CPU\translate('Product Not Available') }}
                    </div>
                </div>
            @endunless
        </li>
    @empty
        <div class="bg-soft-secondary p-3 rounded text-center">
            <img src="{{ asset('assets/admin/img/no-product.png') }}" alt="img">
            <p>{{ \App\CPU\translate('No Product Found ') }}</p>
        </div>
    @endforelse
</ul>

<script>
    "use strict";

    $('#productList').on('click', '.add-to-cart-link', function(e) {
        e.preventDefault();
        var productName = $(this).text();
        $('.search-bar-input-mobile').val(productName);
        $('.search-bar-input').val(productName);
        var productId = $(this).data('product-id');
        addToCart(productId);
    });
</script>
