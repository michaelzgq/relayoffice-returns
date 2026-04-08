<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\ValidationHandler;

class SettingUpdateRequest extends ValidationHandler
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
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required|email|unique:admins,email,'. auth('admin')->id(),
            'phone' => 'required|min:9|max:20|unique:admins,phone,' .auth('admin')->id(),
            'image' => 'image|mimes:' . str_replace(['.', ' '], '', IMAGE_ACCEPTED_EXTENSIONS) . '|max:' . convertBytesToKiloBytes(maxUploadSize('image'))
        ];
    }

    public function messages(): array
    {
        return [
            'f_name.required' => 'First name is required',
            'l_name.required' => 'Last name is required',
            'image.max' => 'The image size must not exceed ' . readableUploadMaxFileSize('image') . '.',
        ];
    }
}
