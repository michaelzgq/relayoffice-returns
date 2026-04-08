<?php

namespace Tests\Feature\Returns;

use App\Models\RefundGateDecision;
use App\Models\ReturnCase;
use App\Models\ReturnCaseEvent;
use App\Models\ReturnCaseMedia;
use Database\Seeders\ReturnsDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReturnsDemoResetCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_demo_command_restores_canonical_dataset(): void
    {
        $this->seed(ReturnsDemoSeeder::class);

        $extraCase = ReturnCase::query()->create([
            'return_id' => 'RMA-EXTRA-999',
            'brand_id' => 1,
            'brand_rule_profile_id' => 1,
            'product_sku' => 'EXTRA-SKU',
            'condition_code' => 'like_new',
            'disposition_code' => 'restock',
            'inspection_status' => 'completed',
            'refund_status' => 'hold',
            'required_photo_count' => 1,
            'evidence_photo_count' => 1,
            'evidence_complete' => true,
            'notes' => 'Temporary QA row',
        ]);

        ReturnCaseMedia::query()->create([
            'return_case_id' => $extraCase->id,
            'file_path' => 'extra-proof.jpg',
            'media_type' => 'image',
            'capture_type' => 'front',
            'sort_order' => 1,
        ]);

        ReturnCaseEvent::query()->create([
            'return_case_id' => $extraCase->id,
            'event_type' => 'qa',
            'title' => 'Temporary QA event',
        ]);

        RefundGateDecision::query()->create([
            'return_case_id' => $extraCase->id,
            'status' => 'hold',
            'reason' => 'Temporary QA decision',
        ]);

        Artisan::call('returns:reset-demo', ['--force' => true]);

        $this->assertSame(
            ['RMA-1001', 'RMA-1002', 'RMA-1003', 'RMA-1004', 'RMA-1005'],
            ReturnCase::query()->orderBy('return_id')->pluck('return_id')->all()
        );

        $this->assertDatabaseMissing('return_cases', ['return_id' => 'RMA-EXTRA-999']);
        $this->assertSame(5, ReturnCase::query()->count());
        $this->assertSame(5, RefundGateDecision::query()->count());
        $this->assertTrue(Storage::disk('public')->exists('return-cases/rma-1003-1.png'));
    }
}
