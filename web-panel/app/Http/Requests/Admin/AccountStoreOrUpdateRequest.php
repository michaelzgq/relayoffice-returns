<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\ValidationHandler;

class AccountStoreOrUpdateRequest extends ValidationHandler
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
            'account' => 'required|unique:accounts,account,' . $id,
            'description' => 'nullable|string',
            'balance'=> [$id ? 'nullable' : 'required', 'numeric'],
            'account_number' => 'required|unique:accounts,account_number,' . $id,
        ];
    }
}
