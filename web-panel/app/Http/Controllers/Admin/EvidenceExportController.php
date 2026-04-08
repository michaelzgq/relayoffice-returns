<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Models\ReturnCase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use Mpdf\Mpdf;

class EvidenceExportController extends Controller
{
    public function __construct(private readonly ReturnCase $returnCase)
    {
    }

    public function show(Request $request, int $id): View|Response
    {
        $resource = $this->returnCase
            ->newQuery()
            ->when(Helpers::returns_user_is_inspector(), fn($query) => $query->where('created_by', auth('admin')->id()))
            ->with(['brand', 'ruleProfile', 'media', 'events', 'refundDecision'])
            ->findOrFail($id);

        $packData = $this->buildPackData($resource);

        if ($request->query('download') === 'pdf') {
            return $this->downloadPdf($resource, $packData);
        }

        return view('admin-views.returns.cases.export', array_merge([
            'resource' => $resource,
        ], $packData));
    }

    public function brandReview(Request $request, int $id): View
    {
        $resource = $this->returnCase
            ->newQuery()
            ->with(['brand', 'ruleProfile', 'media', 'events', 'refundDecision'])
            ->findOrFail($id);

        $packData = $this->buildPackData($resource, externalView: true);
        $expiresAt = $this->expiresAtFromRequest($request);

        return view('admin-views.returns.cases.brand-review', array_merge([
            'resource' => $resource,
            'brandReviewPdfUrl' => URL::temporarySignedRoute('returns.brand-review.pdf', $expiresAt, ['id' => $resource->id]),
            'brandReviewExpiresAt' => $expiresAt,
        ], $packData));
    }

    public function brandReviewPdf(Request $request, int $id): Response
    {
        $resource = $this->returnCase
            ->newQuery()
            ->with(['brand', 'ruleProfile', 'media', 'events', 'refundDecision'])
            ->findOrFail($id);

        return $this->downloadPdf(
            resource: $resource,
            packData: $this->buildPackData($resource, externalView: true),
            externalView: true,
        );
    }

    private function buildPackData(ReturnCase $resource, bool $externalView = false): array
    {
        $requiredPhotoTypes = collect($resource->ruleProfile?->required_photo_types ?? [])
            ->filter()
            ->values();
        $capturedPhotoTypes = $resource->media
            ->pluck('capture_type')
            ->filter()
            ->values();

        $evidenceChecklist = $requiredPhotoTypes->map(function (string $photoType) use ($capturedPhotoTypes) {
            return [
                'label' => str_replace('_', ' ', $photoType),
                'captured' => $capturedPhotoTypes->contains($photoType),
            ];
        });

        $coverageGaps = collect();
        if (!$resource->evidence_complete) {
            $coverageGaps->push('Evidence set is incomplete for the assigned brand rule profile.');
        }
        if ($resource->ruleProfile?->sku_required && empty($resource->product_sku)) {
            $coverageGaps->push('SKU is required by the rule profile but missing on the case.');
        }
        if ($resource->ruleProfile?->serial_required && empty($resource->serial_number)) {
            $coverageGaps->push('Serial number is required by the rule profile but missing on the case.');
        }
        if ($resource->ruleProfile?->notes_required && empty($resource->notes)) {
            $coverageGaps->push('Inspector notes are required by the rule profile but were not captured.');
        }

        $recommendedDisposition = $resource->ruleProfile?->recommendedDispositionForCondition($resource->condition_code);
        $conditionLabel = str_replace('_', ' ', (string) $resource->condition_code);
        $dispositionLabel = str_replace('_', ' ', (string) $resource->disposition_code);
        $decisionStateLabel = ReturnCase::decisionStatusLabel($resource->refund_status);
        $brandName = $resource->brand?->name ?? 'Unknown brand';
        $evidenceSummary = $resource->evidence_complete
            ? 'Evidence checklist is complete.'
            : 'Evidence checklist still has missing items.';

        $shareReadiness = $coverageGaps->isNotEmpty()
            ? [
                'tone' => 'bad',
                'label' => $externalView ? 'Needs warehouse follow-up' : 'Internal only',
                'summary' => $externalView
                    ? 'This case still has evidence gaps. Treat this link as a review record, not a final decision package.'
                    : 'Coverage gaps still need to be resolved before this pack is safe to share outside the ops team.',
            ]
            : match ($resource->refund_status) {
                'released' => [
                    'tone' => 'good',
                    'label' => 'Decision completed',
                    'summary' => 'Evidence is complete and the final case decision has already been executed.',
                ],
                'ready_to_release' => [
                    'tone' => 'good',
                    'label' => 'Brand-ready',
                    'summary' => $externalView
                        ? 'Evidence is complete and the case is ready for brand-side review.'
                        : 'Evidence is complete and supports a ready-to-release refund decision.',
                ],
                'needs_review' => [
                    'tone' => 'warn',
                    'label' => 'Review-ready',
                    'summary' => 'Evidence is complete, but ops review is still required before the final movement decision.',
                ],
                default => [
                    'tone' => 'warn',
                    'label' => 'Ops hold',
                    'summary' => $externalView
                        ? 'Case evidence is documented, but the warehouse is still holding the case pending internal review.'
                        : 'Case evidence is documented, but the refund is still being held pending an ops decision.',
                ],
            };

        $summarySentence = sprintf(
            '%s return %s was inspected as %s and assigned warehouse action %s. The case currently has %d of %d required evidence slot(s) captured, and decision state is %s.',
            $brandName,
            $resource->return_id,
            $conditionLabel,
            $dispositionLabel,
            (int) $resource->evidence_photo_count,
            (int) $resource->required_photo_count,
            $decisionStateLabel
        );

        $whatThisPackShows = collect([
            $resource->evidence_complete
                ? 'Required evidence coverage is complete for the linked client playbook.'
                : 'Evidence coverage is incomplete for the linked client playbook.',
            $recommendedDisposition
                ? ($resource->disposition_code === $recommendedDisposition
                    ? 'The selected warehouse action matches the playbook recommendation for this condition.'
                    : 'The selected warehouse action overrides the playbook recommendation for this condition.')
                : 'This playbook does not define a default warehouse action for the selected condition.',
            $resource->serial_number
                ? 'A serial number was captured on this case.'
                : ($resource->ruleProfile?->serial_required ? 'A serial number is required by the playbook but missing.' : null),
            $externalView
                ? 'This share view omits internal-only notes and queue controls.'
                : ($resource->refundDecision?->reason
                    ? 'A decision note is attached for audit and brand communication.'
                    : 'No decision note has been recorded yet.'),
            $externalView
                ? 'This link is designed for brand-side review and dispute follow-up.'
                : ($resource->notes
                    ? 'Inspector notes are attached to explain the observed condition.'
                    : 'No inspector notes are attached to explain the observed condition.'),
        ])->filter()->values();

        $actionsNeeded = collect();
        foreach ($coverageGaps as $gap) {
            $actionsNeeded->push($externalView ? 'Warehouse follow-up needed: ' . $gap : $gap);
        }
        if (!$externalView && !$resource->refundDecision?->reason) {
            $actionsNeeded->push('Add a clear decision note before sharing this pack with brand stakeholders.');
        }
        if ($resource->refund_status === 'hold' && $coverageGaps->isEmpty()) {
            $actionsNeeded->push($externalView
                ? 'This case is still on an internal hold. Ask the warehouse team whether more review is pending.'
                : 'Case is still on hold. Add release or review rationale before treating this as a final brand-facing pack.');
        }

        $decisionBasis = $externalView
            ? collect([
                ['label' => 'Rule profile', 'value' => $resource->ruleProfile?->profile_name ?? 'No linked rule profile'],
                ['label' => 'Condition', 'value' => ucfirst($conditionLabel)],
                ['label' => 'Warehouse action', 'value' => ucfirst($dispositionLabel)],
                ['label' => 'Playbook recommendation', 'value' => $recommendedDisposition ? ucfirst(str_replace('_', ' ', $recommendedDisposition)) : 'No playbook default'],
                ['label' => 'Decision state', 'value' => $decisionStateLabel],
                ['label' => 'Evidence readiness', 'value' => $evidenceSummary],
            ])
            : collect([
                ['label' => 'Rule profile', 'value' => $resource->ruleProfile?->profile_name ?? 'No linked rule profile'],
                ['label' => 'Condition', 'value' => ucfirst($conditionLabel)],
                ['label' => 'Warehouse action', 'value' => ucfirst($dispositionLabel)],
                ['label' => 'Playbook recommendation', 'value' => $recommendedDisposition ? ucfirst(str_replace('_', ' ', $recommendedDisposition)) : 'No playbook default'],
                ['label' => 'Decision state', 'value' => $decisionStateLabel],
                ['label' => 'Decision note', 'value' => $resource->refundDecision?->reason ?: 'No decision note recorded'],
            ]);

        $mediaAssets = $resource->media->map(function ($media) {
            $publicPath = 'return-cases/' . $media->file_path;
            $localPath = !empty($media->file_path) && Storage::disk('public')->exists($publicPath)
                ? Storage::disk('public')->path($publicPath)
                : null;

            return [
                'capture_type' => $media->capture_type ?? 'evidence',
                'sort_order' => $media->sort_order,
                'web_url' => $media->file_fullpath,
                'pdf_path' => $localPath,
                'has_file' => (bool) $localPath,
            ];
        })->values();

        $timelineItems = $resource->events->map(function ($event) use ($externalView) {
            return [
                'time' => $event->created_at?->format('Y-m-d H:i') ?? 'N/A',
                'title' => $event->title,
                'description' => $externalView ? null : ($event->description ?: 'No event detail captured.'),
            ];
        })->values();

        return [
            'externalView' => $externalView,
            'evidenceChecklist' => $evidenceChecklist,
            'coverageGaps' => $coverageGaps,
            'shareReadiness' => $shareReadiness,
            'brandDefenseSummary' => $summarySentence,
            'whatThisPackShows' => $whatThisPackShows,
            'actionsNeeded' => $actionsNeeded,
            'decisionBasis' => $decisionBasis,
            'recommendedDisposition' => $recommendedDisposition,
            'mediaAssets' => $mediaAssets,
            'timelineItems' => $timelineItems,
            'decisionStateLabel' => $decisionStateLabel,
        ];
    }

    private function downloadPdf(ReturnCase $resource, array $packData, bool $externalView = false): Response
    {
        $html = view('admin-views.returns.cases.export-pdf', array_merge([
            'resource' => $resource,
        ], $packData))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->WriteHTML($html);

        $filename = ($externalView ? 'brand_review_record_' : 'brand_defense_pack_') . strtolower($resource->return_id) . '.pdf';

        return response($mpdf->Output($filename, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function expiresAtFromRequest(Request $request): Carbon
    {
        $expiresAt = $request->query('expires');

        if (is_numeric($expiresAt) && (int) $expiresAt > now()->timestamp) {
            return Carbon::createFromTimestamp((int) $expiresAt);
        }

        return now()->addDays(7);
    }
}
