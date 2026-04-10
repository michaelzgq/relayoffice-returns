<?php

namespace Tests\Feature;

use App\Models\WorkflowReviewRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsReturnsFixtures;
use Tests\TestCase;

class WorkflowReviewRequestFlowTest extends TestCase
{
    use BuildsReturnsFixtures;
    use RefreshDatabase;

    public function test_public_landing_form_stores_a_workflow_review_request(): void
    {
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
            'submitted_from_host' => 'dossentry.com',
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

        $markResponse = $this->actingAs($admin, 'admin')->post(route('admin.returns.review-requests.mark-reviewed', $request->id));
        $markResponse->assertRedirect(route('admin.returns.review-requests.index'));

        $this->assertDatabaseHas('workflow_review_requests', [
            'id' => $request->id,
            'status' => 'reviewed',
        ]);
    }
}
