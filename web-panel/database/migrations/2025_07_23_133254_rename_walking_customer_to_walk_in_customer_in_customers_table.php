<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{

    public function up(): void
    {
        DB::table('customers')->where('id', 0)->update(['name' => 'Walk-In Customer']);
    }


    public function down(): void
    {
        DB::table('customers')->where('id', 0)->update(['name' => 'walking customer']);
    }
};
