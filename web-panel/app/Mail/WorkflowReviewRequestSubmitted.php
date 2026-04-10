<?php

namespace App\Mail;

use App\Models\WorkflowReviewRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkflowReviewRequestSubmitted extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public WorkflowReviewRequest $requestRecord)
    {
    }

    public function build(): self
    {
        return $this->subject(sprintf(
            'New workflow review request from %s (%s)',
            $this->requestRecord->company_name,
            $this->requestRecord->full_name
        ))
            ->replyTo($this->requestRecord->work_email, $this->requestRecord->full_name)
            ->view('emails.workflow-review-request-submitted');
    }
}
