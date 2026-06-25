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
        Schema::table('ai_chat_sessions', function (Blueprint $table) {
            $table->string('channel', 16)->nullable()->after('user_id'); // null=web, 'line'=LINE bot
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ai_chat_sessions', function (Blueprint $table) {
            $table->dropColumn('channel');
        });
    }
};
