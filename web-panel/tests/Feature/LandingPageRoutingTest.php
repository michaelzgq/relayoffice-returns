<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsReturnsFixtures;
use Tests\TestCase;

class LandingPageRoutingTest extends TestCase
{
    use BuildsReturnsFixtures;
    use RefreshDatabase;

    public function test_root_domain_serves_marketing_page(): void
    {
        $this->seedWorkspaceSettings();
        $case = $this->createReturnCaseWithDecision([
            'return_id' => 'RMA-1003',
        ]);
        $this->attachEvidence($case, ['front', 'back', 'packaging', 'serial_number']);

        $response = $this->get('http://dossentry.com/');

        $response->assertOk();
        $response->assertSee('Defensible return');
        $response->assertSee('Dossentry');
        $response->assertSee('Brand Review Link');
        $response->assertSee('View Sample Brand Review Link');
        $response->assertSee('Shared guest demo');
        $response->assertSee('guest@dossentry.com');
        $response->assertSee(route('privacy-policy'));
        $response->assertSee(route('terms-of-service'));
        $response->assertSee('data-track-cta="sample_review"', false);
        $response->assertSee(json_encode(route('marketing.click-events.store')), false);
        $response->assertSee('property="og:title"', false);
        $response->assertSee('property="og:description"', false);
        $response->assertSee('property="og:image"', false);
        $response->assertSee('assets/dossentry/og-home.png', false);
        $response->assertSee('name="twitter:card"', false);
    }

    public function test_demo_domain_root_redirects_to_login(): void
    {
        $response = $this->get('http://demo.dossentry.com/');

        $response->assertStatus(302);
        $this->assertStringContainsString('admin/auth/login', $response->headers->get('Location', ''));
    }

    public function test_compare_page_serves_public_comparison_with_tracking_hooks(): void
    {
        $response = $this->get('http://dossentry.com/compare/generic-inspection-apps');

        $response->assertOk();
        $response->assertSee('Generic inspection apps collect photos.');
        $response->assertSee('data-track-cta="back_to_site"', false);
        $response->assertSee(json_encode(route('marketing.click-events.store')), false);
        $response->assertSee('property="og:title"', false);
        $response->assertSee('property="og:description"', false);
        $response->assertSee('property="og:image"', false);
        $response->assertSee('assets/dossentry/og-compare.png', false);
        $response->assertSee('name="twitter:card"', false);
    }
}
