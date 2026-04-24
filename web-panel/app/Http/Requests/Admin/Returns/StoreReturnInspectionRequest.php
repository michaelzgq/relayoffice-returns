<?php

namespace App\Http\Requests\Admin\Returns;

use App\CPU\Helpers;
use App\Http\Requests\ValidationHandler;
use App\Models\BrandRuleProfile;
use App\Models\ReturnCase;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreReturnInspectionRequest extends ValidationHandler
{
    protected ?BrandRuleProfile $resolvedRuleProfile = null;
    protected ?ReturnCase $resolvedCase = null;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'case_id' => ['nullable', 'exists:return_cases,id'],
            'expected_inbound_id' => ['nullable', 'exists:return_expected_inbounds,id'],
            'save_as_draft' => ['nullable', 'boolean'],
            'offline_draft_uuid' => ['nullable', 'string', 'max:80'],
            'return_id' => ['required', 'string', 'max:255'],
            'brand_id' => ['required', 'exists:brands,id'],
            'product_sku' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'condition_code' => [$this->isDraftSubmission() ? 'nullable' : 'required', Rule::in([
                'unopened',
                'like_new',
                'opened_resaleable',
                'opened_damaged',
                'wrong_item',
                'empty_box',
                'missing_parts',
                'custom',
            ])],
            'disposition_code' => ['nullable', Rule::in([
                'restock',
                'hold',
                'return_to_brand',
                'refurb',
                'destroy',
                'quarantine',
            ])],
            'refund_status' => ['nullable', Rule::in(['hold', 'ready_to_release', 'needs_review', 'released'])],
            'received_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['nullable', 'image', 'mimes:' . str_replace(['.', ' '], '', IMAGE_ACCEPTED_EXTENSIONS) . '|max:' . convertBytesToKiloBytes(maxUploadSize('image'))],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $ruleProfile = $this->ruleProfile();

            if (!$ruleProfile) {
                $validator->errors()->add('brand_id', 'This brand does not have an active rule profile yet.');
                return;
            }

            if ($this->isDraftSubmission()) {
                $this->validateDraftSubmission($validator, $ruleProfile);
                return;
            }

            if ($ruleProfile->sku_required && !$this->filled('product_sku')) {
                $validator->errors()->add('product_sku', 'SKU is required for the selected brand.');
            }

            if ($ruleProfile->serial_required && !$this->filled('serial_number')) {
                $validator->errors()->add('serial_number', 'Serial number is required for the selected brand.');
            }

            if ($ruleProfile->notes_required && !$this->filled('notes')) {
                $validator->errors()->add('notes', 'Inspection notes are required for the selected brand.');
            }

            $allowedConditions = $ruleProfile->allowed_conditions ?? [];
            if ($allowedConditions && !in_array($this->input('condition_code'), $allowedConditions, true)) {
                $validator->errors()->add('condition_code', 'Selected condition is not allowed by the brand rule profile.');
            }

            $resolvedDisposition = $this->resolvedDispositionCode();
            if (!$resolvedDisposition) {
                $validator->errors()->add(
                    'disposition_code',
                    'Choose a warehouse action or set a recommended action in the client playbook for this condition.'
                );
            }

            $allowedDispositions = $ruleProfile->allowed_dispositions ?? [];
            if ($resolvedDisposition && $allowedDispositions && !in_array($resolvedDisposition, $allowedDispositions, true)) {
                $validator->errors()->add('disposition_code', 'Selected disposition is not allowed by the brand rule profile.');
            }

            $requiredPhotoCount = (int) ($ruleProfile->required_photo_count ?? 0);
            $totalEvidenceCount = $this->existingEvidenceCount() + $this->uploadedEvidenceCount();

            if ($requiredPhotoCount > 0 && $totalEvidenceCount < $requiredPhotoCount) {
                $validator->errors()->add(
                    'photos',
                    "At least {$requiredPhotoCount} evidence photo(s) are required for the selected brand. Currently available: {$totalEvidenceCount}."
                );
            }
        });
    }

    public function ruleProfile(): ?BrandRuleProfile
    {
        if ($this->resolvedRuleProfile instanceof BrandRuleProfile) {
            return $this->resolvedRuleProfile;
        }

        if (!$this->filled('brand_id')) {
            return null;
        }

        $this->resolvedRuleProfile = BrandRuleProfile::query()
            ->where('brand_id', $this->integer('brand_id'))
            ->where('active', 1)
            ->first();

        return $this->resolvedRuleProfile;
    }

    public function inspectionCase(): ?ReturnCase
    {
        if ($this->resolvedCase instanceof ReturnCase) {
            return $this->resolvedCase;
        }

        if (!$this->filled('case_id')) {
            return null;
        }

        $this->resolvedCase = ReturnCase::query()
            ->withCount('media')
            ->find($this->integer('case_id'));

        return $this->resolvedCase;
    }

    public function existingEvidenceCount(): int
    {
        return (int) ($this->inspectionCase()?->media_count ?? 0);
    }

    public function uploadedEvidenceCount(): int
    {
        return count(array_filter((array) $this->file('photos', [])));
    }

    public function resolvedRefundStatus(): string
    {
        if (!Helpers::admin_has_module('returns_queue_section')) {
            return (string) ($this->ruleProfile()?->default_refund_status ?: 'hold');
        }

        return (string) ($this->input('refund_status') ?: $this->ruleProfile()?->default_refund_status ?: 'hold');
    }

    public function resolvedDispositionCode(): ?string
    {
        if ($this->filled('disposition_code')) {
            return (string) $this->input('disposition_code');
        }

        return $this->ruleProfile()?->recommendedDispositionForCondition((string) $this->input('condition_code'));
    }

    public function isDraftSubmission(): bool
    {
        return $this->boolean('save_as_draft');
    }

    protected function getRedirectUrl(): string
    {
        $parameters = [];

        if ($this->filled('case_id')) {
            $parameters['case_id'] = $this->input('case_id');
        } elseif ($this->filled('expected_inbound_id')) {
            $parameters['expected_id'] = $this->input('expected_inbound_id');
        }

        return route('admin.returns.inspect', $parameters);
    }

    private function validateDraftSubmission(Validator $validator, BrandRuleProfile $ruleProfile): void
    {
        $allowedConditions = $ruleProfile->allowed_conditions ?? [];
        if ($this->filled('condition_code') && $allowedConditions && !in_array($this->input('condition_code'), $allowedConditions, true)) {
            $validator->errors()->add('condition_code', 'Selected condition is not allowed by the brand rule profile.');
        }

        $resolvedDisposition = $this->resolvedDispositionCode();
        $allowedDispositions = $ruleProfile->allowed_dispositions ?? [];
        if ($resolvedDisposition && $allowedDispositions && !in_array($resolvedDisposition, $allowedDispositions, true)) {
            $validator->errors()->add('disposition_code', 'Selected disposition is not allowed by the brand rule profile.');
        }
    }
}
