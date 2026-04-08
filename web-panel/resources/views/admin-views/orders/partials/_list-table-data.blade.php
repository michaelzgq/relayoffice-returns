<div class="table-responsive ">
    <table class="table table-hover table-thead-bordered table-nowrap table-align-middle card-table">
        <thead class="thead-light">
        <tr>
            <th data-column="order_sl" class="title w-100px">SL</th>
            <th data-column="order_id" class="title table-column-pl-0 text-capitalize w-100px">Order ID</th>
            <th data-column="order_date" class="title text-capitalize w-100px">Order Date</th>
            <th data-column="customer_info" class="title text-capitalize w-100px">Customer Info</th>
            <th data-column="counter_info" class="title text-capitalize w-100px">Counter Info</th>
            <th data-column="items" class="title text-capitalize w-100px">Items</th>
            <th data-column="order_amount" class="title text-capitalize text-end w-100px">Order Amount</th>
            <th data-column="discount" class="title text-capitalize w-100px">Discount</th>
            <th data-column="vat_tax" class="title text-capitalize text-end w-100px">Vat/Tax</th>
            <th data-column="total_amount" class="title text-capitalize text-end w-100px">Total Amount</th>
            <th data-column="paid_by" class="title text-capitalize w-100px">Paid By</th>
            <th data-column="order_status" class="title text-capitalize text-center w-100px">Order Status</th>
            <th data-column="action" class="title text-capitalize text-center w-100px">Action</th>
        </tr>
        </thead>

        <tbody id="set-rows">
        @foreach($orders as $key => $order)
            @php
                $currency = \App\CPU\Helpers::currency_symbol();
                $orderIndex = ($orders->currentPage() - 1) * $orders->perPage() + $key + 1;
                $orderId = $order?->id;
                $orderDate = $order?->created_at?->format('d M, Y');
                $orderTime = $order?->created_at?->format('h:i A');
                $customerName = $order?->customer?->name ?? 'N/A';
                $customerMobile = $order?->customer?->mobile ?? 'N/A';
                $counterInfo = ($order?->counter?->name && $order?->counter?->number)
                    ? $order?->counter?->name . '-' . $order?->counter?->number
                    : 'N/A';
                $orderQty = $order?->details?->count();
                $orderQtyLabel = $orderQty > 1 ? 'items' : 'item';
                $totals = $order?->details?->reduce(
                                                fn($carry, $product) => [
                                                    'subtotal' => $carry['subtotal'] + ($product->price * $product->quantity),
                                                    'totalDiscount' => $carry['totalDiscount'] + ($product->discount_on_product * $product->quantity),
                                                ],
                                                ['subtotal' => 0, 'totalDiscount' => 0]
                                            );
                $subtotal = $totals['subtotal'];
                $totalDiscount = $totals['totalDiscount'];
                $totalTax = $order?->total_tax ?? 0;
                $extraDiscount = $order?->extra_discount ?? 0;
                $couponDiscount = $order?->coupon_discount_amount ?? 0;
                $totalAmount = $order?->order_amount + $order?->total_tax - $extraDiscount - $couponDiscount;
                $formattedTotalAmount = ($totalAmount > 0)
                    ? $currency . ' ' . number_format($totalAmount, 2)
                    : 0;
                $paidBy = $order->payment_id == 0 ? 'Wallet' : ($order?->account?->account ?? 'N/A');
                $orderStatus = $order?->order_status->label();
                $statusClass = $order?->order_status == \App\Enums\Order\OrderStatus::COMPLETED
                    ? 'badge-soft-success'
                    : 'badge-soft-primary';
            @endphp

            <tr>
                <td data-column="order_sl" class="title">{{ $orderIndex }}</td>
                <td data-column="order_id" class="title table-column-pl-0"><a href="{{ route('admin.order.details', ['id' => $orderId, 'type' => request()->input('type')]) }}">#{{ $orderId }}</a></td>
                <td data-column="order_date" class="title">
                    {{ $orderDate }} <br> {{ $orderTime }}
                </td>
                <td data-column="customer_info" class="title">
                    {{ $customerName }} @if($order?->customer?->id) <br> {{ $customerMobile }} @endif
                </td>
                <td data-column="counter_info" class="title">{{ $counterInfo }}</td>
                <td data-column="items" class="title">
                    <a href="javascript:void(0)" class="text-underline order-items"
                       data-url="{{ route('admin.order.order-items-menu', $orderId) }}"
                       aria-controls="offcanvasOrderItemsMenu" aria-expanded="false"
                       aria-label="Toggle Order Item Quantity menu">
                        {{ $orderQty }} {{ $orderQtyLabel }}
                    </a>
                </td>
                <td data-column="order_amount" class="title text-end">
                    {{ $currency . ' ' . number_format($subtotal, 2) }}
                </td>
                <td data-column="discount" class="title">
                    <dl class="row">
                        <dt class="col-6 title font-weight-normal pr-10">Discount :</dt>
                        <dd class="col-6 text-end">
                            {{ $totalDiscount > 0 ? $currency . ' ' . number_format($totalDiscount, 2) : $currency . 0 }}
                        </dd>
                        <dt class="col-6 title font-weight-normal pr-10">Extra Discount :</dt>
                        <dd class="col-6 text-end">
                            {{ $extraDiscount > 0 ? $currency . ' ' . $extraDiscount : $currency . 0 }}
                        </dd>
                        <dt class="col-6 title font-weight-normal pr-10">Coupon Discount :</dt>
                        <dd class="col-6 text-end">
                            {{ $couponDiscount > 0 ? $currency . ' ' . $couponDiscount : $currency . 0  }}
                        </dd>
                    </dl>
                </td>
                <td data-column="vat_tax" class="title text-end">
                    {{ $totalTax > 0 ? $currency . ' ' . number_format($totalTax, 2) : $currency . 0 }}
                </td>
                <td data-column="total_amount" class="title text-end">{{ $formattedTotalAmount }}</td>
                <td data-column="paid_by" class="title">{{ $paidBy }}</td>
                <td data-column="order_status" class="text-center">
                    <span class="badge {{ $statusClass }} px-2 rounded-full">{{ $orderStatus }}</span>
                </td>
                <td data-column="action">
                    <div class="d-flex align-items-center justify-content-center">
                        <a href="{{ route('admin.order.details', ['id' => $orderId, 'type' => request()->input('type')]) }}"
                           class="btn btn-outline-info icon-btn mr-2">
                            <span class="tio-visible"></span>
                        </a>
                        <a href="javascript:void(0)" class="download-invoice btn btn-outline-theme icon-btn"
                           data-url="{{ route('admin.pos.invoice', $orderId) }}">
                            <i class="tio-arrow-large-downward"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="page-area">
    <table>
        <tfoot>
        {!! $orders->links() !!}
        </tfoot>
    </table>
</div>
@if(count($orders) < 1)
    <div class="text-center p-4">
        <img class="mb-3 w-one-carsi" src="{{asset('assets/admin')}}/svg/illustrations/sorry.svg"
             alt="Image Description">
        <p class="mb-0">{{ \App\CPU\translate('No_data_to_show')}}</p>
    </div>
@endif
