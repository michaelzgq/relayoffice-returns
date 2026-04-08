<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use function App\CPU\translate;

class EmployeeStoreOrUpdateRequest extends ValidationHandler
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
            'f_name' => 'required|max:100',
            'l_name' => 'required|max:100',
            'role_id' => 'required|exists:admin_roles,id',
            'email' => [
                'required',
                'email',
                Rule::unique('admins', 'email')->ignore($id),
            ],
            'phone' => [
                'required',
                'regex:/^([0-9\s\-\+\(\)]*)$/',
                'min:9',
                'max:20',
                Rule::unique('admins', 'phone')->ignore($id),
            ],
            'password' => $id ? 'nullable|min:8' : 'required|min:8',
            'confirm_password' => [
                Rule::requiredIf(function () use($api) {
                    return $this->password != null && !$api;
                }),
                'same:password'],
            'image'=>'image|mimes:' . str_replace(['.', ' '], '', IMAGE_ACCEPTED_EXTENSIONS) . '|max:' . convertBytesToKiloBytes(maxUploadSize('image')),
        ];
    }

    public function messages(): array
    {
        return [
            'f_name.required' => 'First name is required',
            'l_name.required' => 'Last name is required',
            'role_id.required' => 'Role is required',
            'image.max' => 'Image size must be less than ' . readableUploadMaxFileSize('image') . '.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $api = str_contains($this->route()->getPrefix(), 'api');
        $id = $this->route('id') ?? $this->id ?? null;

        if ($this->role_id == 1) {
            throw new HttpResponseException(response()->json(['errors' => [['code' => 'role_id', 'message' => translate('access_denied')]]], $api ? 403 : 200));
        }

        if ($api && (auth('admin-api')->id() == $id || auth('admin-api')->user()?->role_id != 1)){
                throw new HttpResponseException(response()->json(['errors' => [['code' => 'role_id', 'message' => translate('you_cannot_change_your_own_role')]]],  403));
        }

        if (!$api && (auth('admin')->id() == $id || auth('admin')->user()?->role_id  != 1)) {
            throw new HttpResponseException(response()->json(['errors' => [['code' => 'role_id', 'message' => translate('you_cannot_change_your_own_role')]]]));
        }

        showValidationMessageForUploadMaxSize(files: $this->allFiles(), isAjax: $this->ajax(), doesExpectJson: $this->expectsJson(), responseCode: $api ? 403 : 200);
    }
}
