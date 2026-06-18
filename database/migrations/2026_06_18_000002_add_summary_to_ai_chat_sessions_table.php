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
            // บทสรุปข้อความเก่า (rolling summary) — แทนการส่งข้อความเก่าทั้งหมดทุกเทิร์น
            $table->longText('summary')->nullable()->after('model');
            // id ข้อความสุดท้ายที่ถูกย่อเข้า summary แล้ว (ข้อความหลัง id นี้ = recent window)
            $table->unsignedBigInteger('summary_until_message_id')->nullable()->after('summary');
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
            $table->dropColumn(['summary', 'summary_until_message_id']);
        });
    }
};
