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
        Schema::create('job_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('job_id');
            $table->integer('employee_id');
            $table->enum('log_type', ['status', 'washing_machine_id', 'dryer_machine_id', 'laundry_type_id', 'laundry_product_id', 'wet_weight', 'color']);
            $table->string('old_value');
            $table->string('new_value');
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
        Schema::dropIfExists('job_activity_logs');
    }
};
