<?php

namespace App\Http\Requests\Admin;

use App\CPU\Helpers;
use App\Http\Requests\ValidationHandler;
use App\Models\Account;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use function App\CPU\translate;

class TransferStoreOrUpdateRequest extends ValidationHandler
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
            'account_from_id' => 'required|different:account_to_id|exists:accounts,id|numeric',
            'account_to_id' => 'required|different:account_from_id|exists:accounts,id|numeric',
            'description'=> 'required|string|max:255',
            'amount' => 'required|min:1',
            'date' => 'required|date|before_or_equal:today',
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        $account = Account::find($this->account_from_id);
        if ($account && $this->amount > $account->balance) {
            throw new HttpResponseException(response()->json(['errors' => [['code' => 'amount', 'message' => translate('Insufficient balance in the selected account.')]]]));
        }

        throw new HttpResponseException(response()->json(['errors' => Helpers::error_processor($validator)]));
    }
}
