<?php

namespace App\Http\Requests\Admin\Returns;

use App\Http\Requests\ValidationHandler;
use App\Models\ReturnCase;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateRefundDecisionRequest extends ValidationHandler
{
    protected ?ReturnCase $resolvedCase = null;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'refund_status' => ['required', Rule::in(['hold', 'ready_to_release', 'needs_review', 'released'])],
            'decision_note' => ['nullable', 'string', 'max:1000'],
            'redirect_to' => ['nullable', Rule::in(['case', 'queue'])],
            'search' => ['nullable', 'string', 'max:255'],
            'brand_id' => ['nullable', 'integer'],
            'evidence_missing' => ['nullable'],
            'filter_status' => ['nullable', Rule::in(['hold', 'ready_to_release', 'needs_review', 'released'])],
            'min_sla_hours' => ['nullable', 'integer', 'in:24,48'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $resource = $this->returnCase();
            $targetStatus = (string) $this->input('refund_status');

            if (!$resource) {
                return;
            }

            if (in_array($targetStatus, ['ready_to_release', 'released'], true) && !$resource->evidence_complete) {
                $validator->errors()->add('refund_status', 'Evidence must be complete before a case can move to Ready for brand review or Decision completed.');
            }

            if ($targetStatus === 'needs_review' && !$this->filled('decision_note')) {
                $validator->errors()->add('decision_note', 'A decision note is required when a case is moved to Needs ops review.');
            }
        });
    }

    protected function getRedirectUrl(): string
    {
        if ($this->input('redirect_to') === 'queue') {
            return route('admin.returns.queue.index', array_filter([
                'search' => $this->input('search'),
                'brand_id' => $this->input('brand_id'),
                'evidence_missing' => $this->boolean('evidence_missing') ? 1 : null,
                'filter_status' => $this->input('filter_status'),
                'min_sla_hours' => $this->input('min_sla_hours'),
            ], fn ($value) => !is_null($value) && $value !== ''));
        }

        if ($this->route('id')) {
            return route('admin.returns.cases.show', $this->route('id'));
        }

        return parent::getRedirectUrl();
    }

    private function returnCase(): ?ReturnCase
    {
        if ($this->resolvedCase instanceof ReturnCase) {
            return $this->resolvedCase;
        }

        if (!$this->route('id')) {
            return null;
        }

        $this->resolvedCase = ReturnCase::query()->find($this->route('id'));

        return $this->resolvedCase;
    }
}
