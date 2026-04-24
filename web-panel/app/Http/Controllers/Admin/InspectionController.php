<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Returns\StoreReturnInspectionRequest;
use App\Models\Brand;
use App\Models\BrandRuleProfile;
use App\Models\RefundGateDecision;
use App\Models\ReturnCase;
use App\Models\ReturnCaseEvent;
use App\Models\ReturnCaseMedia;
use App\Models\ReturnExpectedInbound;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use function App\CPU\translate;

class InspectionController extends Controller
{
    public function __construct(
        private readonly Brand $brand,
        private readonly BrandRuleProfile $brandRuleProfile,
        private readonly ReturnCase $returnCase
    ) {
    }

    public function create(Request $request): View
    {
        $currentCase = null;
        if ($request->filled('case_id')) {
            $currentCase = $this->visibleCaseQuery()
                ->with(['brand', 'ruleProfile', 'media', 'expectedInbound'])
                ->findOrFail($request->integer('case_id'));
        }

        $expectedInbound = $currentCase?->expectedInbound;
        if (!$currentCase && $request->filled('expected_id')) {
            $expectedInbound = ReturnExpectedInbound::query()
                ->with('brand')
                ->findOrFail($request->integer('expected_id'));
        }

        $profiles = $this->brandRuleProfile
            ->where('active', 1)
            ->get()
            ->keyBy('brand_id');

        return view('admin-views.returns.cases.inspect', [
            'currentCase' => $currentCase,
            'brands' => $this->brand->where('status', 1)->orderBy('name')->get(),
            'profiles' => $profiles,
            'profilesForJs' => $profiles->mapWithKeys(function (BrandRuleProfile $profile) {
                return [
                    (string) $profile->brand_id => [
                        'brand_id' => $profile->brand_id,
                        'profile_name' => $profile->profile_name,
                        'required_photo_count' => $profile->required_photo_count,
                        'required_photo_types' => $profile->required_photo_types ?? [],
                        'allowed_conditions' => $profile->allowed_conditions ?? [],
                        'allowed_dispositions' => $profile->allowed_dispositions ?? [],
                        'recommended_dispositions' => $profile->recommended_dispositions ?? [],
                        'auto_hold_triggers' => $profile->auto_hold_triggers ?? [],
                        'reviewer_note_template' => $profile->reviewer_note_template,
                        'rule_version' => (int) ($profile->rule_version ?? 1),
                        'default_refund_status' => $profile->default_refund_status,
                        'notes_required' => (bool) $profile->notes_required,
                        'sku_required' => (bool) $profile->sku_required,
                        'serial_required' => (bool) $profile->serial_required,
                    ],
                ];
            })->all(),
            'expectedInbound' => $expectedInbound,
            'conditionOptions' => ReturnCase::conditionOptions(),
            'dispositionOptions' => ReturnCase::dispositionOptions(),
            'refundStatusOptions' => ReturnCase::refundStatusOptions(),
        ]);
    }

    public function store(StoreReturnInspectionRequest $request): RedirectResponse
    {
        $ruleProfile = $request->ruleProfile();
        $isDraft = $request->isDraftSubmission();
        $resolvedRefundStatus = $isDraft ? 'hold' : $request->resolvedRefundStatus();
        $resolvedDisposition = $request->resolvedDispositionCode() ?: 'hold';

        $case = DB::transaction(function () use ($request, $ruleProfile, $resolvedRefundStatus, $resolvedDisposition, $isDraft) {
            $case = $request->filled('case_id')
                ? $this->visibleCaseQuery()->findOrFail($request->integer('case_id'))
                : new ReturnCase();
            $expectedInbound = $this->resolveExpectedInbound($request);

            $case->fill([
                'return_id' => (string) $request->input('return_id'),
                'brand_id' => $request->integer('brand_id'),
                'brand_rule_profile_id' => $ruleProfile?->id,
                'expected_inbound_id' => $expectedInbound?->id,
                'product_sku' => $request->filled('product_sku') ? (string) $request->input('product_sku') : null,
                'serial_number' => $request->filled('serial_number') ? (string) $request->input('serial_number') : null,
                'condition_code' => $request->filled('condition_code') ? (string) $request->input('condition_code') : 'custom',
                'disposition_code' => (string) $resolvedDisposition,
                'refund_status' => $resolvedRefundStatus,
                'inspection_status' => $isDraft ? 'draft' : 'completed',
                'required_photo_count' => $ruleProfile?->required_photo_count ?? 0,
                'notes' => $request->filled('notes') ? (string) $request->input('notes') : null,
                'received_at' => $request->filled('received_at') ? $request->date('received_at') : now(),
                'inspected_at' => $isDraft ? null : now(),
                'created_by' => $case->exists ? $case->created_by : auth('admin')->id(),
                'offline_draft_uuid' => $request->filled('offline_draft_uuid') ? (string) $request->input('offline_draft_uuid') : $case->offline_draft_uuid,
                'sync_status' => $isDraft ? 'draft' : 'synced',
                'sync_error' => null,
                'draft_payload' => $isDraft ? $request->except(['_token', 'photos']) : null,
            ]);
            $case->save();

            $existingCount = $case->media()->count();
            $captureTypes = array_values($ruleProfile?->required_photo_types ?: ReturnCase::photoTypeOptions());
            foreach ($request->file('photos', []) as $index => $photo) {
                $path = Helpers::upload('return-cases/', APPLICATION_IMAGE_FORMAT, $photo);
                ReturnCaseMedia::create([
                    'return_case_id' => $case->id,
                    'file_path' => $path,
                    'capture_type' => $captureTypes[$existingCount + $index] ?? $captureTypes[$index] ?? ReturnCase::photoTypeOptions()[$existingCount + $index] ?? null,
                    'sort_order' => $existingCount + $index + 1,
                    'uploaded_by' => auth('admin')->id(),
                ]);
            }

            $mediaCount = $case->media()->count();
            $case->update([
                'evidence_photo_count' => $mediaCount,
                'evidence_complete' => $mediaCount >= ($case->required_photo_count ?? 0),
            ]);

            if ($expectedInbound) {
                $case->loadMissing('expectedInbound');
                $expectedInbound->update([
                    'status' => $isDraft ? 'in_review' : ($expectedInbound->mismatchSummaryForCase($case) ? 'exception' : 'received'),
                    'matched_return_case_id' => $case->id,
                ]);
            }

            if (!$isDraft) {
                RefundGateDecision::updateOrCreate(
                    ['return_case_id' => $case->id],
                    [
                        'status' => $resolvedRefundStatus,
                        'reason' => $request->filled('notes') ? (string) $request->input('notes') : null,
                        'decided_by' => auth('admin')->id(),
                        'decided_at' => now(),
                    ]
                );
            }

            ReturnCaseEvent::create([
                'return_case_id' => $case->id,
                'event_type' => $isDraft ? 'inspection_draft_saved' : 'inspection_submitted',
                'title' => $isDraft ? 'Inspection draft saved' : ($request->filled('case_id') ? 'Inspection updated' : 'Inspection submitted'),
                'description' => 'Condition: ' . $request->input('condition_code') . ', disposition: ' . $resolvedDisposition,
                'meta' => [
                    'refund_status' => $resolvedRefundStatus,
                    'evidence_complete' => $case->evidence_complete,
                    'recommended_disposition_used' => !$request->filled('disposition_code') && !$isDraft,
                    'expected_inbound_id' => $expectedInbound?->id,
                    'draft' => $isDraft,
                ],
                'created_by' => auth('admin')->id(),
            ]);

            return $case;
        });

        Toastr::success($isDraft ? translate('Return case draft saved') : translate('Return case saved successfully'));
        return redirect()->route('admin.returns.cases.show', $case->id);
    }

    private function resolveExpectedInbound(StoreReturnInspectionRequest $request): ?ReturnExpectedInbound
    {
        if ($request->filled('expected_inbound_id')) {
            $expectedInbound = ReturnExpectedInbound::query()->find($request->integer('expected_inbound_id'));

            if ($expectedInbound && (int) $expectedInbound->brand_id === $request->integer('brand_id')) {
                return $expectedInbound;
            }
        }

        return ReturnExpectedInbound::query()
            ->where('brand_id', $request->integer('brand_id'))
            ->where('return_id', (string) $request->input('return_id'))
            ->first();
    }

    private function visibleCaseQuery()
    {
        return $this->returnCase
            ->newQuery()
            ->when(Helpers::returns_user_is_inspector(), fn($query) => $query->where('created_by', auth('admin')->id()));
    }
}
