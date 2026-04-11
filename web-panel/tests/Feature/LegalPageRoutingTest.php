<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsReturnsFixtures;
use Tests\TestCase;

class LegalPageRoutingTest extends TestCase
{
    use BuildsReturnsFixtures;
    use RefreshDatabase;

    public function test_privacy_policy_page_is_publicly_reachable(): void
    {
        $this->seedWorkspaceSettings();

        $response = $this->get(route('privacy-policy'));

        $response->assertOk();
        $response->assertSee('Privacy Policy');
        $response->assertSee('Hosted demo vs customer deployments');
    }

    public function test_terms_page_is_publicly_reachable(): void
    {
        $this->seedWorkspaceSettings();

        $response = $this->get(route('terms-of-service'));

        $response->assertOk();
        $response->assertSee('Terms of Service');
        $response->assertSee('Guest demo rules');
    }
}
