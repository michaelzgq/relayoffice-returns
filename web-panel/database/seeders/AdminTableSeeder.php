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
    }
}
