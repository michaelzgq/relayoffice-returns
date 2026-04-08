<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
{
    public function toArray($request): array
    {
        $data = $this->details?->reduce(function ($carry, $item) {
            if (!$item) {
                return $carry;
            }

            $subtotal = round($item->price * $item->quantity, 2);
            $discount = round($item->discount_on_product * $item->quantity, 2);

            return [
                'subtotal' => round($carry['subtotal'] + $subtotal, 2),
                'discount_on_product' => round($carry['discount_on_product'] + $discount, 2),
            ];
        }, [
            'subtotal' => 0.00,
            'discount_on_product' => 0.00,
        ]);

        $orderTotal = round($this?->order_amount + $this?->total_tax - $this?->extra_discount - $this?->coupon_discount_amount, 2);
        return [
            'order_id' => $this->id,
            'order_date' => $this->created_at,
            'counter_name' => $this?->counter ? $this?->counter?->name : null,
            'counter_no' => $this?->counter ? $this?->counter?->number : null,
            'reference_id' => $this?->transaction_reference ?? null,
            'payment_method' => $this?->payment_id == 0 ? 'wallet' :  ($this?->account ? $this?->account?->account : null),
            'order_note' => $this?->comment ?? null,
            'order_details' => OrderedItemListResource::collection($this->details),
            'order_tax' => $this?->total_tax ?? 0,
            'order_extra_discount' => $this?->extra_discount ?? 0,
            'order_coupon_discount_amount' => $this?->coupon_discount_amount ?? 0,
            'order_total' => ($orderTotal),
            'paid_amount' => $this?->collected_cash ?? 0,
            'change_amount' => ($this?->collected_cash ?? 0) - $orderTotal ,
            'refund_amount' => $this?->refund ? $this?->refund?->refund_amount : 0,
            'refund_reason' => $this?->refund ? $this?->refund?->refund_reason : null,
            'refund_admin_payment_method_id' => $this?->refund ? $this?->refund?->admin_payment_method_id : null,
            'refund_admin_payment_method_name' => $this?->refund ? $this?->refund?->admin_payment_method_name : null,
            'refund_customer_payout_method_name' => $this?->refund ? $this?->refund?->customer_payout_method_name : null,
            'refund_other_payment_details' => $this?->refund ? $this?->refund?->other_payment_details : null,
            'refund_date' => $this?->refund ? $this?->refund?->created_at : null,
            'subtotal' => $data['subtotal'],
            'discount' => $data['discount_on_product'],
            'customer' => $this?->customer,
            'order_status' => $this?->order_status,
            'card_number' => $this?->card_number ?? null,
            'email_or_phone' => $this?->email_or_phone ?? null,
        ];
    }
}
