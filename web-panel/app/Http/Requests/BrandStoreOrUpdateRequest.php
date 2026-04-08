<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;

class BrandStoreOrUpdateRequest extends ValidationHandler
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
            'name' => [
                'required',
                'max:255',
                Rule::unique('brands', 'name')->ignore($id),
            ],
            'description' => 'nullable|string',
            'image' => 'image|mimes:' . str_replace(['.', ' '], '', IMAGE_ACCEPTED_EXTENSIONS) . '|max:' . convertBytesToKiloBytes(maxUploadSize("image")),
        ];
    }

    public function messages(): array
    {
        return [
            'image.max' => 'The image size must not exceed ' . readableUploadMaxFileSize("image") . '.',
        ];
    }
}
