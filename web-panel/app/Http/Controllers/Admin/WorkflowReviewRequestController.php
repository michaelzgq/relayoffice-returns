<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Models\MarketingClickEvent;
use App\Models\WorkflowReviewRequest;
use App\Services\WorkflowReviewNotificationService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;
use function App\CPU\translate;

class WorkflowReviewRequestController extends Controller
{
    public function __construct(private readonly WorkflowReviewNotificationService $notificationService)
    {
    }

    public function index(Request $request): View
    {
        abort_unless(Helpers::returns_user_can_view_review_requests(), 403);

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

        $clicksLast30Days = MarketingClickEvent::query()
            ->where('created_at', '>=', now()->subDays(30));

        return view('admin-views.returns.review-requests.index', [
            'resources' => $resources,
            'statusCounts' => WorkflowReviewRequest::query()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
            'clickSummary' => [
                'last7Days' => MarketingClickEvent::query()
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count(),
                'last30Days' => (clone $clicksLast30Days)->count(),
                'uniqueClients30Days' => (clone $clicksLast30Days)
                    ->whereNotNull('client_token')
                    ->distinct()
                    ->count('client_token'),
                'ctaCounts30Days' => (clone $clicksLast30Days)
                    ->selectRaw('cta_key, count(*) as total')
                    ->groupBy('cta_key')
                    ->pluck('total', 'cta_key'),
                'topCtas30Days' => (clone $clicksLast30Days)
                    ->selectRaw('page_key, placement, cta_key, count(*) as total')
                    ->groupBy('page_key', 'placement', 'cta_key')
                    ->orderByDesc('total')
                    ->limit(8)
                    ->get(),
                'recentClicks' => MarketingClickEvent::query()
                    ->latest()
                    ->limit(20)
                    ->get(),
            ],
            'mailDiagnostics' => [
                'notificationEmail' => trim((string) config('dossentry.workflow_review_notification_email')),
                'mailFromAddress' => trim((string) config('mail.from.address')),
                'mailMailer' => trim((string) config('mail.default')),
                'sameGmailMailbox' => $this->isSameGmailMailbox(
                    trim((string) config('dossentry.workflow_review_notification_email')),
                    trim((string) config('mail.from.address'))
                ),
            ],
        ]);
    }

    public function markReviewed(Request $request, int $id): RedirectResponse
    {
        abort_unless(Helpers::returns_user_can_view_review_requests(), 403);

        $resource = WorkflowReviewRequest::query()->findOrFail($id);

        $resource->update([
            'status' => 'reviewed',
            'reviewed_at' => now(),
        ]);

        Toastr::success(translate('Workflow review request marked as reviewed'));

        return redirect()->route('admin.returns.review-requests.index', $request->only(['search', 'status', 'page']));
    }

    public function resendNotification(Request $request, int $id): RedirectResponse
    {
        abort_unless(Helpers::returns_user_can_view_review_requests(), 403);

        $resource = WorkflowReviewRequest::query()->findOrFail($id);

        if ($this->notificationService->send($resource)) {
            Toastr::success('Notification email sent');
        } else {
            Toastr::error('Notification email failed. Check the delivery status column for the latest error.');
        }

        return redirect()->route('admin.returns.review-requests.index', $request->only(['search', 'status', 'page']));
    }

    public function sendTestNotification(Request $request): RedirectResponse
    {
        abort_unless(Helpers::returns_user_can_view_review_requests(), 403);

        $notificationEmail = trim((string) config('dossentry.workflow_review_notification_email'));
        $mailFromAddress = trim((string) config('mail.from.address'));
        $mailMailer = trim((string) config('mail.default'));

        if ($notificationEmail === '') {
            Toastr::error('Notification email is not configured.');

            return redirect()->route('admin.returns.review-requests.index', $request->only(['search', 'status', 'page']));
        }

        try {
            Mail::raw(
                implode(PHP_EOL, [
                    'Dossentry workflow notification test',
                    '',
                    'Notification recipient: ' . $notificationEmail,
                    'Mail from address: ' . ($mailFromAddress ?: 'not configured'),
                    'Mailer: ' . ($mailMailer ?: 'not configured'),
                    'Sent at: ' . now()->format('Y-m-d H:i:s T'),
                    '',
                    'If this message does not appear in the inbox, check All Mail, Sent, and Spam.',
                    'When a Gmail account sends to itself, Google may file the message differently than a normal inbound alert.',
                ]),
                function ($message) use ($notificationEmail) {
                    $message->to($notificationEmail)
                        ->subject('Dossentry workflow notification test');
                }
            );

            Log::info('Workflow review notification test email sent', [
                'notification_email' => $notificationEmail,
                'mail_from_address' => $mailFromAddress,
                'mail_mailer' => $mailMailer,
            ]);

            Toastr::success('Test email sent. If it does not appear, check All Mail, Sent, or Spam.');
        } catch (Throwable $exception) {
            Log::warning('Workflow review notification test email failed', [
                'notification_email' => $notificationEmail,
                'mail_from_address' => $mailFromAddress,
                'mail_mailer' => $mailMailer,
                'error' => $exception->getMessage(),
            ]);

            Toastr::error('Test email failed: ' . $exception->getMessage());
        }

        return redirect()->route('admin.returns.review-requests.index', $request->only(['search', 'status', 'page']));
    }

    private function isSameGmailMailbox(string $notificationEmail, string $mailFromAddress): bool
    {
        if ($notificationEmail === '' || $mailFromAddress === '') {
            return false;
        }

        return strcasecmp($notificationEmail, $mailFromAddress) === 0
            && str_ends_with(strtolower($notificationEmail), '@gmail.com');
    }
}
