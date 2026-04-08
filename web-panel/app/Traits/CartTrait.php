<?php

namespace App\Traits;

use App\CPU\Helpers;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait CartTrait
{
    /**
     * Add or update a product in the session cart.
     *
     * @param Request $request
     * @param Product $product
     * @param string|null $cartId
     * @return array
     */
    public function productAddToCart(Request $request, Product $product, ?string $cartId = null): array
    {
        try {
            // Ensure $cartId exists, fallback to 'wc-0' if missing
            $cartId = $cartId ?? session('current_user') ?? 'wc-0';

            // Extract user type and id from cart ID
            [$userType, $userId] = $this->extractUserTypeAndId($cartId);

            // Load cart from session with default structure
            $cart = $this->initializeCart($cartId);

            // Try to find product in cart
            $productKey = $this->findProductInCart($cart['items'], $product->id);

            if ($productKey !== null) {
                // Product already in cart, increase quantity by 1
                $newQuantity = $cart['items'][$productKey]['quantity'] + 1;

                // Check stock limit
                if ($newQuantity > $product->quantity) {
                    return $this->buildResponse(0, $product->quantity, $userType, $userId, $cart, 'No more stock available');
                }

                // Update quantity
                $cart['items'][$productKey]['quantity'] = $newQuantity;
            } else {
                // Product not in cart, add new entry with requested qty or 1 by default
                $requestedQty = max(1, intval($request->quantity ?? 1));
                $qtyToAdd = min($requestedQty, $product->quantity);

                $cart['items'][] = [
                    'id' => $product->id,
                    'quantity' => $qtyToAdd,
                    'price' => $product->selling_price,
                    'unit' => ($qtyToAdd *$product->unit_value) . ' ' . $product?->unit?->unit_type,
                    'name' => $product->name,
                    'discount' => Helpers::discount_calculate($product, $product->selling_price),
                    'image' => $product->image,
                    'tax' => Helpers::tax_calculate($product, $product->selling_price),
                ];

                $productKey = count($cart['items']) - 1;
            }

            // Save updated cart back to session
            session()->put($cartId, $cart);

            // Return successful response
            return $this->buildResponse(
                $cart['items'][$productKey]['quantity'],
                $product->quantity,
                $userType,
                $userId,
                $cart
            );
        } catch (\Throwable $e) {
            return [
                'qty' => 0,
                'cart_quantity' => 0,
                'user_type' => 'wc',
                'user_id' => 0,
                'cart' => [],
                'message' => 'Internal error occurred while adding to cart',
            ];
        }
    }

    /**
     * Extract user type and user id from cart ID string.
     *
     * @param string $cartId
     * @return array [userType, userId]
     */
    protected function extractUserTypeAndId(string $cartId): array
    {
        if (Str::startsWith($cartId, 'sc-')) {
            $parts = explode('-', $cartId);
            return ['sc', intval($parts[1] ?? 0)];
        }

        // Default to walking customer
        return ['wc', 0];
    }

    /**
     * Find product in cart items by product ID.
     *
     * @param array $items
     * @param int $productId
     * @return int|null Returns key if found, null if not found
     */
    protected function findProductInCart(array $items, int $productId): ?int
    {
        foreach ($items as $key => $item) {
            if (isset($item['id']) && $item['id'] === $productId) {
                return $key;
            }
        }
        return null;
    }

    /**
     * Build standard response array for addToCart method.
     *
     * @param int $qty
     * @param int $cartQuantity
     * @param string $userType
     * @param int $userId
     * @param array $cart
     * @param string $message
     * @return array
     */
    protected function buildResponse(int $qty, int $cartQuantity, string $userType, int $userId, array $cart, string $message = ''): array
    {

        return [
            'qty' => $qty,
            'cart_quantity' => $cartQuantity,
            'user_type' => $userType,
            'user_id' => $userId,
            'cart' => $cart,
            'message' => $message,
        ];
    }

    protected function getValidCoupon(string $code, int $userId)
    {
        if ($userId > 0) {
            $used = $this->order
                ->where('user_id', $userId)
                ->where('coupon_code', $code)
                ->count();

            return $this->coupon
                ->where('code', $code)
                ->where('user_limit', '>', $used)
                ->where('status', 1)
                ->whereDate('start_date', '<=', now())
                ->whereDate('expire_date', '>=', now())
                ->first();
        }

        return $this->coupon
            ->where('code', $code)
            ->where('coupon_type', 'default')
            ->where('status', 1)
            ->whereDate('start_date', '<=', now())
            ->whereDate('expire_date', '>=', now())
            ->first();
    }

    protected function calculateCouponDiscount($coupon, float $subtotal, float $productDiscount): float
    {
        if ($coupon['discount_type'] === 'percent') {
            $base = $subtotal - $productDiscount;
            $calculated = $base * ($coupon['discount'] / 100);
            return min($calculated, $coupon['max_discount']);
        }

        return $coupon['discount'];
    }


    protected function defaultCart(): array
    {
        return [
            'items' => [],
            'ext_discount_amount' => 0,
            'ext_discount_type' => 'amount',
            'coupon_discount' => 0,
            'coupon_code' => null,
            'coupon_title' => null,
        ];
    }

    protected function initializeCart(string $cartId): array
    {
        $cart = session($cartId, []);
        $cart = array_merge($this->defaultCart(), is_array($cart) ? $cart : []);
        $cart['items'] = array_filter($cart['items'], 'is_array');
        session()->put($cartId, $cart);
        return $cart;
    }

    protected function getCurrentCartId(): string
    {
        $cartId = session('current_user') ?? 'wc-' . now()->timestamp . '-' . Str::random(6);
        session()->put('current_user', $cartId);
        return $cartId;
    }

    protected function parseCartId(string $cartId): array
    {
        [$prefix, $userId] = explode('-', $cartId) + [null, 0];
        return [
            'user_type' => $prefix === 'sc' ? 'sc' : 'wc',
            'user_id' => (int)$userId,
        ];
    }

}
