<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class SupplierStoreOrUpdateRequest extends ValidationHandler
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
        return [
            'name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('suppliers', 'email')->ignore($id),
            ],
            'mobile' => [
                'required',
                'regex:/^([0-9\s\-\+\(\)]*)$/',
                'min:9',
                'max:20',
                Rule::unique('suppliers', 'mobile')->ignore($id),
            ],
            'state' => 'required',
            'city' => 'required',
            'zip_code' => 'required',
            'address' => 'required',
            'image' => 'image|mimes:' . str_replace(['.', ' '], '', IMAGE_ACCEPTED_EXTENSIONS) . '|max:' . convertBytesToKiloBytes(maxUploadSize('image'))
        ];
    }

    public function messages(): array
    {
        return [
            'image.max' => 'The image size must not exceed ' . readableUploadMaxFileSize('image') . '.',
        ];
    }

}
