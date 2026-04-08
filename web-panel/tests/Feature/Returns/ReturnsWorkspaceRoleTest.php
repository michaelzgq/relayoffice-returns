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
        $response->assertSee('Refund status will be set automatically');
        $response->assertDontSee('name="refund_status"', false);
        $response->assertDontSee('name="received_at"', false);
    }
}
