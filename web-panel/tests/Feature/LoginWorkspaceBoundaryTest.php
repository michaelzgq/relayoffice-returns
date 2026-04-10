<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsReturnsFixtures;
use Tests\TestCase;

class LoginWorkspaceBoundaryTest extends TestCase
{
    use BuildsReturnsFixtures;
    use RefreshDatabase;

    protected function demoHostUrl(string $path): string
    {
        return 'https://demo.dossentry.com' . $path;
    }

    protected function internalHostUrl(string $path): string
    {
        return 'https://internal.dossentry.test' . $path;
    }

    public function test_public_demo_login_page_shows_guest_demo_boundary_copy(): void
    {
        $this->seedWorkspaceSettings();

        $response = $this
            ->get($this->demoHostUrl('/admin/auth/login'));

        $response->assertOk();
        $response->assertSee('Shared guest demo only.');
        $response->assertSee('guest@dossentry.com');
        $response->assertSee('staff login');
    }

    public function test_demo_host_blocks_internal_accounts_from_logging_in(): void
    {
        config()->set('dossentry.internal_admin_login_url', 'https://internal.dossentry.test/admin/auth/login');

        $admin = $this->signInAdmin();
        auth('admin')->logout();

        $response = $this
            ->withSession(['default_captcha_code' => 'ABCD'])
            ->post($this->demoHostUrl('/admin/auth/login'), [
                'email' => $admin->email,
                'password' => 'password',
                'default_captcha_value' => 'ABCD',
            ]);

        $response->assertRedirect($this->internalHostUrl('/admin/auth/login?notice=internal-workspace-only'));
        $this->assertGuest('admin');
    }

    public function test_authenticated_internal_users_are_redirected_off_the_demo_host(): void
    {
        config()->set('dossentry.internal_admin_login_url', 'https://internal.dossentry.test/admin/auth/login');

        $admin = $this->signInAdmin();

        $response = $this
            ->actingAs($admin, 'admin')
            ->get($this->demoHostUrl('/admin'));

        $response->assertRedirect($this->internalHostUrl('/admin/auth/login?notice=internal-workspace-only'));
        $this->assertGuest('admin');
    }

    public function test_authenticated_guest_demo_users_are_redirected_out_of_the_internal_workspace(): void
    {
        config()->set('dossentry.internal_admin_login_url', 'https://internal.dossentry.test/admin/auth/login');

        $guest = $this->signInGuestDemo();

        $response = $this
            ->actingAs($guest, 'admin')
            ->get($this->internalHostUrl('/admin'));

        $response->assertRedirect($this->demoHostUrl('/admin/auth/login?notice=guest-demo-only'));
        $this->assertGuest('admin');
    }
}
