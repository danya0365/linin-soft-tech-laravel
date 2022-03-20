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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // (ซัก, อบ, รีด, พับแพ็ค, จัดเก็บ)
            $table->enum('input_unit', ['weight', 'piece']); //หน่วยในการจัดเก็บข้อมูลที่แสดง ในหน้าสถิติพนักงาน
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departments');
    }
};
