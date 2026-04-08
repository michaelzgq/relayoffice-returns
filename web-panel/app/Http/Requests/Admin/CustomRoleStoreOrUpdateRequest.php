<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\ValidationHandler;
use function App\CPU\translate;

class CustomRoleStoreOrUpdateRequest extends ValidationHandler
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
            'name' => 'required|max:191|unique:admin_roles,name,' . $id,
            'modules'=>'required|array|min:1'
        ];
    }

    public function messages()
    {
        return [
          'modules.required' => translate('Please select at least 1 module')
        ];
    }
}
