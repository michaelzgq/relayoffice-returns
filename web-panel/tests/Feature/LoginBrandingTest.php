<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsReturnsFixtures;
use Tests\TestCase;

class LoginBrandingTest extends TestCase
{
    use BuildsReturnsFixtures;
    use RefreshDatabase;

    public function test_login_page_uses_dossentry_branding(): void
    {
        $this->seedWorkspaceSettings();

        $response = $this->get(route('admin.auth.login'));

        $response->assertOk();
        $response->assertSee('Dossentry');
        $response->assertSee('Brand-ready return evidence and decision workflows.');
        $response->assertSee(route('privacy-policy'));
        $response->assertSee(route('terms-of-service'));
        $response->assertDontSee('The Ultimate');
        $response->assertDontSee('POS Solution');
    }
}
