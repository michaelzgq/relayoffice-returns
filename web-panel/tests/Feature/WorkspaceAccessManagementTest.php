<?php

namespace Tests\Feature;

use Database\Seeders\AdminTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsReturnsFixtures;
use Tests\TestCase;

class WorkspaceAccessManagementTest extends TestCase
{
    use BuildsReturnsFixtures;
    use RefreshDatabase;

    public function test_master_admin_can_create_update_and_remove_workspace_accounts(): void
    {
        $this->seedWorkspaceSettings();
        $this->seed(AdminTableSeeder::class);

        $admin = \App\Models\Admin::query()->where('email', 'admin@admin.com')->firstOrFail();

        $createResponse = $this->actingAs($admin, 'admin')->post(route('admin.settings.workspace-access.store'), [
            'workspace_f_name' => 'Taylor',
            'workspace_l_name' => 'Ops',
            'workspace_email' => 'taylor@dockline.example',
            'workspace_role_id' => 2,
            'workspace_password' => 'StrongPass123!',
            'workspace_password_confirmation' => 'StrongPass123!',
        ]);
        $createResponse->assertRedirect();

        $this->assertDatabaseHas('admins', [
            'email' => 'taylor@dockline.example',
            'role_id' => 2,
        ]);

        $managedAccount = \App\Models\Admin::query()->where('email', 'taylor@dockline.example')->firstOrFail();

        $updateResponse = $this->actingAs($admin, 'admin')->post(route('admin.settings.workspace-access.update', $managedAccount->id), [
            'workspace_f_name' => 'Taylor',
            'workspace_l_name' => 'Inspector',
            'workspace_email' => 'taylor@dockline.example',
            'workspace_role_id' => 3,
            'workspace_password' => 'FreshPass123!',
            'workspace_password_confirmation' => 'FreshPass123!',
        ]);
        $updateResponse->assertRedirect();

        $this->assertDatabaseHas('admins', [
            'email' => 'taylor@dockline.example',
            'role_id' => 3,
            'l_name' => 'Inspector',
        ]);

        $deleteResponse = $this->actingAs($admin, 'admin')->post(route('admin.settings.workspace-access.delete', $managedAccount->id));
        $deleteResponse->assertRedirect();

        $this->assertDatabaseMissing('admins', [
            'email' => 'taylor@dockline.example',
        ]);
    }

    public function test_non_master_admin_cannot_manage_workspace_access(): void
    {
        $inspector = $this->signInInspector();

        $response = $this->actingAs($inspector, 'admin')->post(route('admin.settings.workspace-access.store'), [
            'workspace_f_name' => 'Taylor',
            'workspace_l_name' => 'Ops',
            'workspace_email' => 'taylor@dockline.example',
            'workspace_role_id' => 2,
            'workspace_password' => 'StrongPass123!',
            'workspace_password_confirmation' => 'StrongPass123!',
        ]);

        $response->assertRedirect(route('admin.settings'));
        $this->assertDatabaseMissing('admins', [
            'email' => 'taylor@dockline.example',
        ]);
    }
}
