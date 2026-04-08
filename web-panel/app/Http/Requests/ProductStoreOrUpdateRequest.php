<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use function App\CPU\translate;

class ProductStoreOrUpdateRequest extends ValidationHandler
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
        $id = $this->route('id') ?? $this->id ?? null;
        $api = str_contains($this->route()->getPrefix(), 'api');
        return [
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'product_code' => ['required', Rule::unique('products', 'product_code')->ignore($id), 'min:5'],
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where(function ($query) {
                if ($this->category_id) {
                    $query->where('parent_id', $this->category_id);
                }
            })],
            'brand_id' => 'nullable|exists:brands,id',
            'quantity' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:1',
            'unit_type' => 'required|exists:units,id',
            'unit_value' => 'required|numeric|min:1',
            'purchase_price' => 'required|numeric|min:1',
            'selling_price' => 'required|numeric|min:1',
            'image' => 'image|mimes:' . str_replace(['.', ' '], '', IMAGE_ACCEPTED_EXTENSIONS) . '|max:' . convertBytesToKiloBytes(maxUploadSize("image")),
            'discount_type' => 'required|in:percent,amount',
            'discount' => ['nullable', 'numeric', 'min:0', function ($attribute, $value, $fail) {
                if ($this->discount_type === 'percent') {
                    if ($value > 100) {
                        $fail(translate('The discount percentage must not exceed 100%.'));
                    }
                    $discount = ($this->selling_price / 100) * $this->discount;
                } else {
                    $discount = $this->discount;
                }

                if ($this->selling_price <= $discount) {
                    $fail(translate('The discount must be less than the selling price.'));
                }
            }],
            'tax' => 'nullable|numeric|min:0|max:100',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'available_time_started_at' => 'nullable|date_format:' . ($api ? 'H:i' : 'h : i A') . '|required_with:available_time_ended_at',
            'available_time_ended_at'   => 'nullable|date_format:' . ($api ? 'H:i' : 'h : i A') . '|required_with:available_time_started_at|after:available_time_started_at',
        ];
    }

    public function messages(): array
    {
        $api = str_contains($this->route()->getPrefix(), 'api');
        return [
            'name.required' => translate('Product name is required'),
            'name.max' => translate('Product name must not exceed 255 characters'),
            'image.max' => 'The image size must not exceed ' . readableUploadMaxFileSize("image") . '.',
            'category_id.required' => translate('Category is required'),
            'category_id.exists' => translate('Selected category does not exist'),
            'sub_category_id.exists' => translate('Selected sub-category does not exist'),
            'brand_id.exists' => translate('Selected brand does not exist'),
            'available_time_started_at.required_with' => translate('Available time starts is required when Available time ends is provided.'),
            'available_time_ended_at.required_with' => translate('Available time ends is required when Available time starts is provided.'),
            'available_time_ended_at.after' => translate('Available time ends must be after Available time starts.'),
            'available_time_started_at.date_format' => translate('Available time starts must be in the format ' . ($api ? 'H:i' : 'h : i A')),
            'available_time_ended_at.date_format' => translate('Available time ends must be in the format ' . ($api ? 'H:i' : 'h : i A')),
            'discount_type.in' => translate('Invalid discount type selected'),
        ];
    }
}
