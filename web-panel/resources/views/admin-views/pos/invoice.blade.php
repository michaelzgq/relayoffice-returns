@php
    use App\Models\BusinessSetting;
    use function App\CPU\translate;
    use App\CPU\Helpers;
@endphp
@if(isset($order))
    <div class="width-inone" data-id="{{ $order->id }}">
        <div class="text-center mb-3">
            <img src="{{onErrorImage(BusinessSetting::where(['key' => 'shop_logo'])->first()->value, asset('storage/shop').'/' . BusinessSetting::where(['key' => 'shop_logo'])->first()->value,asset('assets/admin/img/160x160/img2.jpg') ,'shop/')}}"
                 style="height: 27px; margin-bottom: 10px" alt="">
            <h5 class="style-inone">
                {{ BusinessSetting::where(['key' => 'shop_address'])->first()->value }}
            </h5>

            <h5 class="style-intwo">
                {{ translate('Vat Reg.') }}
                : {{ BusinessSetting::where(['key' => 'vat_reg_no'])->first()->value }}
            </h5>

            <hr class="line-dot">

            <div class="mt-3 text-center">
                <h5 class="style-intwo">{{ translate('Invoice') }} : {{ $order['id'] }}</h5>

                <h5 class="style-intwo">
                    {{ date('D, M d, Y h:i:s a', strtotime($order['created_at'])) }}
                </h5>
            </div>

            <hr class="line-dot">


            @if($order?->counter)
                <div class="mt-3 text-center d-flex justify-content-between">
                    <div class="style-intwo m-0">{{ translate('Counter No') }}</div>
                    <div class="style-intwo m-0">{{ $order?->counter->name }} - {{ $order?->counter->number }}</div>
                </div>

                <hr class="line-dot">
            @endif


            <h5 class="style-intwo d-flex gap-2 align-items-center justify-content-between">
                <span>{{ translate('Customer Name') }}</span>
                <span>{{ $order?->customer?->name }}</span>
            </h5>
            @if($order?->customer->id != 0)
                <h5 class="style-intwo d-flex gap-2 align-items-center justify-content-between">
                    <span>{{ translate('Phone') }}</span>
                    <span>{{ $order?->customer?->mobile ?? 'N/A' }}</span>
                </h5>
                <h5 class="style-intwo d-flex gap-2 align-items-center justify-content-between">
                    <span>{{ translate('Address') }}</span>
                    <span>{{ $order?->customer?->full_address }}</span>
                </h5>
            @endif

            @if($order?->details)
                <h5 class="style-intwo d-flex gap-2 align-items-center justify-content-between mt-5">
                    <span class="w-50 text-start">Products</span>
                    <span class="w-25 text-center">Qty</span>
                    <span class="w-25 text-end">Price</span>
                </h5>
                <hr class="line-dot mt-0">
                @foreach($order?->details as $key => $product)
                    @php
                        $productDetails = json_decode($product?->product_details, true) ?? [];
                    @endphp
                    <h5 class="style-intwo d-flex gap-2 align-items-center justify-content-between">
                        <span class="w-50 text-start">{{ ++$key }}. {{ $productDetails['name'] ?? 'No Product Name' }}</span>
                        <span class="w-25 text-center">{{ $product?->quantity }}</span>
                        <span class="w-25 text-end">{{ Helpers::currency_symbol() }}{{ number_format($product?->price * $product?->quantity ?? 0, 2)  }}</span>
                    </h5>
                @endforeach
            @endif

            <hr class="line-dot">
            <h5 class="style-intwo d-flex gap-2 align-items-center justify-content-between">
                <span>{{ translate('Subtotal') }}</span>
                <span>{{ Helpers::currency_symbol() }}{{ number_format($order?->details->map(function($product){ return  $product?->price * $product?->quantity;})->sum() ?? 0, 2) }}</span>
            </h5>

            <h5 class="style-intwo d-flex gap-2 align-items-center justify-content-between">
                <span>{{ translate('Discount') }}</span>
                <span>{{ Helpers::currency_symbol() }}{{ number_format($order?->details->map(function($product){  return  $product->discount_on_product * $product?->quantity;})->sum() ?? 0, 2) }}</span>
            </h5>

            <h5 class="style-intwo d-flex gap-2 align-items-center justify-content-between">
                <span>{{ translate('Coupon Discount') }}</span>
                <span>{{ Helpers::currency_symbol() }}{{ number_format($order?->coupon_discount_amount ?? 0, 2) }}</span>
            </h5>

            <h5 class="style-intwo d-flex gap-2 align-items-center justify-content-between">
                <span>{{ translate('Extra Discount') }}</span>
                <span>{{ Helpers::currency_symbol() }}{{ number_format($order?->extra_discount ?? 0, 2) }}</span>
            </h5>


            <h5 class="style-intwo d-flex gap-2 align-items-center justify-content-between">
                <span>{{ translate('tax') }}</span>
                <span>{{ Helpers::currency_symbol() }}{{ number_format($order?->total_tax ?? 0, 2) }}</span>
            </h5>

            <h5 class="style-intwo d-flex gap-2 align-items-center justify-content-between font-weight-bold">
                <span>{{ translate('total') }}</span>
                <span>
                    {{ Helpers::currency_symbol() }}{{ number_format(($order?->order_amount + $order?->total_tax) - ($order?->coupon_discount_amount ?? 0) - ($order?->extra_discount ?? 0) ,2) }}</span>
            </h5>

            <hr class="line-dot">

            <h5 class="style-intwo d-flex gap-2 align-items-center justify-content-between">
                <span>{{ translate('Paid By') }}</span>
                <span>{{ ($order?->account?->account == 0 ? translate('Wallet') : $order?->account?->account) ?? 'N/A' }}</span>
            </h5>

            <h5 class="style-intwo d-flex gap-2 align-items-center justify-content-between">
                <span>{{ translate('Paid Amount') }}</span>
                <span>{{ Helpers::currency_symbol() }}{{ number_format($order?->collected_cash ?? 0, 2) }}</span>
            </h5>


            @if(number_format($order->collected_cash - $order->order_amount - $order->total_tax + ($order->extra_discount ?? 0) + ($order->coupon_discount_amount ?? 0), 2)  != 0)
                <h5 class="style-intwo d-flex gap-2 align-items-center justify-content-between">
                    <span>{{ translate('Change Return') }}</span>
                    <span>{{ Helpers::currency_symbol() }} {{ number_format($order->collected_cash - $order->order_amount - $order->total_tax + ($order->extra_discount ?? 0) + ($order->coupon_discount_amount ?? 0), 2) }}</span>

                </h5>
            @endif

            @if($order?->refund)
                <h5 class="style-intwo d-flex gap-2 align-items-center justify-content-between font-weight-bold">
                    <span>{{ translate('Refund Amount') }}</span>
                    <span>{{ Helpers::currency_symbol() }} {{ number_format($order?->refund?->refund_amount, 2) }}</span>

                </h5>
            @if($order?->refund?->refund_reason)
                    <h5 class="style-intwo text-start gap-2">
                        {{ translate('Refund Reason:') .' '. translate($order?->refund?->refund_reason) }}
                    </h5>
            @endif
            @endif

            <hr class="line-dot">

            <h5 class="style-intwo text-center">{{ translate('Thank you for buying. Please visit ') }}{{ BusinessSetting::where('key', 'shop_name')->first()->value }} {{ translate('again.') }}.</h5>

            <hr class="line-dot">

            <h5 class="style-intwo text-center">{{ translate('Powered by ') }}{{ BusinessSetting::where('key', 'shop_name')->first()->value }}, {{ translate('Phone') }}
                : {{ BusinessSetting::where('key', 'shop_phone')->first()->value }}</h5>
        </div>
    </div>
@endif
