<?php

namespace Tests\Feature\Returns;

use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsReturnsFixtures;
use Tests\TestCase;

class ReturnsRuleProfileValidationTest extends TestCase
{
    use BuildsReturnsFixtures;
    use RefreshDatabase;

    public function test_rule_profile_rejects_photo_count_above_selected_template_slots(): void
    {
        $admin = $this->signInAdmin();
        $bundle = $this->createBrandWithProfile();
        $brand = $bundle['brand'];

        $response = $this->actingAs($admin, 'admin')->post(route('admin.returns.rules.store'), [
            'brand_id' => $brand->id,
            'profile_name' => 'Broken profile',
            'allowed_conditions' => ['like_new'],
            'allowed_dispositions' => ['hold'],
            'required_photo_types' => ['front', 'back'],
            'required_photo_count' => 3,
            'default_refund_status' => 'hold',
            'notes_required' => 1,
            'sku_required' => 1,
            'serial_required' => 0,
            'active' => 1,
        ]);

        $response->assertSessionHasErrors('required_photo_count');
    }

    public function test_rule_profile_rejects_recommended_action_outside_allowed_dispositions(): void
    {
        $admin = $this->signInAdmin();
        $brand = Brand::query()->create([
            'name' => 'Luma QA ' . uniqid(),
            'description' => 'Validation target brand.',
            'image' => null,
            'status' => 1,
        ]);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.returns.rules.store'), [
            'brand_id' => $brand->id,
            'profile_name' => 'Broken mapping',
            'allowed_conditions' => ['like_new'],
            'allowed_dispositions' => ['hold'],
            'recommended_dispositions' => [
                'like_new' => 'restock',
            ],
            'required_photo_types' => ['front'],
            'required_photo_count' => 1,
            'default_refund_status' => 'hold',
            'notes_required' => 1,
            'sku_required' => 1,
            'serial_required' => 0,
            'active' => 1,
        ]);

        $response->assertSessionHasErrors('recommended_dispositions.like_new');
    }
}
