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
        Schema::create('ai_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ai_chat_session_id');
            $table->string('role', 16); // 'user' | 'assistant'
            $table->longText('content');
            $table->string('model', 100)->nullable(); // แถว assistant: model ที่ใช้จริง
            $table->unsignedInteger('prompt_tokens')->nullable();
            $table->unsignedInteger('completion_tokens')->nullable();
            $table->boolean('is_estimated')->default(false);
            $table->boolean('is_partial')->default(false); // ผู้ใช้กดหยุดกลางทาง
            $table->timestamps();
            $table->index('ai_chat_session_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ai_chat_messages');
    }
};
