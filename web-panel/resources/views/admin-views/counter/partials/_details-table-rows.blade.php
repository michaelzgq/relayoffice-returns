@forelse($orders as $key => $order)
    <tr>
        <td data-column="sl">{{ $orders->firstitem()+$key }}</td>
        <td data-column="order_id">#{{ $order['id'] }}</td>

        <td data-column="order_date">{{date('d M Y',strtotime($order['created_at']))}}</td>
        <td data-column="customer_info">
            @if($order->user_id != 0)
                @if($order->customer)
                    {{ $order?->customer?->name }}
                    <br>
                    {{ $order?->customer?->mobile }}
                @else
                    <span
                        class="badge badge-danger">{{ \App\CPU\translate('Customer_Deleted') }}</span>
                @endif
            @else
                {{ \App\CPU\translate('Walk-In_Customer') }}
            @endif
        </td>
        <td data-column="total_amount">
            {{ number_format($order->order_amount + $order->total_tax - $order->coupon_discount_amount - ($order->extra_discount ?? 0), 2) . ' ' . \App\CPU\Helpers::currency_symbol()}}
        </td>
        <td data-column="paid_by" class="text-capitalize">
            {{ ($order->payment_id != 0) ? ($order->account ? $order->account->account : \App\CPU\translate('account_deleted')): \App\CPU\translate('Customer balance') }}
        </td>
        <td data-column="action">
            <div class="d-flex justify-content-center gap-2">
                <button class="btn btn-sm btn-white print-invoice" type="button"
                        data-id="{{$order->id}}"><i
                        class="tio-download"></i> {{\App\CPU\translate('invoice')}}
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center p-4">
            <img class="mb-3 img-one-in" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="{{\App\CPU\translate('Image Description')}}">
            <p class="mb-0">{{ \App\CPU\translate('No_data_to_show')}}</p>
        </td>
    </tr>
@endforelse
