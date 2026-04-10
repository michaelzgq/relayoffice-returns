<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $modules = json_encode([
            'returns_cases_section',
            'returns_queue_section',
            'returns_ops_board_section',
            'returns_playbooks_section',
        ]);

        DB::table('admin_roles')->updateOrInsert(
            ['id' => 4],
            [
                'name' => 'Guest Demo',
                'modules' => $modules,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

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

    public function down(): void
    {
        DB::table('admins')->where('email', 'guest@dossentry.com')->delete();
        DB::table('admin_roles')->where('id', 4)->delete();
    }
};
