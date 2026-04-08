<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Counter;
use App\Traits\CartTrait;
use App\Traits\TransactionTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Transection;
use App\Models\Account;
use App\Models\OrderDetail;
use App\Models\Customer;
use App\CPU\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use function App\CPU\translate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class POSController extends Controller
{
    use TransactionTrait, CartTrait;

    public function __construct(
        private Category    $category,
        private Product     $product,
        private Order       $order,
        private Coupon      $coupon,
        private Transection $transection,
        private Account     $account,
        private OrderDetail $orderDetails,
        private Customer    $customer,
        private Counter     $counter,
        private Brand       $brand,
    )
    {
    }

    public function index(Request $request): Factory|View|Application
    {
        $category = $request->query('category_id', 0);
        $keyword = $request->query('search', '');
        $key = explode(' ', $keyword);

        $categories = $this->category->where(['status' => 1, 'position' => 0])->latest()->get();
        $counters = $this->counter->where('status', 1)->latest()->get();
        $brands = $this->brand->where('status', 1)->latest()->get();

        $products = $this->product->where('quantity', '>', 0)->active()
            ->when($category, fn($q) => $q->whereJsonContains('category_ids', [['id' => (string)$category]]))
            ->when($request->category_ids, fn($q) => $q->where(function ($subQ) use ($request) {
                foreach ($request->category_ids as $id) {
                    $subQ->orWhereJsonContains('category_ids', [['id' => (string)$id]]);
                }
            }))
            ->when($request->subcategory_ids, fn($q) => $q->where(function ($subQ) use ($request) {
                foreach ($request->subcategory_ids as $id) {
                    $subQ->orWhereJsonContains('category_ids', [['id' => (string)$id]]);
                }
            }))
            ->when($request->brand_ids, fn($q) => $q->whereIn('brand', $request->brand_ids))
            ->when($request->filled(['min_price', 'max_price']), fn($q) => $q->whereBetween('selling_price', [$request->min_price, $request->max_price]))
            ->latest()
            ->paginate(Helpers::pagination_limit());

        $selectedCategories = $request->input('category_ids', []);
        $selectedSubcategories = $request->input('subcategory_ids', []);
        $subcategories = $this->category->where('position', 1)
            ->whereIn('parent_id', $selectedCategories)
            ->where('status', 1)
            ->get();

        $minPrice = $this->product->where('quantity', '>', 0)->active()->min('selling_price');
        $maxPrice = $this->product->where('quantity', '>', 0)->active()->max('selling_price');

        // ------------------- SESSION CART SETUP -------------------
        $cartId = session('current_user') ?? ('wc-' . rand(1000, 9999));
        session()->put('current_user', $cartId);

        // Add current cart ID to cart_name list if not already added
        $cartNames = session('cart_name', []);
        if (!in_array($cartId, $cartNames)) {
            session()->push('cart_name', $cartId);
        }

        // Set default counter if not set
        if (!session()->has('counter_id') && $counters->isNotEmpty()) {
            session()->put('counter_id', $counters[0]->id);
        } elseif ($counters->isEmpty()) {
            session()->forget('counter_id');
        }

        // ------------------- CURRENT CART -------------------
        $cart = session($cartId, [
            'items' => [],
            'ext_discount_amount' => 0,
            'ext_discount_type' => 'amount',
            'coupon_discount' => 0,
        ]);

        $cartItems = is_array($cart['items']) ? $cart['items'] : [];
        $cartItemsById = [];
        foreach ($cartItems as $item) {
            if (isset($item['id'])) {
                $cartItemsById[$item['id']] = $item;
            }
        }

        $userType = str_starts_with($cartId, 'sc') ? 'sc' : 'wc';
        $userId = explode('-', $cartId)[1] ?? 0;

        $cartView = $this->getCartHtml();

        // ------------------- HOLD CARTS -------------------
        $holdCarts = [];
        $sessionCartNames = session('cart_name', []);
        foreach ($sessionCartNames as $key => $holdId) {
            if ($holdId === $cartId) {
                continue; // skip current cart
            }

            $holdCart = session($holdId, []);
            $items = $holdCart['items'] ?? [];

            if (count($items) < 1) {
                unset($sessionCartNames[$key]); // optionally clean up
                continue;
            }

            $type = str_starts_with($holdId, 'sc') ? 'sc' : 'wc';
            $uid = $type === 'sc' ? (int)(explode('-', $holdId)[1] ?? 0) : 0;
            $customer = $uid ? \App\Models\Customer::find($uid) : null;

            $holdCarts[] = [
                'id' => $holdId,
                'items' => $items,
                'customer' => $customer,
                'user_type' => $type,
            ];
        }

        return view('admin-views.pos.index', compact(
            'categories', 'products', 'cartId', 'category', 'userId', 'counters',
            'minPrice', 'maxPrice', 'brands', 'selectedSubcategories',
            'subcategories', 'selectedCategories', 'cartView', 'cartItemsById',
            'holdCarts'
        ));
    }

    public function changeCounter(Request $request): JsonResponse
    {
        $request->validate([
            'counter_id' => 'required|integer|exists:counters,id',
        ]);

        try {
            session()->put('counter_id', $request->counter_id);

            return response()->json([
                'success' => true,
                'message' => translate('Counter updated successfully.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => translate('Failed to update counter.'),
            ], 500);
        }
    }


    #Customer Start
    public function selectedCustomer(): JsonResponse
    {
        // Get the current cart session ID or default to 'wc-0'
        $cartId = session('current_user', 'wc-0');

        // Safely split the cart ID into prefix and userId
        $parts = explode('-', $cartId);
        $prefix = $parts[0] ?? 'wc';
        $userId = $parts[1] ?? 0;

        // Default values for Walking Customer
        $currentCustomer = 'Walk-In Customer';
        $userType = 0;
        $phone = '';
        $balance = 0;

        // If it's a saved customer (sc), fetch customer details
        if ($prefix === 'sc') {
            $userType = 1;
            $customer = $this->customer->find($userId);

            if ($customer) {
                $currentCustomer = $customer->name ?? $currentCustomer;
                $phone = $customer->mobile ?? '';
                $balance = $customer->balance ?? 0;
            }
        }

        return response()->json([
            'cart_nam' => session('cart_name') ?? [],
            'current_user' => $cartId,
            'current_customer' => $currentCustomer,
            'current_customer_phone' => $phone,
            'current_customer_balance' => $balance,
            'user_type' => $userType,
            'customer_id' => $userId,
        ]);
    }

    public function getCustomers(Request $request): JsonResponse
    {
        $search = $request->input('q', '');
        $keywords = array_filter(explode(' ', $search));

        $data = DB::table('customers')
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $word) {
                    $query->orWhere('name', 'like', "%{$word}%")
                        ->orWhere('mobile', 'like', "%{$word}%");
                }
            })
            ->limit(6)
            ->get([
                'id',
                DB::raw('IF(id <> "0", CONCAT(name, " (", mobile, ")"), name) as text')
            ]);

        return response()->json($data);
    }

    public function customerBalance(Request $request): JsonResponse
    {
        $cartId = session('current_user', 'wc-0');
        $parts = explode('-', $cartId);
        $prefix = $parts[0] ?? 'wc';
        $userId = $request->customer_id ?? ($prefix === 'sc' ? ($parts[1] ?? 0) : 0);

        $balance = 0;

        if ($userId > 0) {
            $customer = $this->customer->find($userId);
            if ($customer) {
                $balance = $customer->balance ?? 0;
            }
        }

        return response()->json([
            'customer_balance' => $balance,
        ]);
    }
    #Customer End


    #Product, Category Start
    public function searchProduct(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => translate('Product name is required'),
        ]);

        $keywords = array_filter(explode(' ', $request->input('name')));

        $products = $this->product->where('quantity', '>', 0)
            ->active()
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('name', 'like', "%{$keyword}%");
                    $query->orWhere('product_code', 'like', "%{$keyword}%");
                }
            })
            ->paginate(6);

        return response()->json([
            'result' => view('admin-views.pos._search-result', compact('products'))->render(),
            'count' => $products->count()
        ]);
    }

    public function searchByAddProduct(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => translate('Product name is required'),
        ]);

        $query = $this->product->where('quantity', '>', 0)->active();

        if (is_numeric($request->name)) {
            $query->where('product_code', $request->name);
        } else {
            $query->where('name', $request->name);
        }

        $products = $query->paginate(6);
        if ($products->count() == 1) {
            $product = $products->first();
            $now = Carbon::now();
            $startTime = $product->available_time_started_at ? Carbon::parse($product->available_time_started_at) : null;
            $endTime = $product->available_time_ended_at ? Carbon::parse($product->available_time_ended_at) : null;
            $isAvailable = true;

            if ($startTime && $endTime) {
                $isAvailable = $now->between($startTime, $endTime);
            }
            if ($isAvailable) {
                return response()->json([
                    'count' => 1,
                    'id' => $product->id,
                ]);
            }
            return response()->json(['count' => 0], 200);
        }

        return response()->json(['count' => 0], 200);
    }

    public function getSubcategories(Request $request): JsonResponse
    {
        $subcategories = [];

        if (!empty($request->category_ids)) {
            $subcategories = $this->category->where('position', 1)
                ->where('status', 1)
                ->whereIn('parent_id', $request->category_ids)
                ->get();
        }

        return response()->json([
            'subcategories' => $subcategories
        ]);
    }

    public function quickView(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);

        try {
            $product = $this->product->findOrFail($request->product_id);

            $view = view('admin-views.pos._quick-view-data', compact('product'))->render();

            return response()->json([
                'success' => true,
                'view' => $view,
            ]);
        } catch (\Exception $e) {
            \Log::error('Quick View Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => translate('Unable to load product preview.'),
            ], 500);
        }
    }
    #Product, Category End


    #Start Cart Get,Add,Remove,Update,Empty
    public function getCartIds(Request $request): JsonResponse
    {
        $cartId = $this->getCurrentCartId();
        $cart = $this->initializeCart($cartId);
        extract($this->parseCartId($cartId));

        $currentCustomer = 'Walk-In Customer';
        $currentCustomerPhone = '';
        $currentCustomerBalance = 0;

        if ($user_type === 'sc' && ($customer = $this->customer->find($user_id))) {
            $currentCustomer = $customer->name;
            $currentCustomerPhone = $customer->mobile;
            $currentCustomerBalance = $customer->balance ?? 0;
        }

        return response()->json([
            'current_user' => $cartId,
            'cart_nam' => session('cart_name') ?? [],
            'current_customer' => $currentCustomer,
            'current_customer_phone' => $currentCustomerPhone,
            'current_customer_balance' => $currentCustomerBalance,
            'user_type' => $user_type,
            'user_id' => $user_id,
            'view' => $this->getCartHtml(),
        ]);
    }

    public function addToCart(Request $request): JsonResponse
    {
        $product = $this->product->findOrFail($request->id);
        $result = $this->productAddToCart($request, $product);

        if (!empty($result['message'])) {
            return response()->json(['error' => $result['message']], 400);
        }

        return response()->json([
            'qty' => $result['qty'],
            'cart_quantity' => $result['cart_quantity'],
            'user_type' => $result['user_type'],
            'user_id' => $result['user_id'],
            'view' => $this->getCartHtml(),
        ]);
    }

    public function addToCartData(Request $request): JsonResponse
    {
        $product = $this->product->findOrFail($request->id);

        if ($product->quantity < $request->quantity) {
            return response()->json([
                'qty' => 0,
                'product_stock' => $product->quantity,
                'user_type' => 'wc',
                'user_id' => 0,
                'view' => $this->getCartHtml()
            ]);
        }

        $data = $this->updateCart($request, $product);

        return response()->json([
            'qty' => $data['qty'],
            'user_type' => $data['user_type'],
            'user_id' => $data['user_id'],
            'view' => $this->getCartHtml()
        ]);
    }

    private function updateCart(Request $request, Product $product): array
    {
        $cartId = $this->getCurrentCartId();
        extract($this->parseCartId($cartId));

        $cart = session($cartId, $this->defaultCart());
        $cart['items'] = is_array($cart['items']) ? $cart['items'] : [];

        $found = false;
        foreach ($cart['items'] as &$item) {
            if ($item['id'] == $product->id) {
                $item['quantity'] = $request->quantity;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $cart['items'][] = [
                'id' => $product->id,
                'name' => $product->name,
                'quantity' => $request->quantity,
                'price' => $product->selling_price,
                'unit' => ($request->quantity * $product->unit_value) . ' ' . $product?->unit?->unit_type,
                'discount' => Helpers::discount_calculate($product, $product->selling_price),
                'tax' => Helpers::tax_calculate($product, $product->selling_price),
                'image' => $product->image,
            ];
        }

        session()->put($cartId, $cart);

        return [
            'cartId' => $cartId,
            'user_type' => $user_type,
            'user_id' => $user_id,
            'qty' => $request->quantity,
        ];
    }

    public function removeFromCart(Request $request): JsonResponse
    {
        $cartId = $this->getCurrentCartId();
        extract($this->parseCartId($cartId));

        $cart = $this->initializeCart($cartId);
        $cart['items'] = array_values(array_filter($cart['items'], fn($item) => is_array($item) && $item['id'] != $request->input('key')));

        session()->put($cartId, $cart);

        return response()->json([
            'user_type' => $user_type,
            'user_id' => $user_id,
            'view' => $this->getCartHtml(),
        ]);
    }

    public function updateQuantity(Request $request): JsonResponse
    {
        $cartId = $this->getCurrentCartId();
        extract($this->parseCartId($cartId));

        if ($request->quantity <= 0) {
            return response()->json([
                'upQty' => 'zeroNegative',
                'user_type' => $user_type,
                'user_id' => $user_id,
                'view' => $this->getCartHtml(),
            ]);
        }

        $product = $this->product->findOrFail($request->key);
        $cart = session($cartId, $this->defaultCart());
        $cart['items'] = is_array($cart['items']) ? $cart['items'] : [];

        foreach ($cart['items'] as &$item) {
            if ($item['id'] == $product->id) {
                $item['quantity'] = min($request->quantity, $product->quantity);
                $item['unit'] = (min($request->quantity, $product->quantity) * $product->unit_value) . ' ' . $product?->unit?->unit_type;
                break;
            }
        }

        session()->put($cartId, $cart);

        return response()->json([
            'user_type' => $user_type,
            'user_id' => $user_id,
            'cart_quantity' => min($request->quantity, $product->quantity),
            'view' => $this->getCartHtml(),
            'message' => $request->quantity > $product->quantity
                ? trans(key: 'This quantity is not available in stock. Maximum available quantity is :max_quantity', replace: ['max_quantity' => $product->quantity])
                : null,
        ]);
    }

    public function emptyCart(Request $request): JsonResponse
    {
        $cartId = $this->getCurrentCartId();
        extract($this->parseCartId($cartId));
        session()->put($cartId, $this->defaultCart());

        return response()->json([
            'user_type' => $user_type,
            'user_id' => $user_id,
            'view' => $this->getCartHtml(),
        ]);
    }

    public function changeCart(Request $request): RedirectResponse
    {
        if ($cartId = $request->input('cart_id')) {
            session()->put('current_user', $cartId);
        }
        return redirect()->route('admin.pos.index');
    }

    public function newCartId(Request $request): RedirectResponse
    {
        $cartId = 'wc-' . rand(1000, 9999);
        session()->put('current_user', $cartId);

        $existingCarts = session('cart_name', []);
        if (!in_array($cartId, $existingCarts)) {
            $existingCarts[] = $cartId;
            session()->put('cart_name', $existingCarts);
        }

        return redirect()->route('admin.pos.index');
    }

    public function clearCartIds(): RedirectResponse
    {
        foreach (session('cart_name', []) as $id) {
            session()->forget($id);
        }
        session()->forget(['cart_name', 'current_user']);

        $newCartId = 'wc-' . rand(1000, 9999);
        session()->put('current_user', $newCartId);
        session()->put('cart_name', [$newCartId]);

        return redirect()->route('admin.pos.index');
    }

    public function cartItems(): View|Factory|Application
    {
        return response($this->getCartHtml());
    }

    public function getCartHtml(): string
    {
        try {
            $cartId = $this->getCurrentCartId();
            $cart = session($cartId);
            $userId = 0;
            if (Str::contains($cartId, 'sc')) {
                $userId = (int)explode('-', $cartId)[1];
            }
            $items = $cart ? $cart['items'] : [];
            if (!empty($items)) {
                foreach ($items as $item) {
                    $product = Product::find($item['id']);
                    $now = Carbon::now();
                    $startTime = $product->available_time_started_at ? Carbon::parse($product->available_time_started_at) : null;
                    $endTime = $product->available_time_ended_at ? Carbon::parse($product->available_time_ended_at) : null;
                    $isAvailable = true;

                    if ($startTime && $endTime) {
                        $isAvailable = $now->between($startTime, $endTime);
                    }
                    if(!$isAvailable){
                        $cart['items'] = array_values(array_filter($items, fn($item) => is_array($item) && $item['id'] != $product->id));
                        session()->put($cartId, $cart);
                    }

                }
            }


            $subtotal = 0;
            $discountOnProduct = 0;
            $productTax = 0;

            foreach ($items as $item) {
                $subtotal += $item['price'] * $item['quantity'];
                $discountOnProduct += ($item['discount'] ?? 0) * $item['quantity'];
                $productTax += ($item['tax'] ?? 0) * $item['quantity'];
            }

            $couponCode = $cart['coupon_code'] ?? null;
            $coupon = $couponCode ? $this->getValidCoupon($couponCode, $userId) : null;

            if (!$coupon || $subtotal < ($coupon->min_purchase ?? 0)) {
                $cart['coupon_code'] = null;
                $cart['coupon_discount'] = 0;
            } else {
                $cart['coupon_discount'] = $this->calculateCouponDiscount($coupon, $subtotal, $discountOnProduct);
            }

            $totalBeforeExt = $subtotal - $discountOnProduct;
            $extDiscountAmount = $cart['ext_discount_amount'] ?? 0;
            $extDiscountType = $cart['ext_discount_type'] ?? 'amount';
            $couponDiscount = $cart['coupon_discount'] ?? 0;
            $discountAmount = ($extDiscountType === 'percent')
                ? (($totalBeforeExt - $couponDiscount) * ($extDiscountAmount / 100))
                : $extDiscountAmount;

            $totalAmount = round($totalBeforeExt - $couponDiscount - $discountAmount + $productTax, 2);

            return view('admin-views.pos._cart', compact(
                'cart', 'cartId', 'items', 'subtotal',
                'discountOnProduct', 'couponDiscount', 'couponCode',
                'extDiscountAmount', 'extDiscountType',
                'discountAmount', 'productTax', 'totalAmount'
            ))->render();
        } catch (\Throwable $e) {
            dd($e);
            return '<div class="alert alert-danger">Cart could not be loaded.</div>';
        }
    }

    #END Cart Get,Add,Remove,Update


    #Coupon Start
    public function getCoupon(): JsonResponse
    {
        $cartId = session('current_user') ?? 'wc-' . rand(1000, 9999);
        $userId = 0;

        if (Str::contains($cartId, 'sc')) {
            $userId = (int)explode('-', $cartId)[1];
        }

        $now = now();
        $baseQuery = $this->coupon
            ->where('status', 1)
            ->whereDate('start_date', '<=', $now)
            ->whereDate('expire_date', '>=', $now);

        if ($userId > 0) {
            // Get used coupon codes by this user with counts
            $usedCoupons = $this->order
                ->where('user_id', $userId)
                ->whereNotNull('coupon_code')
                ->selectRaw('coupon_code, COUNT(*) as used_count')
                ->groupBy('coupon_code')
                ->pluck('used_count', 'coupon_code');

            $coupons = $baseQuery->get()->filter(function ($coupon) use ($usedCoupons) {
                $used = $usedCoupons[$coupon->code] ?? 0;
                return $used < $coupon->user_limit;
            });
        } else {
            $coupons = $baseQuery
                ->where('coupon_type', 'default')
                ->get();
        }

        return response()->json([
            'total_coupon' => $coupons->count(),
            'view' => view('admin-views.pos._get-coupon', compact('coupons'))->render()
        ]);
    }

    public function couponDiscount(Request $request): JsonResponse
    {
        $cartId = session('current_user') ?? 'wc-' . rand(1000, 9999);
        $userId = 0;
        $userType = 'wc';

        if (Str::contains($cartId, 'sc')) {
            $userId = (int)explode('-', $cartId)[1];
            $userType = 'sc';
        }

        // Get coupon data
        $coupon = $this->getValidCoupon($request['coupon_code'], $userId);

        // Get and initialize cart
        $cart = $this->initializeCart($cartId);
        $items = $cart['items'];

        if (!$coupon) {
            return $this->couponResponse('coupon_invalid', $userType, $userId, $cartId);
        }

        if (empty($items)) {
            return $this->couponResponse('cart_empty', $userType, $userId, $cartId);
        }

        $subtotal = 0;
        $productDiscount = 0;
        $productTax = 0;

        foreach ($items as $item) {
            if (!is_array($item)) continue;
            $subtotal += $item['price'] * $item['quantity'];
            $productDiscount += ($item['discount'] ?? 0) * $item['quantity'];
            $productTax += ($item['tax'] ?? 0) * $item['quantity'];
        }

        if ($subtotal < $coupon['min_purchase']) {
            return response()->json([
                'coupon' => 'min_purchase',
                'user_type' => $userType,
                'user_id' => $userId,
                'min_purchase' => $coupon['min_purchase'],
                'view' => $this->getCartHtml()
            ]);
        }

        $discount = $this->calculateCouponDiscount($coupon, $subtotal, $productDiscount);

        $extDiscount = 0;
        if (isset($cart['ext_discount_type'])) {
            $extDiscount = $this->extraDisCalculate($cart, $subtotal);
        }

        $total = $subtotal - $productDiscount + $productTax - $discount - $extDiscount;

        if ($total < 0) {
            return $this->couponResponse('amount_low', $userType, $userId, $cartId);
        }

        // Save coupon data to session
        $cart['coupon_code'] = $request['coupon_code'];
        $cart['coupon_discount'] = $discount;
        $cart['coupon_title'] = $coupon->title;
        session()->put($cartId, $cart);

        return $this->couponResponse('success', $userType, $userId, $cartId);
    }

    protected function couponResponse(string $status, string $userType, int $userId, string $cartId): JsonResponse
    {
        return response()->json([
            'coupon' => $status,
            'user_type' => $userType,
            'user_id' => $userId,
            'view' => $this->getCartHtml()
        ]);
    }

    public function removeCoupon(Request $request): JsonResponse
    {
        // Generate cart ID based on user type
        $cartId = ($request->user_id != 0) ? 'sc-' . $request->user_id : 'wc-' . rand(10, 1000);

        // Push new cartId to session cart_name list if not present
        if (!in_array($cartId, session('cart_name') ?? [])) {
            session()->push('cart_name', $cartId);
        }

        // Get current cart and preserve items only
        $oldCart = session(session('current_user'), []);
        $cartItems = $oldCart['items'] ?? [];

        // Remove old current_user cart name from session list
        $tempCartNames = array_filter(session('cart_name', []), function ($name) use ($cartId) {
            return $name !== session('current_user');
        });

        session()->put('cart_name', $tempCartNames);
        session()->forget(session('current_user'));

        // Prepare new cart structure
        $newCart = [
            'items' => $cartItems,
            'ext_discount_amount' => 0,
            'ext_discount_type' => 'amount',
            'coupon_discount' => 0,
        ];

        // Put new cart in session
        session()->put($cartId, $newCart);
        session()->put('current_user', $cartId);

        // Load customer info
        [$prefix, $userId] = explode('-', $cartId) + [null, 0];
        $currentCustomer = 'Walk-In Customer';
        $phone = '';
        $balance = 0;

        if ($prefix === 'sc') {
            $customer = $this->customer->find($userId);
            if ($customer) {
                $currentCustomer = $customer->name;
                $phone = $customer->mobile ?? '';
                $balance = $customer->balance ?? 0;
            }
        }

        return response()->json([
            'cart_nam' => session('cart_name'),
            'current_user' => $cartId,
            'current_customer' => $currentCustomer,
            'current_customer_phone' => $phone,
            'current_customer_balance' => $balance,
            'view' => $this->getCartHtml(),
        ]);
    }
    #Coupon End

    #update discount, Tax
    public function updateDiscount(Request $request): JsonResponse
    {
        $cartId = session('current_user') ?? 'wc-' . rand(1000, 9999);
        $parts = explode('-', $cartId);
        $userType = $parts[0] ?? 'wc';
        $userId = $parts[1] ?? 0;

        $cart = $this->initializeCart($cartId);
        if (empty($cart['items'])) {
            return $this->renderDiscountResponse('empty', $userType, $userId, $cartId);
        }

        $subtotal = 0;
        $discountOnProduct = 0;
        $productTax = 0;
        $couponDiscount = $cart['coupon_discount'] ?? 0;

        foreach ($cart['items'] as $item) {
            if (!is_array($item)) continue;

            $subtotal += $item['price'] * $item['quantity'];
            $discountOnProduct += ($item['discount'] ?? 0) * $item['quantity'];
            $productTax += ($item['tax'] ?? 0) * $item['quantity'];
        }

        $totalBeforeExtDiscount = $subtotal - $discountOnProduct - $couponDiscount;
        $discountAmount = ($request->type === 'percent')
            ? ($totalBeforeExtDiscount * ($request->discount / 100))
            : $request->discount;

        $totalAfterDiscount = $totalBeforeExtDiscount + $productTax - $discountAmount;
        if ($totalAfterDiscount < 0) {
            return $this->renderDiscountResponse('amount_low', $userType, $userId, $cartId);
        }

        // Update cart with external discount info
        $cart['ext_discount_amount'] = $request->discount;
        $cart['ext_discount_type'] = $request->type;

        // Save updated cart back to session
        session()->put($cartId, $cart);
        return $this->renderDiscountResponse('success', $userType, $userId, $cartId);
    }

    protected function renderDiscountResponse(string $status, string $userType, int|string $userId, string $cartId): JsonResponse
    {
        // Use your getCartHtml() for consistent cart HTML rendering
        $cartHtml = $this->getCartHtml();

        return response()->json([
            'extra_discount' => $status,
            'user_type' => $userType,
            'user_id' => $userId,
            'view' => $cartHtml,
        ]);
    }

    public function updateTax(Request $request): RedirectResponse
    {
        $cartId = session('current_user') ?? 'wc-' . rand(1000, 9999);
        $cart = session($cartId, []);

        // Update tax value (assuming tax is a global value for the whole cart)
        $cart['tax'] = $request->tax;

        session()->put($cartId, $cart);

        return back();
    }

    public function extraDisCalculate(array $cart, float|int $price): float|int
    {
        $extDiscountAmount = $cart['ext_discount_amount'] ?? 0;
        $extDiscountType = $cart['ext_discount_type'] ?? 'amount';

        if ($extDiscountType === 'percent') {
            return ($price * $extDiscountAmount) / 100;
        }

        return $extDiscountAmount;
    }

    #Update Discount, Tax


    #Hold Order Start
    public function cancelHoldOrder(Request $request)
    {
        $cartId = $request->input('cart_id');
        $currentCartId = session('current_user');
        $sessionCartNames = session('cart_name', []);

        // Remove cartId from session cart list
        $updatedCartList = array_filter($sessionCartNames, fn($id) => $id !== $cartId);
        session()->put('cart_name', array_values($updatedCartList)); // reindex

        // Remove the session cart data
        session()->forget($cartId);

        // If the deleted cart was the current cart, reset it
        if ($currentCartId === $cartId) {
            session()->forget('current_user');

            // Set new current cart
            $newCartId = 'wc-' . rand(1000, 9999);
            session()->put('current_user', $newCartId);

            $cartNames = session('cart_name', []);
            if (!in_array($newCartId, $cartNames)) {
                session()->push('cart_name', $newCartId);
            }
        }

        // Clean up cart_name if now empty
        if (empty(session('cart_name', []))) {
            session()->forget('cart_name');
        }

        return redirect()->route('admin.pos.index');
    }
    #Hold Order End

    #Order Start
    public function placeOrder(Request $request): RedirectResponse
    {
        $cartId = session('current_user');
        $userId = 0;
        $userType = 'wc';

        if (Str::contains($cartId, 'sc')) {
            $userId = explode('-', $cartId)[1] ?? 0;
            $userType = 'sc';
        }

        $cart = session($cartId);
        if (empty($cart['items'] ?? [])) {
            Toastr::error(translate('cart_empty_warning'));
            return back();
        }

        $couponDiscount = $cart['coupon_discount'] ?? 0;
        $extDiscount = 0;

        $orderId = $this->generateUniqueOrderId();

        $order = $this->order;

        // Assign attributes individually, no fill()
        $order->id = $orderId;
        $order->user_id = $userId;
        $order->coupon_code = $cart['coupon_code'] ?? null;
        $order->coupon_discount_title = $cart['coupon_title'] ?? null;
        $order->payment_id = $request->type;
        $order->transaction_reference = $request->transaction_reference;
        $order->comment = $request->comment;
        $order->card_number = $request->card_number;
        $order->email_or_phone = $request->email_or_phone;
        $order->counter_id = session('counter_id');
        $order->created_at = now();
        $order->updated_at = now();

        $productPrice = 0;
        $productDiscount = 0;
        $productTax = 0;
        $orderDetails = [];

        foreach ($cart['items'] as $c) {
            $product = $this->product->find($c['id']);
            if (!$product) continue;

            $quantity = $c['quantity'];
            $price = $c['price'];
            $discount = $c['discount'] ?? 0;
            $tax = $c['tax'] ?? 0;

            $orderDetails[] = [
                'product_id' => $c['id'],
                'product_details' => $product,
                'quantity' => $quantity,
                'price' => $product->selling_price,
                'tax_amount' => Helpers::tax_calculate($product, $product->selling_price),
                'discount_on_product' => Helpers::discount_calculate($product, $product->selling_price),
                'discount_type' => 'discount_on_product',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $productPrice += $price * $quantity;
            $productDiscount += $discount * $quantity;
            $productTax += $tax * $quantity;

            $product->decrement('quantity', $quantity);
            $product->increment('order_count');
        }

        $totalPrice = $productPrice - $productDiscount;

        if (!empty($cart['ext_discount_type'])) {
            $extDiscount = $this->extraDisCalculate($cart, ($totalPrice - $couponDiscount));
            $order->extra_discount = $extDiscount;
        }

        $grandTotal = $totalPrice + $productTax - $extDiscount - $couponDiscount;

        try {
            $order->total_tax = $productTax;
            $order->order_amount = $totalPrice;
            $order->coupon_discount_amount = $couponDiscount;
            $order->collected_cash = $request->collected_cash ?? $grandTotal;
            $order->save();

            if ($userType === 'sc') {
                $customer = $this->customer->find($userId);
                if ($request->type == 0) {
                    $this->handleWalletPayment($grandTotal, $request->remaining_balance, $customer->id, $orderId);
                } else {
                    $this->handleNonWalletPayment($grandTotal, $customer->id, $orderId, $request->type);
                }
            }

            foreach ($orderDetails as &$detail) {
                $detail['order_id'] = $order->id;
            }

            $this->orderDetails->insert($orderDetails);

            session()->forget($cartId);
            session(['last_order' => $order->id]);

            Toastr::success(translate('order_placed_successfully'));
            return back();
        } catch (\Throwable $e) {
            Toastr::warning(translate('failed_to_place_order'));
            return back();
        }
    }

    private function generateUniqueOrderId(): int
    {
        $latest = $this->order->orderBy('id', 'desc')->first();
        return $latest ? $latest->id + 1 : 100001;
    }

    public function orderList(Request $request): Factory|View|Application
    {
        $search = $request->input('search');

        $ordersQuery = $this->order->latest();

        if ($search) {
            $ordersQuery->where('id', 'like', "%{$search}%");
        }

        $orders = $ordersQuery->paginate(Helpers::pagination_limit())->appends(['search' => $search]);

        return view('admin-views.pos.order.list', compact('orders', 'search'));
    }

    public function generateInvoice($id): JsonResponse
    {
        $order = $this->order
            ->with(['details', 'counter', 'customer', 'account', 'refund'])
            ->findOrFail($id);

        $view = view('admin-views.pos.invoice', compact('order'))->render();

        return response()->json([
            'success' => 1,
            'view' => $view,
        ]);
    }

    #Order End


}
