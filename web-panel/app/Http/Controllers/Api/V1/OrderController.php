<?php

namespace App\Http\Controllers\Api\V1;

use App\CPU\Helpers;
use App\Enums\Order\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Refund\StoreRefundRequest;
use App\Http\Resources\OrderDetailsResource;
use App\Http\Resources\OrderedItemListResource;
use App\Models\Account;
use App\Models\BusinessSetting;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Refund;
use App\Models\Transection;
use App\Traits\TransactionTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function App\CPU\translate;

class OrderController extends Controller
{
    use TransactionTrait;

    private BusinessSetting $business_setting;
    private Refund $refund;
    private Transection $transection;
    private OrderDetail $order_detail;
    private Customer $customer;
    private Product $product;
    private Account $account;
    private Order $order;

    public function __construct(
        Order           $order,
        Account         $account,
        Product         $product,
        Customer        $customer,
        OrderDetail     $order_detail,
        Transection     $transection,
        BusinessSetting $business_setting,
        Refund          $refund
    )
    {
        $this->order = $order;
        $this->account = $account;
        $this->product = $product;
        $this->customer = $customer;
        $this->order_detail = $order_detail;
        $this->transection = $transection;
        $this->refund = $refund;
        $this->business_setting = $business_setting;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $filters = [
            'type' => request()->input('type', 'all'),
            'start_date' => request()->input('start_date'),
            'end_date' => request()->input('end_date'),
            'customer_id' => request()->input('customer_id'),
            'payment_method_id' => json_decode(request()->input('payment_method_id', '')),
            'counter_id' => request()->input('counter_id'),
            'search' => request()->input('search'),
        ];
        $orders = $this->getFilteredOrders($filters)->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'total' => $orders->total(),
            'limit' => $limit,
            'offset' => $offset,
            'orders' => OrderDetailsResource::collection($orders),
            'type' => $filters['type'],
            'start_date' => $filters['start_date'],
            'end_date' => $filters['end_date'],
            'customer_id' => $filters['customer_id'],
            'payment_method_id' => $filters['payment_method_id'],
            'counter_id' => $filters['counter_id'],
            'search' => $filters['search'],
        ];
        return response()->json($data, 200);

    }

    private function getFilteredOrders(array $filters)
    {
        return $this->order->with(['customer' => function ($query) {
            $query->withCount('orders');
        }, 'counter', 'details', 'account'])
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

    public function itemList($id): JsonResponse
    {
        $order = $this->order->with('details')->find($id);
        $orderedItems = OrderedItemListResource::collection($order->details);
        $countItems = $order->details->count();
        $data = [
            'total_items' => $countItems,
            'ordered_items' => $orderedItems,
        ];

        return response()->json($data, 200);
    }

    public function refund(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(),
            [
                'refund_amount' => ['required', 'numeric', 'gt:0',
                    function ($attribute, $value, $fail) use ($id) {
                        $order = $this->order->find($id);
                        if ($order) {
                            $maxAmount = ($order->order_amount + $order->total_tax) - ($order->coupon_discount_amount ?? 0) - ($order->extra_discount ?? 0);
                            if ((float) $value > round($maxAmount, 2)) {
                                $fail('Refund amount cannot be greater than the total amount.');
                            }
                        }
                    },
                ],
                'refund_reason' => 'nullable|string|max:255',
                'admin_payment_method' => ['required', 'numeric', Rule::exists('accounts', 'id')->whereNotIn('account', ['Payable', 'Receivable'])],
                'customer_payout_method' => [
                    'required',
                    'in:cash,wallet,other',
                    function ($attribute, $value, $fail) use ($id) {
                        $order = $this->order->find($id);
                        if ($order && $value === 'wallet' && (int)$order->user_id === 0) {
                            $fail('Wallet payout is only allowed if the order has a registered customer.');
                        }
                    },
                ],
                'payment_method' => 'required_if:customer_payout_method,other|nullable|string|max:255',
                'payment_info' => 'nullable|string|max:255',
            ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $order = $this->order->findOrFail($id);

        if (!$order) {
            return response()->json(['errors' => [[
                'code' => 'order_not_found',
                'message' => translate('Order not found'),
            ]]], 403);
        }

        if ($this->refund->where('order_id', $id)->exists()) {
            return response()->json(['errors' =>[[
                'code' => 'refund_already_exists',
                'message' => translate('Refund already exists for this order'),
            ]]], 403);
        }
        $adminPaymentMethodName = $this->account->find($request->admin_payment_method)->account ?? 'N/A';
        DB::beginTransaction();
        $data = $validator->validated();
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
            'Refund',
            $adminPaymentMethod,
            $refundAmount,
            'Customer refunded for order #' . $order->id,
            true,
            date("Y/m/d"),
            0,
            $id
        );

        if ($order->user_id != 0 && $refund->customer_payout_method_name === 'wallet')
        {
            $customer->balance += $refundAmount;
            $customer->save();
        }
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => translate('Refund is added successfully '),
        ], 200);
    }

    public function details($id): JsonResponse
    {
        $order = $this->order->with(['customer' => function ($query) {
            $query->withCount('orders');
        }, 'counter', 'details', 'account', 'refund'])->find($id);
        return response()->json(OrderDetailsResource::make($order), 200);
    }
}
