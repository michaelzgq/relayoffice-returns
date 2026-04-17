<?php

namespace Tests\Feature;

use Database\Seeders\AdminTableSeeder;
use Database\Seeders\DemoBootstrapSeeder;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SelfHostedBootstrapTest extends TestCase
{
    protected function tearDown(): void
    {
        foreach ([
            'SELF_HOSTED_BOOTSTRAP_MODE',
            'SELF_HOSTED_PRIMARY_ADMIN_FIRST_NAME',
            'SELF_HOSTED_PRIMARY_ADMIN_LAST_NAME',
            'SELF_HOSTED_PRIMARY_ADMIN_EMAIL',
            'SELF_HOSTED_PRIMARY_ADMIN_PASSWORD',
            'SELF_HOSTED_OPS_ADMIN_EMAIL',
            'SELF_HOSTED_OPS_ADMIN_PASSWORD',
            'SELF_HOSTED_INSPECTOR_EMAIL',
            'SELF_HOSTED_INSPECTOR_PASSWORD',
            'BUYER_USERNAME',
            'PURCHASE_CODE',
            'SOFTWARE_ID',
        ] as $variable) {
            putenv($variable);
            unset($_ENV[$variable], $_SERVER[$variable]);
        }

        parent::tearDown();
    }

    public function test_blank_self_hosted_bootstrap_uses_customer_owned_accounts_and_skips_demo_users(): void
    {
        putenv('SELF_HOSTED_BOOTSTRAP_MODE=blank');
        putenv('SELF_HOSTED_PRIMARY_ADMIN_FIRST_NAME=Avery');
        putenv('SELF_HOSTED_PRIMARY_ADMIN_LAST_NAME=Owner');
        putenv('SELF_HOSTED_PRIMARY_ADMIN_EMAIL=owner@dockline.example');
        putenv('SELF_HOSTED_PRIMARY_ADMIN_PASSWORD=SuperSecure123!');
        putenv('SELF_HOSTED_OPS_ADMIN_EMAIL=ops@dockline.example');
        putenv('SELF_HOSTED_OPS_ADMIN_PASSWORD=OpsSecure123!');
        putenv('SELF_HOSTED_INSPECTOR_EMAIL=');
        putenv('SELF_HOSTED_INSPECTOR_PASSWORD=');

        Artisan::call('migrate:fresh', ['--force' => true]);
        $this->seed(AdminTableSeeder::class);
        $this->seed(DemoBootstrapSeeder::class);

        $this->assertDatabaseHas('admins', [
            'email' => 'owner@dockline.example',
            'role_id' => 1,
        ]);

        $this->assertDatabaseHas('admins', [
            'email' => 'ops@dockline.example',
            'role_id' => 2,
        ]);

        $this->assertDatabaseMissing('admins', ['email' => 'admin@admin.com']);
        $this->assertDatabaseMissing('admins', ['email' => 'ops@admin.com']);
        $this->assertDatabaseMissing('admins', ['email' => 'inspector@admin.com']);
        $this->assertDatabaseMissing('admins', ['email' => 'guest@dossentry.com']);
        $this->assertDatabaseMissing('admin_roles', ['id' => 4]);
    }

    public function test_blank_self_hosted_login_page_renders_without_license_envs(): void
    {
        putenv('SELF_HOSTED_BOOTSTRAP_MODE=blank');
        putenv('SELF_HOSTED_PRIMARY_ADMIN_FIRST_NAME=Avery');
        putenv('SELF_HOSTED_PRIMARY_ADMIN_LAST_NAME=Owner');
        putenv('SELF_HOSTED_PRIMARY_ADMIN_EMAIL=owner@dockline.example');
        putenv('SELF_HOSTED_PRIMARY_ADMIN_PASSWORD=SuperSecure123!');
        putenv('BUYER_USERNAME');
        putenv('PURCHASE_CODE');
        putenv('SOFTWARE_ID');

        Artisan::call('migrate:fresh', ['--force' => true]);
        $this->seed(AdminTableSeeder::class);
        $this->seed(DemoBootstrapSeeder::class);

        $response = $this->get('/admin/auth/login');

        $response->assertOk();
        $response->assertSee('Enter the workspace');
        $response->assertSee('Workspace Login');
    }
}
