<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\ValidationHandler;
use Illuminate\Validation\Rule;

class CouponStoreOrUpdateRequest extends ValidationHandler
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
        $id = $this->route('id') ?? null;
        return [
            'title' => 'required',
            'code' => ['required', Rule::unique('coupons', 'code')->ignore($id)],
            'coupon_type'=>'required|in:default,first_order',
            'user_limit' => 'required_if:coupon_type,default|nullable|numeric|min:1',
            'start_date' => 'required',
            'expire_date' => 'required',
            'min_purchase' => 'required|numeric|min:1',
            'discount_type' => 'required|in:amount,percent',
            'discount' => ['required', function ($attribute, $value, $fail) {
                if($this->discount_type == 'percent' && $value > 100) {
                    $fail('The discount percentage must not exceed 100%.');
                }
            }],
            'max_discount' => ['required_if:discount_type,percent', function ($attribute, $value, $fail) {
                if ($this->discount_type == 'percent' && $value <= 0) {
                    $fail('The maximum discount must be greater than 0.');
                }
            }],
        ];
    }
}
