<?php

namespace App\Http\Requests\Admin\Returns;

use App\Http\Requests\ValidationHandler;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreBrandRuleProfileRequest extends ValidationHandler
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'brand_id' => ['required', 'exists:brands,id', Rule::unique('brand_rule_profiles', 'brand_id')->ignore($id)],
            'profile_name' => ['required', 'string', 'max:255'],
            'allowed_conditions' => ['required', 'array', 'min:1'],
            'allowed_conditions.*' => ['required', 'string'],
            'allowed_dispositions' => ['required', 'array', 'min:1'],
            'allowed_dispositions.*' => ['required', 'string'],
            'recommended_dispositions' => ['nullable', 'array'],
            'required_photo_types' => ['nullable', 'array'],
            'required_photo_types.*' => ['nullable', 'string'],
            'required_photo_count' => ['required', 'integer', 'min:0', 'max:10'],
            'default_refund_status' => ['required', Rule::in(['hold', 'ready_to_release', 'needs_review', 'released'])],
            'notes_required' => ['nullable', 'boolean'],
            'sku_required' => ['nullable', 'boolean'],
            'serial_required' => ['nullable', 'boolean'],
            'active' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $photoTypes = collect($this->input('required_photo_types', []))
                ->filter()
                ->unique()
                ->values();
            $allowedConditions = collect($this->input('allowed_conditions', []))
                ->filter()
                ->values();
            $allowedDispositions = collect($this->input('allowed_dispositions', []))
                ->filter()
                ->values();
            $recommendedDispositions = collect($this->input('recommended_dispositions', []))
                ->filter(fn($value) => filled($value));

            $requiredPhotoCount = (int) $this->input('required_photo_count', 0);

            if ($requiredPhotoCount > 0 && $photoTypes->isEmpty()) {
                $validator->errors()->add(
                    'required_photo_types',
                    'Select the evidence photo template before setting a required photo count.'
                );
            }

            if ($requiredPhotoCount > $photoTypes->count()) {
                $validator->errors()->add(
                    'required_photo_count',
                    'Required photo count cannot exceed the number of selected required photo types.'
                );
            }

            foreach ($recommendedDispositions as $condition => $disposition) {
                if (!$allowedConditions->contains($condition)) {
                    $validator->errors()->add(
                        "recommended_dispositions.{$condition}",
                        'Recommended actions can only be set for allowed conditions.'
                    );
                }

                if (!$allowedDispositions->contains($disposition)) {
                    $validator->errors()->add(
                        "recommended_dispositions.{$condition}",
                        'Recommended actions must point to one of the allowed warehouse actions.'
                    );
                }
            }
        });
    }
}
