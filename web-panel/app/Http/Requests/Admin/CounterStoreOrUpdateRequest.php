<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\ValidationHandler;
use Illuminate\Validation\Rule;

class CounterStoreOrUpdateRequest extends ValidationHandler
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
            'name' => [
                'required',
                'max:51',
                Rule::unique('counters', 'name')
                    ->ignore($id)
                    ->where('number', $this->number),
            ],

            'number' => [
                'required',
                Rule::unique('counters', 'number')
                    ->ignore($id)
                    ->where('name', $this->name),
            ],
            'description' => 'nullable|string|max:101',
        ];
    }
}
