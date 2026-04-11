<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bootstrapMode = strtolower((string) env('SELF_HOSTED_BOOTSTRAP_MODE', ''));
        $blankWorkspace = $bootstrapMode === 'blank';
        $selfHostedDemo = $bootstrapMode === 'demo';

        $opsModules = json_encode([
            'returns_inspect_section',
            'returns_cases_section',
            'returns_queue_section',
            'returns_ops_board_section',
            'returns_playbooks_section',
        ]);

        $inspectorModules = json_encode([
            'returns_inspect_section',
            'returns_cases_section',
        ]);

        $guestDemoModules = json_encode([
            'returns_cases_section',
            'returns_queue_section',
            'returns_ops_board_section',
            'returns_playbooks_section',
        ]);

        DB::table('admin_roles')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'Master Admin',
                'modules' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('admin_roles')->updateOrInsert(
            ['id' => 2],
            [
                'name' => 'Ops Manager',
                'modules' => $opsModules,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('admin_roles')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'Inspector',
                'modules' => $inspectorModules,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        if (!$blankWorkspace) {
            DB::table('admin_roles')->updateOrInsert(
                ['id' => 4],
                [
                    'name' => 'Guest Demo',
                    'modules' => $guestDemoModules,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        if ($blankWorkspace) {
            $this->purgeLegacyDemoAccounts();
            $this->seedBlankWorkspaceAdmins();
            return;
        }

        $this->seedDemoWorkspaceAdmins($selfHostedDemo);
    }

    private function seedDemoWorkspaceAdmins(bool $selfHostedDemo): void
    {
        DB::table('admins')->updateOrInsert(
            ['email' => 'admin@admin.com'],
            [
                'id' => 1,
                'f_name' => 'Master Admin',
                'l_name' => 'Khandakar',
                'password' => bcrypt('12345678'),
                'role_id' => 1,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('admins')->updateOrInsert(
            ['email' => 'ops@admin.com'],
            [
                'id' => 2,
                'f_name' => 'Ops',
                'l_name' => 'Manager',
                'password' => bcrypt('12345678'),
                'role_id' => 2,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('admins')->updateOrInsert(
            ['email' => 'inspector@admin.com'],
            [
                'id' => 3,
                'f_name' => 'Dock',
                'l_name' => 'Inspector',
                'password' => bcrypt('12345678'),
                'role_id' => 3,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        if ($selfHostedDemo || env('SELF_HOSTED_BOOTSTRAP_MODE') === false || env('SELF_HOSTED_BOOTSTRAP_MODE') === null) {
            DB::table('admins')->updateOrInsert(
                ['email' => 'guest@dossentry.com'],
                [
                    'id' => 4,
                    'f_name' => 'Guest',
                    'l_name' => 'Demo',
                    'password' => bcrypt('12345678'),
                    'role_id' => 4,
                    'remember_token' => Str::random(10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    private function seedBlankWorkspaceAdmins(): void
    {
        $this->upsertWorkspaceAdmin(
            email: (string) env('SELF_HOSTED_PRIMARY_ADMIN_EMAIL', 'owner@workspace.local'),
            firstName: (string) env('SELF_HOSTED_PRIMARY_ADMIN_FIRST_NAME', 'Workspace'),
            lastName: (string) env('SELF_HOSTED_PRIMARY_ADMIN_LAST_NAME', 'Owner'),
            password: (string) env('SELF_HOSTED_PRIMARY_ADMIN_PASSWORD', 'ChangeThisNow123!'),
            roleId: 1
        );

        $this->upsertOptionalWorkspaceAdmin(
            email: (string) env('SELF_HOSTED_OPS_ADMIN_EMAIL', ''),
            firstName: (string) env('SELF_HOSTED_OPS_ADMIN_FIRST_NAME', 'Ops'),
            lastName: (string) env('SELF_HOSTED_OPS_ADMIN_LAST_NAME', 'Manager'),
            password: (string) env('SELF_HOSTED_OPS_ADMIN_PASSWORD', 'ChangeThisNow123!'),
            roleId: 2
        );

        $this->upsertOptionalWorkspaceAdmin(
            email: (string) env('SELF_HOSTED_INSPECTOR_EMAIL', ''),
            firstName: (string) env('SELF_HOSTED_INSPECTOR_FIRST_NAME', 'Dock'),
            lastName: (string) env('SELF_HOSTED_INSPECTOR_LAST_NAME', 'Inspector'),
            password: (string) env('SELF_HOSTED_INSPECTOR_PASSWORD', 'ChangeThisNow123!'),
            roleId: 3
        );
    }

    private function upsertOptionalWorkspaceAdmin(string $email, string $firstName, string $lastName, string $password, int $roleId): void
    {
        if (trim($email) === '') {
            return;
        }

        $this->upsertWorkspaceAdmin($email, $firstName, $lastName, $password, $roleId);
    }

    private function upsertWorkspaceAdmin(string $email, string $firstName, string $lastName, string $password, int $roleId): void
    {
        DB::table('admins')->updateOrInsert(
            ['email' => trim(strtolower($email))],
            [
                'f_name' => trim($firstName) !== '' ? trim($firstName) : 'Workspace',
                'l_name' => trim($lastName) !== '' ? trim($lastName) : 'User',
                'password' => bcrypt($password),
                'role_id' => $roleId,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function purgeLegacyDemoAccounts(): void
    {
        DB::table('admins')->whereIn('email', [
            'admin@admin.com',
            'ops@admin.com',
            'inspector@admin.com',
            'guest@dossentry.com',
        ])->delete();

        DB::table('admin_roles')->where('id', 4)->delete();
    }
}
