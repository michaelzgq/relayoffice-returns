<?php

namespace Tests\Feature\Returns;

use App\Models\RefundGateDecision;
use App\Models\ReturnCase;
use App\Models\ReturnCaseEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\Concerns\BuildsReturnsFixtures;
use Tests\TestCase;

class ReturnsInspectionFlowTest extends TestCase
{
    use BuildsReturnsFixtures;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        File::ensureDirectoryExists(storage_path('app/public/return-cases'));
        File::cleanDirectory(storage_path('app/public/return-cases'));
    }

    public function test_inspection_blocks_invalid_submission_when_rule_requirements_are_missing(): void
    {
        $admin = $this->signInAdmin();
        $bundle = $this->createBrandWithProfile();
        $brand = $bundle['brand'];

        $response = $this->actingAs($admin, 'admin')->post(route('admin.returns.inspect.store'), [
            'return_id' => 'RMA-9100',
            'brand_id' => $brand->id,
            'product_sku' => 'SKU-9100',
            'condition_code' => 'missing_parts',
            'disposition_code' => 'destroy',
            'refund_status' => 'hold',
            'notes' => '',
        ]);

        $response->assertSessionHasErrors([
            'serial_number',
            'notes',
            'condition_code',
            'disposition_code',
            'photos',
        ]);
    }

    public function test_valid_inspection_creates_case_media_and_refund_decision(): void
    {
        $admin = $this->signInAdmin();
        $bundle = $this->createBrandWithProfile();
        $brand = $bundle['brand'];

        $response = $this->actingAs($admin, 'admin')->post(route('admin.returns.inspect.store'), [
            'return_id' => 'RMA-9101',
            'brand_id' => $brand->id,
            'product_sku' => 'SKU-9101',
            'serial_number' => 'SN-9101',
            'condition_code' => 'opened_damaged',
            'disposition_code' => 'hold',
            'refund_status' => 'hold',
            'received_at' => now()->format('Y-m-d\TH:i'),
            'notes' => 'Dent on speaker grill and seal broken.',
            'photos' => [
                UploadedFile::fake()->image('front.jpg'),
                UploadedFile::fake()->image('back.jpg'),
                UploadedFile::fake()->image('packaging.jpg'),
                UploadedFile::fake()->image('serial.jpg'),
            ],
        ]);

        $response->assertRedirect();

        $case = ReturnCase::query()->where('return_id', 'RMA-9101')->firstOrFail();

        $this->assertSame(4, $case->media()->count());
        $this->assertTrue($case->fresh()->evidence_complete);
        $this->assertDatabaseHas('refund_gate_decisions', [
            'return_case_id' => $case->id,
            'status' => 'hold',
        ]);
        $this->assertDatabaseHas('return_case_events', [
            'return_case_id' => $case->id,
            'event_type' => 'inspection_submitted',
        ]);

        $decision = RefundGateDecision::query()->where('return_case_id', $case->id)->first();
        $event = ReturnCaseEvent::query()->where('return_case_id', $case->id)->latest('id')->first();

        $this->assertSame('Dent on speaker grill and seal broken.', $decision?->reason);
        $this->assertSame('Inspection submitted', $event?->title);
    }

    public function test_inspector_submission_defaults_refund_status_from_playbook_when_ops_field_is_absent(): void
    {
        $inspector = $this->signInInspector();
        $bundle = $this->createBrandWithProfile([], [
            'default_refund_status' => 'needs_review',
            'serial_required' => false,
        ]);
        $brand = $bundle['brand'];

        $response = $this->actingAs($inspector, 'admin')->post(route('admin.returns.inspect.store'), [
            'return_id' => 'RMA-9102',
            'brand_id' => $brand->id,
            'product_sku' => 'SKU-9102',
            'condition_code' => 'opened_damaged',
            'disposition_code' => 'hold',
            'notes' => 'Box crushed on two corners.',
            'photos' => [
                UploadedFile::fake()->image('front.jpg'),
                UploadedFile::fake()->image('back.jpg'),
                UploadedFile::fake()->image('packaging.jpg'),
                UploadedFile::fake()->image('serial.jpg'),
            ],
        ]);

        $response->assertRedirect();

        $case = ReturnCase::query()->where('return_id', 'RMA-9102')->firstOrFail();

        $this->assertSame('needs_review', $case->refund_status);
        $this->assertDatabaseHas('refund_gate_decisions', [
            'return_case_id' => $case->id,
            'status' => 'needs_review',
        ]);
    }

    public function test_inspection_can_default_disposition_from_playbook_recommendation(): void
    {
        $inspector = $this->signInInspector();
        $bundle = $this->createBrandWithProfile([], [
            'serial_required' => false,
            'recommended_dispositions' => [
                'opened_damaged' => 'refurb',
            ],
        ]);
        $brand = $bundle['brand'];

        $response = $this->actingAs($inspector, 'admin')->post(route('admin.returns.inspect.store'), [
            'return_id' => 'RMA-9103',
            'brand_id' => $brand->id,
            'product_sku' => 'SKU-9103',
            'condition_code' => 'opened_damaged',
            'notes' => 'Warehouse followed playbook default action.',
            'photos' => [
                UploadedFile::fake()->image('front.jpg'),
                UploadedFile::fake()->image('back.jpg'),
                UploadedFile::fake()->image('packaging.jpg'),
                UploadedFile::fake()->image('serial.jpg'),
            ],
        ]);

        $response->assertRedirect();

        $case = ReturnCase::query()->where('return_id', 'RMA-9103')->firstOrFail();

        $this->assertSame('refurb', $case->disposition_code);
        $this->assertDatabaseHas('return_case_events', [
            'return_case_id' => $case->id,
            'event_type' => 'inspection_submitted',
        ]);

        $event = ReturnCaseEvent::query()->where('return_case_id', $case->id)->latest('id')->first();
        $this->assertTrue((bool) data_get($event?->meta, 'recommended_disposition_used'));
    }

    public function test_inspection_can_be_saved_as_draft_without_required_evidence(): void
    {
        $admin = $this->signInAdmin();
        $bundle = $this->createBrandWithProfile();
        $brand = $bundle['brand'];

        $response = $this->actingAs($admin, 'admin')->post(route('admin.returns.inspect.store'), [
            'return_id' => 'RMA-DRAFT-9104',
            'brand_id' => $brand->id,
            'save_as_draft' => 1,
            'offline_draft_uuid' => 'draft-9104',
        ]);

        $response->assertRedirect();

        $case = ReturnCase::query()->where('return_id', 'RMA-DRAFT-9104')->firstOrFail();

        $this->assertSame('draft', $case->inspection_status);
        $this->assertSame('draft', $case->sync_status);
        $this->assertFalse($case->evidence_complete);
        $this->assertSame('custom', $case->condition_code);
        $this->assertSame('hold', $case->disposition_code);
        $this->assertDatabaseMissing('refund_gate_decisions', [
            'return_case_id' => $case->id,
        ]);
        $this->assertDatabaseHas('return_case_events', [
            'return_case_id' => $case->id,
            'event_type' => 'inspection_draft_saved',
        ]);
    }
}
