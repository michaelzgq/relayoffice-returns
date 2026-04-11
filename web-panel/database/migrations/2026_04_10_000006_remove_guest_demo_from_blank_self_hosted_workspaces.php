<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (strtolower((string) env('SELF_HOSTED_BOOTSTRAP_MODE', '')) !== 'blank') {
            return;
        }

        DB::table('admins')->where('email', 'guest@dossentry.com')->delete();
        DB::table('admin_roles')->where('id', 4)->delete();
    }

    public function down(): void
    {
        // no-op: guest demo role/account are recreated by the existing demo migration if needed
    }
};
