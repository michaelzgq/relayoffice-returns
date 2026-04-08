<?php

namespace Tests\Feature\Returns;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsReturnsFixtures;
use Tests\TestCase;

class BrandDefensePackExportTest extends TestCase
{
    use BuildsReturnsFixtures;
    use RefreshDatabase;

    public function test_brand_defense_pack_preview_contains_new_summary_sections(): void
    {
        $admin = $this->signInAdmin();
        $case = $this->createReturnCaseWithDecision([
            'return_id' => 'RMA-BRAND-9001',
            'created_by' => $admin->id,
            'refund_status' => 'ready_to_release',
        ]);
        $this->attachEvidence($case, ['front', 'back', 'packaging', 'serial_number']);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.returns.cases.export', $case->id));

        $response->assertOk();
        $response->assertSee('Brand Defense Pack');
        $response->assertSee('Executive Summary');
        $response->assertSee('What This Pack Shows');
        $response->assertSee('Rule Coverage');
    }

    public function test_brand_defense_pack_can_be_downloaded_as_pdf(): void
    {
        $admin = $this->signInAdmin();
        $case = $this->createReturnCaseWithDecision([
            'return_id' => 'RMA-BRAND-9002',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.returns.cases.export', [
            'id' => $case->id,
            'download' => 'pdf',
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_inspector_cannot_export_another_users_case_pack(): void
    {
        $inspector = $this->signInInspector();
        $otherCase = $this->createReturnCaseWithDecision([
            'return_id' => 'RMA-BRAND-9003',
            'created_by' => 999,
        ]);

        $response = $this->actingAs($inspector, 'admin')->get(route('admin.returns.cases.export', $otherCase->id));

        $response->assertNotFound();
    }
}
