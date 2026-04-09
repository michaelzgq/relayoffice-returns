<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingPageRoutingTest extends TestCase
{
    public function test_root_domain_serves_marketing_page(): void
    {
        $response = $this->get('http://dossentry.com/');

        $response->assertOk();
        $response->assertSee('One defensible record for the returns that create arguments.');
        $response->assertSee('Dossentry');
        $response->assertSee('Brand Review Link');
    }

    public function test_demo_domain_root_redirects_to_login(): void
    {
        $response = $this->get('http://demo.dossentry.com/');

        $response->assertStatus(302);
        $this->assertStringContainsString('admin/auth/login', $response->headers->get('Location', ''));
    }
}
