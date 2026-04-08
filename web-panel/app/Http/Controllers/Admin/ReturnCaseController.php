<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Returns\BatchUpdateRefundDecisionRequest;
use App\Http\Requests\Admin\Returns\UpdateRefundDecisionRequest;
use App\Models\Brand;
use App\Models\RefundGateDecision;
use App\Models\ReturnCase;
use App\Models\ReturnCaseEvent;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use function App\CPU\translate;

class ReturnCaseController extends Controller
{
    public function __construct(
        private readonly ReturnCase $returnCase,
        private readonly Brand $brand
    ) {
    }

    public function index(Request $request): View
    {
        $inspectorView = Helpers::returns_user_is_inspector();

        return view('admin-views.returns.cases.index', [
            'resources' => $this->buildCaseQuery($request)->paginate(Helpers::pagination_limit())->appends($request->query()),
            'brands' => $this->brand->where('status', 1)->orderBy('name')->get(),
            'refundStatusOptions' => ReturnCase::refundStatusOptions(),
            'inspectorView' => $inspectorView,
        ]);
    }

    public function show(int $id): View
    {
        $resource = $this->visibleCaseQuery()
            ->with(['brand', 'ruleProfile', 'media', 'events', 'refundDecision'])
            ->findOrFail($id);

        return view('admin-views.returns.cases.show', [
            'resource' => $resource,
            'refundStatusOptions' => ReturnCase::refundStatusOptions(),
            'canManageRefundGate' => Helpers::admin_has_module('returns_queue_section'),
            'inspectorView' => Helpers::returns_user_is_inspector(),
        ]);
    }

    public function queue(Request $request): View
    {
        $baseQuery = $this->buildCaseQuery($request)
            ->whereIn('refund_status', ['hold', 'ready_to_release', 'needs_review']);

        return view('admin-views.returns.queue.index', [
            'resources' => (clone $baseQuery)
                ->paginate(Helpers::pagination_limit())
                ->appends($request->query()),
            'brands' => $this->brand->where('status', 1)->orderBy('name')->get(),
            'refundStatusOptions' => ReturnCase::refundStatusOptions(),
            'columnCounts' => (clone $baseQuery)
                ->select('refund_status', DB::raw('count(*) as total'))
                ->groupBy('refund_status')
                ->pluck('total', 'refund_status'),
        ]);
    }

    public function dashboard(): View
    {
        $openQueueQuery = $this->returnCase
            ->newQuery()
            ->whereIn('refund_status', ['hold', 'ready_to_release', 'needs_review']);

        $inspectionsToday = $this->returnCase
            ->newQuery()
            ->whereDate('inspected_at', today())
            ->count();

        $awaitingRefundAction = (clone $openQueueQuery)->count();

        $readyToReleaseCount = (clone $openQueueQuery)
            ->where('refund_status', 'ready_to_release')
            ->count();

        $over48hStuck = (clone $openQueueQuery)
            ->where('refund_status', 'hold')
            ->whereRaw($this->slaAgeSql() . ' >= ?', [48])
            ->count();

        $brandsWithHighestBacklog = (clone $openQueueQuery)
            ->select('brand_id', DB::raw('count(*) as backlog'))
            ->with('brand:id,name')
            ->groupBy('brand_id')
            ->orderByDesc('backlog')
            ->limit(5)
            ->get();

        $brandsWithBacklogCount = (clone $openQueueQuery)
            ->whereNotNull('brand_id')
            ->distinct('brand_id')
            ->count('brand_id');

        $missingEvidenceCases = (clone $openQueueQuery)
            ->with('brand:id,name')
            ->where('evidence_complete', false)
            ->latest()
            ->limit(10)
            ->get();

        $missingEvidenceCount = (clone $openQueueQuery)
            ->where('evidence_complete', false)
            ->count();

        $recentInspections = $this->returnCase
            ->newQuery()
            ->with('brand:id,name')
            ->latest('inspected_at')
            ->limit(8)
            ->get();

        return view('admin-views.returns.dashboard.index', compact(
            'inspectionsToday',
            'awaitingRefundAction',
            'readyToReleaseCount',
            'over48hStuck',
            'brandsWithBacklogCount',
            'brandsWithHighestBacklog',
            'missingEvidenceCount',
            'missingEvidenceCases',
            'recentInspections'
        ));
    }

    public function updateRefundDecision(UpdateRefundDecisionRequest $request, int $id): RedirectResponse
    {
        $resource = $this->returnCase->findOrFail($id);

        DB::transaction(function () use ($request, $resource) {
            $this->applyRefundDecision(
                resource: $resource,
                targetStatus: (string) $request->input('refund_status'),
                decisionNote: $request->filled('decision_note') ? (string) $request->input('decision_note') : null,
                title: $request->input('redirect_to') === 'queue' ? 'Refund gate updated from queue' : 'Refund gate updated',
                eventMeta: ['source' => $request->input('redirect_to') === 'queue' ? 'queue' : 'case_detail']
            );
        });

        Toastr::success(translate('Refund gate decision updated'));

        if ($request->input('redirect_to') === 'queue') {
            return redirect()->route('admin.returns.queue.index', $this->queueRedirectQuery($request));
        }

        return redirect()->route('admin.returns.cases.show', $resource->id);
    }

    public function batchUpdateRefundDecision(BatchUpdateRefundDecisionRequest $request): RedirectResponse
    {
        $resources = $request->returnCases();
        $targetStatus = (string) $request->input('refund_status');
        $decisionNote = $request->filled('decision_note') ? (string) $request->input('decision_note') : null;

        DB::transaction(function () use ($resources, $targetStatus, $decisionNote) {
            foreach ($resources as $resource) {
                $this->applyRefundDecision(
                    resource: $resource,
                    targetStatus: $targetStatus,
                    decisionNote: $decisionNote,
                    title: 'Refund gate updated from batch queue action',
                    eventMeta: ['source' => 'queue_batch']
                );
            }
        });

        Toastr::success($resources->count() . ' refund case(s) updated from queue');
        return redirect()->route('admin.returns.queue.index', $this->queueRedirectQuery($request));
    }

    private function buildCaseQuery(Request $request)
    {
        $filterStatus = $request->input('filter_status', $request->input('refund_status'));

        return $this->visibleCaseQuery()
            ->with(['brand', 'ruleProfile', 'refundDecision'])
            ->when($request->filled('brand_id'), fn($query) => $query->where('brand_id', $request->integer('brand_id')))
            ->when(filled($filterStatus), fn($query) => $query->where('refund_status', (string) $filterStatus))
            ->when($request->boolean('evidence_missing'), fn($query) => $query->where('evidence_complete', false))
            ->when($request->filled('min_sla_hours'), fn($query) => $query->whereRaw($this->slaAgeSql() . ' >= ?', [$request->integer('min_sla_hours')]))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = (string) $request->input('search');
                $query->where(function ($nested) use ($search) {
                    $nested->where('return_id', 'like', "%{$search}%")
                        ->orWhere('product_sku', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%");
                });
            })
            ->latest();
    }

    private function visibleCaseQuery()
    {
        return $this->returnCase
            ->newQuery()
            ->when(Helpers::returns_user_is_inspector(), fn($query) => $query->where('created_by', auth('admin')->id()));
    }

    private function applyRefundDecision(
        ReturnCase $resource,
        string $targetStatus,
        ?string $decisionNote,
        string $title,
        array $eventMeta = []
    ): void {
        $resource->update([
            'refund_status' => $targetStatus,
            'refund_decided_at' => now(),
        ]);

        RefundGateDecision::updateOrCreate(
            ['return_case_id' => $resource->id],
            [
                'status' => $targetStatus,
                'reason' => $decisionNote,
                'meta' => $eventMeta ?: null,
                'decided_by' => auth('admin')->id(),
                'decided_at' => now(),
            ]
        );

        ReturnCaseEvent::create([
            'return_case_id' => $resource->id,
            'event_type' => 'refund_gate_updated',
            'title' => $title,
            'description' => $decisionNote,
            'meta' => array_merge(['refund_status' => $targetStatus], $eventMeta),
            'created_by' => auth('admin')->id(),
        ]);
    }

    private function queueRedirectQuery(Request $request): array
    {
        return array_filter([
            'search' => $request->input('search'),
            'brand_id' => $request->input('brand_id'),
            'evidence_missing' => $request->boolean('evidence_missing') ? 1 : null,
            'filter_status' => $request->input('filter_status'),
            'min_sla_hours' => $request->input('min_sla_hours'),
        ], fn ($value) => !is_null($value) && $value !== '');
    }

    private function slaAgeSql(): string
    {
        return 'TIMESTAMPDIFF(HOUR, COALESCE(received_at, created_at), NOW())';
    }
}
