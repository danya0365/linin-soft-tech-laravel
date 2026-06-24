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
        Schema::create('ai_write_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ใครสั่งสร้างผ่าน AI
            $table->unsignedBigInteger('ai_chat_session_id')->nullable();
            $table->string('entity_key', 64); // ชนิดข้อมูล เช่น customer
            $table->unsignedBigInteger('record_id'); // id ของ record ที่ถูกสร้าง
            $table->json('payload'); // snapshot ค่าที่ใช้สร้าง
            $table->timestamp('created_at')->nullable();
            $table->index(['entity_key', 'record_id']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ai_write_audits');
    }
};
