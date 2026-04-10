<?php

namespace App\Http\Controllers;

use App\Models\WorkflowReviewRequest;
use App\Services\WorkflowReviewNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WorkflowReviewRequestController extends Controller
{
    public function __construct(private readonly WorkflowReviewNotificationService $notificationService)
    {
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'work_email' => ['required', 'email:rfc', 'max:160'],
            'company_name' => ['required', 'string', 'max:160'],
            'role_title' => ['nullable', 'string', 'max:120'],
            'volume_note' => ['nullable', 'string', 'max:120'],
            'workflow_note' => ['required', 'string', 'max:2000'],
            'website' => ['nullable', 'string', 'max:10'],
        ]);

        if (!empty($validated['website'])) {
            return redirect()->to($request->getSchemeAndHttpHost() . '/#review-request')
                ->with('reviewRequestSubmitted', true);
        }

        unset($validated['website']);

        $reviewRequest = WorkflowReviewRequest::query()->create(array_merge($validated, [
            'submitted_from_host' => $request->getHost(),
            'submitted_from_url' => $request->fullUrl(),
            'status' => 'new',
            'notification_status' => 'pending',
        ]));

        $this->notificationService->send($reviewRequest);

        return redirect()->to($request->getSchemeAndHttpHost() . '/#review-request')
            ->with('reviewRequestSubmitted', true);
    }
}
