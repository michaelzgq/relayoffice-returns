<?php

namespace App\Http\Requests;

class BulkImportRequest extends ValidationHandler
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
            'products_file' => [
                'required',
                'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,application/zip',
                'max:' . convertBytesToKiloBytes(maxUploadSize('file')),
            ],
        ];
    }



    public function messages(): array
    {
        return [
            'products_file.required' =>
                'Please upload an Excel file to import products.',

            'products_file.mimetypes' =>
                'The uploaded file must be a valid Excel (.xlsx or .xls) file.',

            'products_file.max' =>
                'The uploaded file exceeds the maximum allowed size of ' . readableUploadMaxFileSize('file') . '.',
        ];
    }


}
