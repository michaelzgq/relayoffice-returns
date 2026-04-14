<?php

namespace Tests\Feature\Returns;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsReturnsFixtures;
use Tests\TestCase;

class ReturnsWorkspaceRoleTest extends TestCase
{
    use BuildsReturnsFixtures;
    use RefreshDatabase;

    public function test_dashboard_redirects_inspector_to_inspection_entrypoint(): void
    {
        $inspector = $this->signInInspector();

        $response = $this->actingAs($inspector, 'admin')->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.returns.inspect'));
    }

    public function test_inspector_only_sees_their_own_cases(): void
    {
        $inspector = $this->signInInspector();
        $ownCase = $this->createReturnCaseWithDecision([
            'return_id' => 'RMA-OWN-1001',
            'created_by' => $inspector->id,
        ]);

        $otherCase = $this->createReturnCaseWithDecision([
            'return_id' => 'RMA-OTHER-1002',
            'created_by' => 999,
        ]);

        $listResponse = $this->actingAs($inspector, 'admin')->get(route('admin.returns.cases.index'));
        $listResponse->assertOk();
        $listResponse->assertSee('RMA-OWN-1001');
        $listResponse->assertDontSee('RMA-OTHER-1002');

        $detailResponse = $this->actingAs($inspector, 'admin')->get(route('admin.returns.cases.show', $otherCase->id));
        $detailResponse->assertNotFound();

        $ownDetailResponse = $this->actingAs($inspector, 'admin')->get(route('admin.returns.cases.show', $ownCase->id));
        $ownDetailResponse->assertOk();
        $ownDetailResponse->assertSee('RMA-OWN-1001');
    }

    public function test_inspector_inspection_form_hides_ops_only_fields(): void
    {
        $inspector = $this->signInInspector();
        $this->createBrandWithProfile();

        $response = $this->actingAs($inspector, 'admin')->get(route('admin.returns.inspect'));

        $response->assertOk();
        $response->assertSee('Scan return label');
        $response->assertSee('Works with camera scan, USB/Bluetooth barcode scanners, or manual typing.');
        $response->assertSee('Use camera photo');
        $response->assertSee('html5-qrcode@2.3.8/html5-qrcode.min.js');
        $response->assertSee('Decision state will be set automatically');
        $response->assertDontSee('name="refund_status"', false);
        $response->assertDontSee('name="received_at"', false);
    }

    public function test_guest_demo_redirects_dashboard_to_ops_board(): void
    {
        $guest = $this->signInGuestDemo();

        $response = $this->actingAs($guest, 'admin')->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.returns.dashboard.index'));
    }

    public function test_guest_demo_workspace_hides_admin_controls_and_write_actions(): void
    {
        $guest = $this->signInGuestDemo();
        $case = $this->createReturnCaseWithDecision([
            'return_id' => 'RMA-GUEST-1001',
        ]);
        $this->attachEvidence($case, ['front', 'back', 'packaging', 'serial_number']);

        $dashboardResponse = $this->actingAs($guest, 'admin')->get(route('admin.returns.dashboard.index'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Ops Board');
        $dashboardResponse->assertDontSee('Start inspection');
        $dashboardResponse->assertDontSee('Review Requests');

        $queueResponse = $this->actingAs($guest, 'admin')->get(route('admin.returns.queue.index'));
        $queueResponse->assertOk();
        $queueResponse->assertSee('Shared demo mode');
        $queueResponse->assertDontSee('Bulk action');
        $queueResponse->assertDontSee('Update');

        $rulesResponse = $this->actingAs($guest, 'admin')->get(route('admin.returns.rules.index'));
        $rulesResponse->assertOk();
        $rulesResponse->assertSee('Playbook Snapshot');
        $rulesResponse->assertDontSee('Save playbook');
        $rulesResponse->assertDontSee('Edit');

        $detailResponse = $this->actingAs($guest, 'admin')->get(route('admin.returns.cases.show', $case->id));
        $detailResponse->assertOk();
        $detailResponse->assertSee('Open Brand Defense Pack');
        $detailResponse->assertDontSee('Edit inspection');
        $detailResponse->assertDontSee('Update decision review');
    }

    public function test_guest_demo_cannot_change_decisions_playbooks_or_settings(): void
    {
        $guest = $this->signInGuestDemo();
        $case = $this->createReturnCaseWithDecision([
            'return_id' => 'RMA-GUEST-2001',
            'refund_status' => 'hold',
        ]);

        $decisionResponse = $this->actingAs($guest, 'admin')->post(route('admin.returns.cases.refund-decision', $case->id), [
            'refund_status' => 'ready_to_release',
        ]);
        $decisionResponse->assertRedirect(route('admin.returns.queue.index'));

        $this->assertDatabaseHas('return_cases', [
            'id' => $case->id,
            'refund_status' => 'hold',
        ]);

        $playbookResponse = $this->actingAs($guest, 'admin')->post(route('admin.returns.rules.update', $case->brand_rule_profile_id), [
            'brand_id' => $case->brand_id,
            'profile_name' => 'Guest attempt',
            'required_photo_count' => 4,
            'default_refund_status' => 'hold',
            'allowed_conditions' => ['opened_damaged'],
            'allowed_dispositions' => ['hold'],
            'required_photo_types' => ['front', 'back', 'packaging', 'serial_number'],
            'active' => 1,
        ]);
        $playbookResponse->assertRedirect(route('admin.returns.rules.index'));

        $settingsResponse = $this->actingAs($guest, 'admin')->get(route('admin.settings'));
        $settingsResponse->assertRedirect(route('admin.returns.dashboard.index'));

        $reviewRequestsResponse = $this->actingAs($guest, 'admin')->get(route('admin.returns.review-requests.index'));
        $reviewRequestsResponse->assertForbidden();
    }
}
