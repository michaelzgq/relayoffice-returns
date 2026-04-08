<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class BusinessSettingsStoreOrUpdateRequest extends ValidationHandler
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
        $api = str_contains($this->route()->getPrefix(), 'api');
        return [
            'shop_name' => 'required|string|max:101',
            'shop_email' => 'required|email|max:100',
            'shop_phone' => 'required|string|max:15',
            'country' => 'required',
            'shop_address' => 'required|string|max:255',
            'shop_logo' => 'nullable|image|mimes:' . str_replace(['.', ' '] , '', IMAGE_ACCEPTED_EXTENSIONS) . '|max:' . convertBytesToKiloBytes(maxUploadSize("image")),
            'fav_icon' => 'nullable|image|mimes:' . str_replace(['.', ' '] , '', IMAGE_ACCEPTED_EXTENSIONS) . '|max:' . convertBytesToKiloBytes(maxUploadSize("image")),
            'time_zone' => 'required',
            'time_format' => Rule::requiredIf(function () use ($api) {return !$api;}),
            'pagination_limit' => 'required|integer|min:1',
            'currency' => 'required',
            'currency_symbol_position' => [Rule::requiredIf(function () use ($api) {return !$api;}), Rule::in(['left', 'right'])],
            'footer_text' => 'nullable|string|max:255',
            'vat_reg_no' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'shop_logo.max' => 'The shop logo size must not exceed ' . readableUploadMaxFileSize("image") . '.',
            'fav_icon.max' => 'The fav icon size must not exceed ' . readableUploadMaxFileSize("image") . '.',
        ];
    }
}
