<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class CategoryStoreOrUpdateRequest extends ValidationHandler
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
        $subcategory = str_contains($this->route()->getPrefix(), 'sub');

        return [
            'name' => ['required', 'string', Rule::unique('categories', 'name')->where('parent_id', $this->parent_id ?? 0)->ignore($id), 'max:255'],
            'image'=>'image|mimes:' . str_replace(['.', ' '], '', IMAGE_ACCEPTED_EXTENSIONS) . '|max:' . convertBytesToKiloBytes(maxUploadSize("image")),
            'description'=>'nullable|string',
            'parent_id' => [($this->type && $this->type == 'sub-category') || $subcategory ? 'required' : 'nullable', 'integer', 'exists:categories,id'],
            'type' => [Rule::requiredIf(function () use ($api) {return !$api;}), Rule::in(['category', 'sub-category'])],
        ];
    }

    public function messages(): array
    {
        return [
            'image.max' => 'The image size must not exceed ' . readableUploadMaxFileSize("image") . '.',
        ];
    }
}
