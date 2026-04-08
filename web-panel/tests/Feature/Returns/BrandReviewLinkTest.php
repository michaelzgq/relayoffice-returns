<?php

namespace Tests\Feature\Returns;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\Concerns\BuildsReturnsFixtures;
use Tests\TestCase;

class BrandReviewLinkTest extends TestCase
{
    use BuildsReturnsFixtures;
    use RefreshDatabase;

    public function test_case_detail_surfaces_signed_brand_review_link_tools(): void
    {
        $admin = $this->signInAdmin();
        $case = $this->createReturnCaseWithDecision([
            'return_id' => 'RMA-SHARE-1001',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.returns.cases.show', [
            'id' => $case->id,
            'share_days' => 30,
        ]));

        $response->assertOk();
        $response->assertSee('Brand Review Link');
        $response->assertSee('signed read-only case record', false);
        $response->assertSee('Link expires in');
    }

    public function test_signed_brand_review_link_is_read_only_and_hides_internal_notes(): void
    {
        $admin = $this->signInAdmin();
        $case = $this->createReturnCaseWithDecision([
            'return_id' => 'RMA-SHARE-1002',
            'created_by' => $admin->id,
            'refund_status' => 'ready_to_release',
        ]);
        $this->attachEvidence($case, ['front', 'back', 'packaging', 'serial_number']);

        $url = URL::temporarySignedRoute('returns.brand-review', now()->addDay(), [
            'id' => $case->id,
        ]);

        $response = $this->get($url);

        $response->assertOk();
        $response->assertSee('Brand Review Record');
        $response->assertSee('Ready for brand review');
        $response->assertDontSee('Fixture note');
        $response->assertDontSee('Fixture decision');
    }

    public function test_invalid_signature_cannot_open_brand_review_link(): void
    {
        $case = $this->createReturnCaseWithDecision([
            'return_id' => 'RMA-SHARE-1003',
        ]);

        $response = $this->get(route('returns.brand-review', ['id' => $case->id]));

        $response->assertForbidden();
    }

    public function test_signed_brand_review_pdf_can_be_downloaded(): void
    {
        $this->signInAdmin();
        $case = $this->createReturnCaseWithDecision([
            'return_id' => 'RMA-SHARE-1004',
        ]);

        $url = URL::temporarySignedRoute('returns.brand-review.pdf', now()->addDay(), [
            'id' => $case->id,
        ]);

        $response = $this->get($url);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }
}
