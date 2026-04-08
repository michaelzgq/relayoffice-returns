<?php

namespace Tests\Concerns;

use App\Models\Admin;
use App\Models\AdminRole;
use App\Models\Brand;
use App\Models\BrandRuleProfile;
use App\Models\RefundGateDecision;
use App\Models\ReturnCase;
use App\Models\ReturnCaseEvent;
use App\Models\ReturnCaseMedia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait BuildsReturnsFixtures
{
    protected function signInAdmin(): Admin
    {
        $this->seedWorkspaceSettings();

        $role = AdminRole::query()->forceCreate([
            'id' => 1,
            'name' => 'Master Admin',
            'modules' => null,
            'status' => 1,
        ]);

        $admin = Admin::query()->forceCreate([
            'f_name' => 'Test',
            'l_name' => 'Admin',
            'email' => 'admin+' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);

        $admin->forceFill(['role_id' => $role->id])->save();

        $this->actingAs($admin, 'admin');

        return $admin;
    }

    protected function signInInspector(): Admin
    {
        $this->seedWorkspaceSettings();

        $role = AdminRole::query()->forceCreate([
            'id' => 2,
            'name' => 'Inspector',
            'modules' => json_encode([
                'returns_inspect_section',
                'returns_cases_section',
            ]),
            'status' => 1,
        ]);

        $admin = Admin::query()->forceCreate([
            'f_name' => 'Dock',
            'l_name' => 'Inspector',
            'email' => 'inspector+' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);

        $admin->forceFill(['role_id' => $role->id])->save();

        $this->actingAs($admin, 'admin');

        return $admin;
    }

    protected function seedWorkspaceSettings(): void
    {
        DB::table('business_settings')->updateOrInsert(
            ['key' => 'pagination_limit'],
            ['value' => '15']
        );

        DB::table('business_settings')->updateOrInsert(
            ['key' => 'shop_logo'],
            ['value' => '']
        );

        DB::table('business_settings')->updateOrInsert(
            ['key' => 'shop_name'],
            ['value' => '6POS Returns Demo']
        );

        DB::table('business_settings')->updateOrInsert(
            ['key' => 'footer_text'],
            ['value' => 'Returns workspace']
        );
    }

    protected function createBrandWithProfile(array $brandOverrides = [], array $profileOverrides = []): array
    {
        $brand = Brand::query()->create(array_merge([
            'name' => 'Peak Audio ' . uniqid(),
            'description' => 'Serialized electronics test brand.',
            'image' => null,
            'status' => 1,
        ], $brandOverrides));

        $profile = BrandRuleProfile::query()->create(array_merge([
            'brand_id' => $brand->id,
            'profile_name' => 'Electronics inspection lane',
            'allowed_conditions' => ['unopened', 'opened_resaleable', 'opened_damaged', 'wrong_item', 'empty_box'],
            'allowed_dispositions' => ['restock', 'hold', 'refurb', 'return_to_brand', 'quarantine'],
            'recommended_dispositions' => [
                'unopened' => 'restock',
                'opened_resaleable' => 'restock',
                'opened_damaged' => 'refurb',
                'wrong_item' => 'return_to_brand',
                'empty_box' => 'quarantine',
            ],
            'required_photo_types' => ['front', 'back', 'packaging', 'serial_number'],
            'required_photo_count' => 4,
            'notes_required' => true,
            'sku_required' => true,
            'serial_required' => true,
            'default_refund_status' => 'hold',
            'active' => true,
        ], $profileOverrides));

        return compact('brand', 'profile');
    }

    protected function createReturnCaseWithDecision(array $overrides = []): ReturnCase
    {
        $bundle = $this->createBrandWithProfile();
        $brand = $overrides['brand'] ?? $bundle['brand'];
        $profile = $overrides['profile'] ?? $bundle['profile'];
        unset($overrides['brand'], $overrides['profile']);

        $case = ReturnCase::query()->create(array_merge([
            'return_id' => 'RMA-' . random_int(1000, 9999),
            'brand_id' => $brand->id,
            'brand_rule_profile_id' => $profile->id,
            'product_sku' => 'SKU-' . random_int(100, 999),
            'serial_number' => 'SN-' . random_int(100, 999),
            'condition_code' => 'opened_damaged',
            'disposition_code' => 'hold',
            'inspection_status' => 'completed',
            'refund_status' => 'hold',
            'required_photo_count' => 4,
            'evidence_photo_count' => 4,
            'evidence_complete' => true,
            'sla_hours' => 24,
            'notes' => 'Fixture note',
            'received_at' => Carbon::now()->subHours(30),
            'inspected_at' => Carbon::now()->subHours(28),
            'created_by' => 1,
        ], $overrides));

        RefundGateDecision::query()->create([
            'return_case_id' => $case->id,
            'status' => $case->refund_status,
            'reason' => 'Fixture decision',
            'decided_by' => 1,
            'decided_at' => Carbon::now()->subHours(26),
        ]);

        ReturnCaseEvent::query()->create([
            'return_case_id' => $case->id,
            'event_type' => 'fixture_created',
            'title' => 'Fixture created',
            'description' => 'Case created for test coverage.',
            'created_by' => 1,
        ]);

        return $case;
    }

    protected function attachEvidence(ReturnCase $case, array $types): void
    {
        foreach (array_values($types) as $index => $type) {
            ReturnCaseMedia::query()->create([
                'return_case_id' => $case->id,
                'file_path' => sprintf('%s-%d.jpg', strtolower($case->return_id), $index + 1),
                'media_type' => 'image',
                'capture_type' => $type,
                'sort_order' => $index + 1,
                'uploaded_by' => 1,
            ]);
        }

        $case->forceFill([
            'evidence_photo_count' => count($types),
            'evidence_complete' => count($types) >= ($case->required_photo_count ?? 0),
        ])->save();
    }
}
