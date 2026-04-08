<?php

namespace App\Http\Requests\Admin\Refund;

use App\CPU\Helpers;
use App\Models\Order;
use App\Models\Refund;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;


class StoreRefundRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'refund_amount' => ['required', 'numeric', 'gt:0',
                function ($attribute, $value, $fail) {
                    $order = \App\Models\Order::find($this->route('id'));
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
                function ($attribute, $value, $fail) {
                    $order = Order::find($this->route('id'));
                    if ($order && $value === 'wallet' && (int)$order->user_id === 0) {
                        $fail('Wallet payout is only allowed if the order has a registered customer.');
                    }
                },
            ],
            'payment_method' => 'required_if:customer_payout_method,other|nullable|string|max:255',
            'payment_info' => 'nullable|string|max:255',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
           $orderId = $this->route('id');

           if (!Order::where('id', $orderId)->exists()) {
               $validator->errors()->add('order_id', 'Order not found');
           }

           if (Refund::where('order_id', $orderId)->exists()) {
               $validator->errors()->add('order_id', 'Refund already exists for this order');
           }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => Helpers::error_processor($validator)]));
    }
}
