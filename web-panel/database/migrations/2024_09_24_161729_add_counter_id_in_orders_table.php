<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->bigInteger('counter_id')->nullable();
            $table->string('card_number')->nullable();
            $table->string('comment')->nullable();
            $table->string('email_or_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('counter_id');
            $table->dropColumn('card_number');
            $table->dropColumn('comment');
            $table->dropColumn('email_or_phone');
        });
    }
};
