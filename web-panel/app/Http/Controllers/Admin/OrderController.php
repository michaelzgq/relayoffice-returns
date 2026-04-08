<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Enums\Order\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Refund\StoreRefundRequest;
use App\Models\Account;
use App\Models\BusinessSetting;
use App\Models\Counter;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Refund;
use App\Models\Transection;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Rap2hpoutre\FastExcel\FastExcel;
use function App\CPU\translate;
use App\Traits\TransactionTrait;

class OrderController extends Controller
{
    use TransactionTrait;

    private Account $account;
    private Product $product;
    private Customer $customer;
    private OrderDetail $order_detail;
    private Transection $transection;
    private Counter $counter;
    private BusinessSetting $business_setting;
    private Refund $refund;

    public function __construct(private Order           $order,
                                Account                 $account,
                                Product         $product,
                                Customer        $customer,
                                OrderDetail     $order_detail,
                                Transection     $transection,
                                Counter         $counter,
                                Refund         $refund,
                                BusinessSetting $business_setting)
    {
        $this->refund = $refund;
        $this->business_setting = $business_setting;
        $this->counter = $counter;
        $this->transection = $transection;
        $this->order_detail = $order_detail;
        $this->customer = $customer;
        $this->product = $product;
        $this->account = $account;

    }

    public function list(): View
    {
        if (!in_array(request()->input('type'), ['all', ...OrderStatus::values()])) {
            abort(404);
        }
        $filters = [
            'type' => request()->input('type', 'all'),
            'start_date' => request()->input('start_date'),
            'end_date' => request()->input('end_date'),
            'customer_id' => request()->input('customer_id'),
            'payment_method_id' => request()->input('payment_method_id', []),
            'counter_id' => request()->input('counter_id'),
            'search' => request()->input('search'),
        ];
        $data['type'] = request()->input('type');
        $data['orderCount'] = $this->getFilteredOrders($filters)->count();
        $data['orders'] = $this->getFilteredOrders($filters)->paginate(Helpers::pagination_limit())->appends($filters);
        $data['customers'] = $this->customer->all();
        $data['counters'] = $this->counter->where('status', 1)->get();
        $data['accounts'] = $this->account->whereNotIn('account', ['Payable', 'Receivable'])->get();
        return View('admin-views.orders.list', $data);
    }

    public function orderItemsMenu($id): JsonResponse
    {
        $order = $this->order->with('details')->find($id);
        $orderedItems = $order->details->map(function ($item) {
            $productDetails = json_decode($item->product_details, true);
            return [
                'name' => $productDetails['name'],
                'quantity' => $item->quantity,
                'unit_type' => Unit::where('id', $productDetails['unit_type'])->first()?->unit_type ?? 'N/A',
                'unit_value' => $productDetails['unit_value'],
                'total_price' => (($item->price * $item->quantity) + ($item->tax_amount * $item->quantity)) - ($item->discount_on_product * $item->quantity),
                'image' => $productDetails['image'],
            ];
        });
        $countItems = $order->details->count();
        $orderId = $id;

        return response()->json(view('admin-views.orders.partials._show-order-items-menu', compact('orderedItems', 'countItems', 'orderId'))->render());
    }

    public function search(): JsonResponse
    {
        $filters = [
            'type' => request()->input('type', 'all'),
            'start_date' => request()->input('start_date'),
            'end_date' => request()->input('end_date'),
            'customer_id' => request()->input('customer_id'),
            'payment_method_id' => request()->input('payment_method_id', []),
            'counter_id' => request()->input('counter_id'),
            'search' => request()->input('search'),
        ];

        $orders = $this->getFilteredOrders($filters);
        $data['orderCount'] = $orders->count();
        $data['orders'] = $orders->paginate(Helpers::pagination_limit())->withPath('');

        return response()->json([
            'view' => view('admin-views.orders.partials._list-table-data', $data)->render(),
            'count' => $data['orderCount'],
        ]);
    }

    private function getFilteredOrders(array $filters)
    {
        return $this->order->with(['customer', 'counter', 'details', 'account'])
            ->when(isset($filters['type']) && $filters['type'] !== 'all', function ($query) use ($filters) {
                $query->where('order_status', OrderStatus::fromValue($filters['type'])?->value);
            })
            ->when(isset($filters['start_date']) && isset($filters['end_date']), function ($query) use ($filters) {
                $startDate = date('Y-m-d', strtotime($filters['start_date'])) . ' 00:00:00';
                $endDate = date('Y-m-d', strtotime($filters['end_date'])) . ' 23:59:59';
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when(isset($filters['customer_id']) && $filters['customer_id'] !== 'all', function ($query) use ($filters) {
                $query->whereHas('customer', function ($q) use ($filters) {
                    $q->where('id', $filters['customer_id']);
                });
            })
            ->when(!empty($filters['payment_method_id']), function ($query) use ($filters) {
                $paymentMethodIds = is_array($filters['payment_method_id'])
                    ? $filters['payment_method_id']
                    : [$filters['payment_method_id']];

                $query->whereIn('payment_id', $paymentMethodIds);
            })
            ->when(isset($filters['counter_id']) && $filters['counter_id'] !== 'all', function ($query) use ($filters) {
                $query->whereHas('counter', function ($q) use ($filters) {
                    $q->where('id', $filters['counter_id']);
                });
            })
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('mobile', 'like', "%{$search}%");
                        })
                        ->orWhereHas('counter', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('number', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('created_at', 'desc');
    }

    public function export(Request $request)
    {
        $visibleColumns = explode(',', $request->columns);

        // Get filtered orders
        $orders = $this->getFilteredOrders([
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'customer_id' => $request->customer_id,
            'payment_method_id' => $request->input('payment_method_id', []),
            'counter_id' => $request->counter_id,
            'search' => $request->search,
        ])->get();

        $export = new FastExcel($orders->map(function ($order) use ($visibleColumns) {
            $data = [];

            if (in_array('order_id', $visibleColumns)) {
                $data['Order ID'] = $order->id;
            }

            if (in_array('order_date', $visibleColumns)) {
                $data['Order Date'] = date('d M Y', strtotime($order->created_at));
            }

            if (in_array('customer_info', $visibleColumns)) {
                $customerName = $order->customer?->name ?? 'N/A';
                $customerPhone = $order->customer?->mobile ?? 'N/A';
                $data['Customer Info'] = $customerName . ' (' . $customerPhone . ')';
            }

            if (in_array('counter_info', $visibleColumns)) {
                $counterNumber = $order->counter?->number ?? 'N/A';
                $counterName = $order->counter?->name ?? 'N/A';
                $data['Counter'] = $counterNumber . ' (' . $counterName . ')';
            }

            if (in_array('items', $visibleColumns)) {
                $data['Items'] = ($order->details->count() ?? 0) . ' ' . Str::plural('Item', $order->details->count() ?? 0);
            }

            if (in_array('order_amount', $visibleColumns)) {
                $data['Order Amount'] = \App\CPU\Helpers::currency_symbol() . ' ' . number_format($order?->details?->map(function($product) { return $product?->price * $product?->quantity; })->sum() ?? 0, 2);
            }

            if (in_array('discount', $visibleColumns)) {
                $discountInfo = [
                    'Discount:' . \App\CPU\Helpers::currency_symbol() . ' ' . number_format($order?->details?->map(function($product) { return $product?->discount_on_product * $product?->quantity; })->sum() ?? 0, 2),
                    'Extra Discount:' . \App\CPU\Helpers::currency_symbol() . ' ' . number_format($order->extra_discount ?? 0, 2),
                    'Coupon Discount:' . \App\CPU\Helpers::currency_symbol() . ' ' . number_format($order->coupon_discount_amount ?? 0, 2)
                ];
                $data['Discount Info'] = implode(', ', $discountInfo);
            }

            if (in_array('vat_tax', $visibleColumns)) {
                $data['VAT/Tax'] = \App\CPU\Helpers::currency_symbol() . ' ' . number_format($order->total_tax, 2);
            }

            if (in_array('total_amount', $visibleColumns)) {
                $data['Total Amount'] = \App\CPU\Helpers::currency_symbol() . ' ' . number_format($order->order_amount + $order->total_tax - ($order?->coupon_discount_amount ?? 0) - ($order?->extra_discount ?? 0) , 2);
            }

            if (in_array('paid_by', $visibleColumns)) {
                $data['Paid By'] = $order->account?->account ?? 'N/A';
            }

            if (in_array('order_status', $visibleColumns)) {
                $data['Order Status'] = $order->order_status->label();
            }

            return $data;
        }));

        return $export->download('orders_' . date('Y-m-d') . '.xlsx');
    }

    public function details($id)
    {
        $data['order'] = $this->order->with(['customer', 'account', 'details', 'counter', 'refund'])->findOrFail($id);
        $data['refundedOrder'] = $data['order']->refund;

        return view('admin-views.orders.details', $data);
    }

    public function refund(StoreRefundRequest $request, $id): JsonResponse
    {
        $order = $this->order->findOrFail($id);
        $adminPaymentMethodName = $this->account->find($request->admin_payment_method)->account ?? 'N/A';

        DB::beginTransaction();
        try {
            $data = $request->validated();
            $refundAmount = $data['refund_amount'];

            // Create refund record
            $refund = $this->refund;
            $refund->order_id = $id;
            $refund->refund_amount = $refundAmount;
            $refund->refund_reason = $data['refund_reason'];
            $refund->admin_payment_method_id = $data['admin_payment_method'];
            $refund->admin_payment_method_name = $adminPaymentMethodName;
            $refund->customer_payout_method_name = $data['customer_payout_method'];
            $refund->other_payment_details = [
                'payment_method' => $data['payment_method'] ?? null,
                'payment_info' => $data['payment_info'] ?? null,
            ];
            $refund->save();

            // Update order status
            $order->order_status = OrderStatus::REFUNDED;
            $order->save();

            $customer = $this->customer->find($order->user_id);
            $adminPaymentMethod = $this->account->find($refund->admin_payment_method_id);
            $this->createTransaction(
                tranType: 'Refund',
                account: $adminPaymentMethod,
                amount: $refundAmount,
                description: 'Customer refunded for order #' . $order->id,
                isDebit: true,
                date: date("Y/m/d"),
                customerId: $order->user_id,
                orderId: $id
            );

            if ($order->user_id != 0 && $refund->customer_payout_method_name === 'wallet')
            {
                $customer->balance += $refundAmount;
                $customer->save();
            }

            DB::commit();
            return response()->json(['message' => translate('Order refunded successfully')]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => translate('Failed to process refund')], 500);
        }
    }
}
