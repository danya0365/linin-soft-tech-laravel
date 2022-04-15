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
        Schema::create('energy_resource_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id')->nullable();
            $table->integer('energy_resource_id')->nullable();
            $table->date('log_date')->nullable();
            $table->integer('value')->nullable();
            $table->string('unit')->nullable();
            $table->string('lot_number')->nullable();
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
        Schema::dropIfExists('energy_resource_logs');
    }
};