<?php

namespace App\Http\Requests;

use App\CPU\Helpers;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Redirect;

abstract class ValidationHandler extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $api = str_contains($this->route()->getPrefix(), 'api');
        showValidationMessageForUploadMaxSize(files: $this->allFiles(), isAjax: $this->ajax(), doesExpectJson: $this->expectsJson(), responseCode: $api ? 403 : 200);
    }

    protected function failedValidation(Validator $validator): void
    {
        $api = str_contains($this->route()->getPrefix(), 'api');

        if (($this->ajax() || $this->expectsJson()) || $api) {
            throw new HttpResponseException(response()->json(['errors' => Helpers::error_processor($validator)], $api ? 403 : 200));
        }

        throw new HttpResponseException(
            Redirect::back()
                ->withErrors($validator)
                ->withInput()
        );
    }
}
