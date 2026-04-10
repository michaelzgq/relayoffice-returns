<?php

namespace App\Services;

use App\Mail\WorkflowReviewRequestSubmitted;
use App\Models\WorkflowReviewRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class WorkflowReviewNotificationService
{
    public function send(WorkflowReviewRequest $reviewRequest): bool
    {
        $notificationEmail = trim((string) config('dossentry.workflow_review_notification_email'));

        if ($notificationEmail === '') {
            $reviewRequest->forceFill([
                'notification_status' => 'failed',
                'notification_attempted_at' => now(),
                'notification_error' => 'Notification email is not configured.',
            ])->save();

            return false;
        }

        try {
            Mail::to($notificationEmail)->send(new WorkflowReviewRequestSubmitted($reviewRequest));

            $reviewRequest->forceFill([
                'notification_status' => 'sent',
                'notification_attempted_at' => now(),
                'notification_sent_at' => now(),
                'notification_error' => null,
            ])->save();

            Log::info('Workflow review request notification email sent', [
                'request_id' => $reviewRequest->id,
                'notification_email' => $notificationEmail,
            ]);

            return true;
        } catch (Throwable $exception) {
            $reviewRequest->forceFill([
                'notification_status' => 'failed',
                'notification_attempted_at' => now(),
                'notification_error' => $exception->getMessage(),
            ])->save();

            Log::warning('Workflow review request notification email failed', [
                'request_id' => $reviewRequest->id,
                'notification_email' => $notificationEmail,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
