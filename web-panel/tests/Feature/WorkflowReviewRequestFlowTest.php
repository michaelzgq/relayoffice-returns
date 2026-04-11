<?php

namespace Tests\Feature;

use App\Models\WorkflowReviewRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Tests\Concerns\BuildsReturnsFixtures;
use Tests\TestCase;

class WorkflowReviewRequestFlowTest extends TestCase
{
    use BuildsReturnsFixtures;
    use RefreshDatabase;

    public function test_public_landing_form_stores_a_workflow_review_request(): void
    {
        Mail::fake();

        $response = $this->post('http://dossentry.com/workflow-review-request', [
            'full_name' => 'Jordan Case',
            'work_email' => 'jordan@dockline.co',
            'company_name' => 'Dockline Fulfillment',
            'role_title' => 'Ops Manager',
            'volume_note' => '100-500',
            'workflow_note' => 'We currently send screenshots and notes back to the brand after inspection.',
            'website' => '',
        ]);

        $response->assertRedirect('http://dossentry.com/#review-request');

        $this->assertDatabaseHas('workflow_review_requests', [
            'full_name' => 'Jordan Case',
            'work_email' => 'jordan@dockline.co',
            'company_name' => 'Dockline Fulfillment',
            'status' => 'new',
            'notification_status' => 'sent',
            'submitted_from_host' => 'dossentry.com',
        ]);

        Mail::assertSent(\App\Mail\WorkflowReviewRequestSubmitted::class, function ($mail) {
            return $mail->hasTo('michael.zgq@gmail.com');
        });
    }

    public function test_public_landing_form_logs_email_failure_but_still_stores_request(): void
    {
        Mail::shouldReceive('to->send')->andThrow(new \RuntimeException('SMTP unavailable'));

        $response = $this->post('http://dossentry.com/workflow-review-request', [
            'full_name' => 'Jordan Case',
            'work_email' => 'jordan@dockline.co',
            'company_name' => 'Dockline Fulfillment',
            'role_title' => 'Ops Manager',
            'volume_note' => '100-500',
            'workflow_note' => 'We currently send screenshots and notes back to the brand after inspection.',
            'website' => '',
        ]);

        $response->assertRedirect('http://dossentry.com/#review-request');

        $this->assertDatabaseHas('workflow_review_requests', [
            'company_name' => 'Dockline Fulfillment',
            'status' => 'new',
            'notification_status' => 'failed',
        ]);
    }

    public function test_admin_can_review_and_mark_requests_complete(): void
    {
        $admin = $this->signInAdmin();

        $request = WorkflowReviewRequest::query()->create([
            'full_name' => 'Jordan Case',
            'work_email' => 'jordan@dockline.co',
            'company_name' => 'Dockline Fulfillment',
            'role_title' => 'Ops Manager',
            'volume_note' => '100-500',
            'workflow_note' => 'We currently send screenshots and notes back to the brand after inspection.',
            'submitted_from_host' => 'dossentry.com',
            'submitted_from_url' => 'https://dossentry.com',
            'status' => 'new',
        ]);

        $listResponse = $this->actingAs($admin, 'admin')->get(route('admin.returns.review-requests.index'));
        $listResponse->assertOk();
        $listResponse->assertSee('Workflow Review Requests');
        $listResponse->assertSee('Dockline Fulfillment');
        $listResponse->assertSee('Resend email');

        $markResponse = $this->actingAs($admin, 'admin')->post(route('admin.returns.review-requests.mark-reviewed', $request->id));
        $markResponse->assertRedirect(route('admin.returns.review-requests.index'));

        $this->assertDatabaseHas('workflow_review_requests', [
            'id' => $request->id,
            'status' => 'reviewed',
        ]);
    }

    public function test_admin_can_resend_workflow_review_notification(): void
    {
        Mail::fake();

        $admin = $this->signInAdmin();

        $request = WorkflowReviewRequest::query()->create([
            'full_name' => 'Jordan Case',
            'work_email' => 'jordan@dockline.co',
            'company_name' => 'Dockline Fulfillment',
            'role_title' => 'Ops Manager',
            'volume_note' => '100-500',
            'workflow_note' => 'We currently send screenshots and notes back to the brand after inspection.',
            'submitted_from_host' => 'dossentry.com',
            'submitted_from_url' => 'https://dossentry.com',
            'status' => 'new',
            'notification_status' => 'pending',
        ]);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.returns.review-requests.resend-notification', $request->id));
        $response->assertRedirect(route('admin.returns.review-requests.index'));

        $this->assertDatabaseHas('workflow_review_requests', [
            'id' => $request->id,
            'notification_status' => 'sent',
        ]);

        Mail::assertSent(\App\Mail\WorkflowReviewRequestSubmitted::class, function ($mail) {
            return $mail->hasTo('michael.zgq@gmail.com');
        });
    }
    public function test_admin_can_view_notification_diagnostics_and_send_a_test_email(): void
    {
        Mail::fake();

        Config::set('dossentry.workflow_review_notification_email', 'michael.zgq@gmail.com');
        Config::set('mail.from.address', 'michael.zgq@gmail.com');

        $admin = $this->signInAdmin();

        $listResponse = $this->actingAs($admin, 'admin')->get(route('admin.returns.review-requests.index'));
        $listResponse->assertOk();
        $listResponse->assertSee('Notification diagnostics');
        $listResponse->assertSee('Send test email');
        $listResponse->assertSee('Gmail self-send detected');

        $sendResponse = $this->actingAs($admin, 'admin')->post(route('admin.returns.review-requests.test-notification'));
        $sendResponse->assertRedirect(route('admin.returns.review-requests.index'));
    }

}

