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
        $response->assertSee('One defensible record for the returns that create arguments.');
        $response->assertSee('Dossentry');
        $response->assertSee('Brand Review Link');
        $response->assertSee('View Sample Brand Review Link');
        $response->assertSee('Shared guest workspace');
        $response->assertSee('ops@admin.com');
    }

    public function test_demo_domain_root_redirects_to_login(): void
    {
        $response = $this->get('http://demo.dossentry.com/');

        $response->assertStatus(302);
        $this->assertStringContainsString('admin/auth/login', $response->headers->get('Location', ''));
    }
}
