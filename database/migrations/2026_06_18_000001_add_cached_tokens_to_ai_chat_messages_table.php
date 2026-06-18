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
        Schema::table('ai_chat_messages', function (Blueprint $table) {
            // ส่วนของ prompt_tokens ที่เป็น cache hit (คิดเงินที่ราคา cache-read ถูกกว่า)
            $table->unsignedInteger('cached_tokens')->nullable()->after('prompt_tokens');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ai_chat_messages', function (Blueprint $table) {
            $table->dropColumn('cached_tokens');
        });
    }
};
