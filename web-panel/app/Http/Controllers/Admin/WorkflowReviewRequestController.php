<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkflowReviewRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use function App\CPU\translate;

class WorkflowReviewRequestController extends Controller
{
    public function index(Request $request): View
    {
        $resources = WorkflowReviewRequest::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = (string) $request->input('search');

                $query->where(function ($nested) use ($search) {
                    $nested->where('full_name', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhere('work_email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->input('status')))
            ->latest()
            ->paginate(\App\CPU\Helpers::pagination_limit())
            ->appends($request->query());

        return view('admin-views.returns.review-requests.index', [
            'resources' => $resources,
            'statusCounts' => WorkflowReviewRequest::query()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
        ]);
    }

    public function markReviewed(Request $request, int $id): RedirectResponse
    {
        $resource = WorkflowReviewRequest::query()->findOrFail($id);

        $resource->update([
            'status' => 'reviewed',
            'reviewed_at' => now(),
        ]);

        Toastr::success(translate('Workflow review request marked as reviewed'));

        return redirect()->route('admin.returns.review-requests.index', $request->only(['search', 'status', 'page']));
    }
}
