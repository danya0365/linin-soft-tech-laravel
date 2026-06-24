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
        Schema::create('ai_write_drafts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ai_chat_session_id');
            $table->unsignedBigInteger('user_id'); // ผู้สั่งสร้าง (acting user)
            $table->string('entity_key', 64); // key ใน EntityWriteRegistry เช่น customer, employee
            $table->json('payload'); // attributes ที่ validate + resolve FK แล้ว (insert จากค่านี้เท่านั้น)
            $table->text('preview'); // ข้อความ preview ที่โชว์ให้ผู้ใช้ยืนยัน
            $table->unsignedBigInteger('created_after_message_id')->default(0); // id ข้อความล่าสุดตอน prepare — กัน auto-confirm
            $table->string('status', 16)->default('pending'); // pending | confirmed | expired
            $table->unsignedBigInteger('record_id')->nullable(); // id ของ record ที่สร้างหลัง confirm
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index(['ai_chat_session_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ai_write_drafts');
    }
};
