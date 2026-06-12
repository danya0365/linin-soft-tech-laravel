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
        Schema::create('ai_credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount', 12, 4); // signed: topup +, usage - (= -(cost+commission)), adjust ±
            $table->decimal('balance_after', 12, 4);
            $table->string('type', 16); // topup | usage | adjust
            $table->decimal('cost_thb', 12, 4)->nullable(); // usage: ต้นทุนจริง (ไม่รวมค่าคอม)
            $table->decimal('commission_thb', 12, 4)->nullable(); // usage: ค่าคอม — |amount| = cost + commission
            $table->unsignedBigInteger('ai_chat_message_id')->nullable()->unique(); // กันหักซ้ำต่อข้อความ
            $table->unsignedBigInteger('created_by')->nullable(); // admin (null = ระบบหักเอง)
            $table->string('note', 255)->nullable();
            $table->timestamps();
            $table->index(['user_id', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ai_credit_transactions');
    }
};
