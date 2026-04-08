<?php

namespace App\Http\Controllers\Api\V1;

use App\CPU\Helpers;
use App\Http\Resources\OrderDetailsResource;
use App\Http\Resources\ProductsResource;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Account;
use App\Models\Product;
use App\Models\Customer;
use App\Models\OrderDetail;
use App\Models\Transection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Traits\TransactionTrait;

class PosController extends Controller
{
    use TransactionTrait;

    public function __construct(
        private Order $order,
        private Account $account,
        private Product $product,
        private Customer $customer,
        private OrderDetail $order_detail,
        private Transection $transection,
        private BusinessSetting $business_setting,
        private Coupon $coupon,
        private Category $category,
    ){}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function orderList(Request $request): JsonResponse
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;

        $orders = $this->order->with('account')->latest()->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'total' => $orders->total(),
            'limit' => $limit,
            'offset' => $offset,
            'orders' => $orders->items(),
        ];
        return response()->json($data, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function invoiceGenerate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $invoice = $this->order->with(['details', 'account', 'counter', 'refund', 'customer' => function ($query) {
            $query->withCount('orders');
        }])->where(['id' => $request['order_id']])->first();
        return response()->json([
            'success' => true,
            'invoice' => OrderDetailsResource::make($invoice),
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function placeOrder(Request $request): JsonResponse
    {
        if ($request['cart']) {
            if (count($request['cart']) < 1) {
                return response()->json(['message' => 'Cart empty'], 403);
            }
        }
        $user_id = $request->user_id;

        $coupon_code = 0;
        $product_price = 0;
        $order_details = [];
        $product_discount = 0;
        $product_tax = 0;
        $ext_discount = 0;
        $coupon_discount = $request->coupon_discount ?? 0;

        $order_id = 100000 + $this->order->all()->count() + 1;
        if ($this->order->find($order_id)) {
            $order_id = $this->order->orderBy('id', 'DESC')->first()->id + 1;
        }

        $order = $this->order;
        $order->id = $order_id;

        $order->user_id = $user_id;
        $order->coupon_code = $request['coupon_code'] ?? null;
        $order->coupon_discount_title = $request['coupon_title'] ?? null;
        $order->payment_id = $request->type;
        $order->transaction_reference = $request->transaction_reference ?? null;
        $order->comment = $request->comment ?? null;
        $order->card_number = $request->card_number ?? null;
        $order->counter_id = $request['counter_id'] ?? null;
        $order->email_or_phone = $request->email_or_phone ?? null;
        $order->created_at = now();
        $order->updated_at = now();

        foreach ($request['cart'] as $c) {
            if (is_array($c)) {
                $product = $this->product->find($c['id']);
                if ($product) {
                    $price = $c['price'];
                    $or_d = [
                        'product_id' => $c['id'],
                        'product_details' => $product,
                        'quantity' => $c['quantity'],
                        'price' => $product->selling_price,
                        //'tax' => Helpers::tax_calculate($product, $product->selling_price),
                        'tax_amount' => Helpers::tax_calculate($product, $product->selling_price),
                        'discount_on_product' => Helpers::discount_calculate($product, $product->selling_price),
                        'discount_type' => 'discount_on_product',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    $product_price += $price * $c['quantity'];
                    $product_discount += $c['discount'] * $c['quantity'];
                    $product_tax += $c['tax'] * $c['quantity'];
                    $order_details[] = $or_d;
                    if ($c['quantity'] > $product->quantity) {
                        return response()->json([
                            'message' => 'Please check product quantity'
                        ], 422);
                    }
                    $product->quantity = $product->quantity - $c['quantity'];
                    $product->order_count++;
                    $product->save();
                }
            }

        }
        $total_price = $product_price - $product_discount;


        if ($request->ext_discount_type == 'percent') {
            $ext_discount = (($total_price - $coupon_discount) / 100) * $request->extra_discount;
            $order->extra_discount = $ext_discount;
        } else {
            $ext_discount = $request->extra_discount;
            $order->extra_discount = $request->extra_discount;
        }

        $total_tax_amount = $product_tax;
        try {
            $order->total_tax = $total_tax_amount;
            $order->order_amount = $total_price;

            $order->coupon_discount_amount = $coupon_discount;
            $order->collected_cash = $request->collected_cash ? $request->collected_cash : $total_price + $total_tax_amount - $ext_discount - $coupon_discount;
            $order->save();

            $customer = $this->customer->where('id', $user_id)->first();
            $grand_total = $total_price + $total_tax_amount - $ext_discount - $coupon_discount;

            // Handle transactions based on payment type
            if ($user_id != 0 && $request->type == 0) {
                // Wallet payment
                $this->handleWalletPayment(
                    $grand_total,
                    $request->remaining_balance,
                    $customer->id,
                    $order_id
                );
            } else if ($request->type != 0) {
                // Non-wallet payment (cash, card, etc)
                $this->handleNonWalletPayment(
                    $grand_total,
                    $customer->id,
                    $order_id,
                    $request->type
                );
            }

            // Save order details
            foreach ($order_details as $key => $item) {
                $order_details[$key]['order_id'] = $order->id;
            }
            $this->order_detail->insert($order_details);

            return response()->json([
                'message' => 'Order placed successfully',
                'order_id' => $order_id
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to placed order'
            ], 400);
        }
    }

    /**
     * @param $c
     * @param $price
     * @return float|int
     */
    public function extra_dis_calculate($c, $price): float|int
    {
        if ($c['ext_discount_type'] == 'percent') {
            $price_discount = ($price / 100) * $c['ext_discount'];
        } else {
            $price_discount = $c['ext_discount'];
        }
        return $price_discount;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function customerOrders(Request $request): JsonResponse
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;

        $orders = $this->order->with('account')->where('user_id', $request->customer_id)->latest()->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'total' => $orders->total(),
            'limit' => $limit,
            'offset' => $offset,
            'orders' => $orders->items(),
        ];
        return response()->json($data, 200);
    }

    public function getCoupon(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if ($request->user_id != 0) {
            $coupons = $this->coupon
                ->where('status', 1)
                ->whereDate('start_date', '<=', now())
                ->whereDate('expire_date', '>=', now())
                ->get()
                ->filter(function ($coupon) {
                    $orderCount = $this->order
                        ->where('coupon_code', $coupon->code)
                        ->count();
                    return $orderCount < $coupon->user_limit;
                });
        } else {
            $coupons = $this->coupon
                ->where('status', '=', 1)
                ->where('coupon_type', '=', 'default')
                ->whereDate('start_date', '<=', now())
                ->whereDate('expire_date', '>=', now())
                ->get();
        }

        return response()->json($coupons, 200);
    }

    public function getCategories(Request $request): JsonResponse
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $categories = $this->category->mainCategory()->where('status', 1)->latest()->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'total' => $categories->total(),
            'limit' => $limit,
            'offset' => $offset,
            'categories' => $categories->items()
        ];

        return response()->json($data, 200);
    }

    public function categoryWiseProduct(Request $request): JsonResponse
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $categoryWiseProduct = $this->product
            ->active()
            ->with(['unit', 'productBelongsToBrand', 'supplier'])
            ->when($request->has('category_id') && $request['category_id'] != 0, function ($query) use ($request) {
                $query->whereJsonContains('category_ids', [['id' => (string) $request['category_id']]]);
            })->latest()
            ->paginate($limit, ['*'], 'page', $offset);
        $categoryWiseProduct = ProductsResource::collection($categoryWiseProduct);
        return response()->json($categoryWiseProduct);
    }

}
