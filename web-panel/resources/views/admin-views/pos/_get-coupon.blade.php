@forelse($coupons as $coupon)
    <div class="swiper-slide">
        <div class="coupon-slider-item">
            <button class="coupon-slider-button coupon-{{$coupon->code }}" data-coupon="{{$coupon->code}}" type="button">
                <div class="left">
                    <h6 class="line-limit-1">{{ \App\CPU\translate('Code') }} : {{ $coupon->code }}</h6>
                    <small class="line-limit-1">{{ $coupon->title }}</small>
                </div>
                <div class="right">
                    <h6>{{ $coupon['discount_type'] == 'amount' ? $coupon['discount']." ".\App\CPU\Helpers::currency_symbol() : $coupon['discount']."%" }}</h6>
                    <small>{{ \App\CPU\translate('Discount') }}</small>
                </div>
                <i class="tio-checkmark-circle checkmark"></i>
            </button>
        </div>
    </div>
@empty
    <h6>{{ \App\CPU\translate('no_coupon_available') }}</h6>
@endforelse
