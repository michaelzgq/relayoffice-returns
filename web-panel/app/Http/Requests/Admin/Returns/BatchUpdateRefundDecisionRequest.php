<?php

namespace App\Http\Requests\Admin\Returns;

use App\Http\Requests\ValidationHandler;
use App\Models\ReturnCase;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class BatchUpdateRefundDecisionRequest extends ValidationHandler
{
    protected ?Collection $resolvedCases = null;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'case_ids' => ['required', 'array', 'min:1'],
            'case_ids.*' => ['required', 'integer', 'exists:return_cases,id'],
            'refund_status' => ['required', Rule::in(['hold', 'ready_to_release', 'needs_review', 'released'])],
            'decision_note' => ['nullable', 'string', 'max:1000'],
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
            $targetStatus = (string) $this->input('refund_status');
            $resources = $this->returnCases();

            if ($targetStatus === 'needs_review' && !$this->filled('decision_note')) {
                $validator->errors()->add('decision_note', 'A batch note is required when cases are moved to Needs ops review.');
            }

            if (!in_array($targetStatus, ['ready_to_release', 'released'], true)) {
                return;
            }

            $blocked = $resources
                ->where('evidence_complete', false)
                ->pluck('return_id')
                ->values();

            if ($blocked->isNotEmpty()) {
                $validator->errors()->add(
                    'case_ids',
                    'Evidence must be complete before a case can move to Ready for brand review or Decision completed. Blocked cases: ' . $blocked->implode(', ')
                );
            }
        });
    }

    public function returnCases(): Collection
    {
        if ($this->resolvedCases instanceof Collection) {
            return $this->resolvedCases;
        }

        $caseIds = collect($this->input('case_ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        $this->resolvedCases = ReturnCase::query()
            ->whereIn('id', $caseIds)
            ->get()
            ->keyBy('id');

        return $this->resolvedCases;
    }

    protected function getRedirectUrl(): string
    {
        return route('admin.returns.queue.index', array_filter([
            'search' => $this->input('search'),
            'brand_id' => $this->input('brand_id'),
            'evidence_missing' => $this->boolean('evidence_missing') ? 1 : null,
            'filter_status' => $this->input('filter_status'),
            'min_sla_hours' => $this->input('min_sla_hours'),
        ], fn ($value) => !is_null($value) && $value !== ''));
    }
}
