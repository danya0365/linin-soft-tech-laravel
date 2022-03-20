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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->integer('job_group_id');
            $table->integer('customer_id');
            $table->integer('employee_id');
            $table->enum('job_type', ['new', 'edit']);
            $table->integer('washing_machine_id')->nullable();
            $table->integer('dryer_machine_id')->nullable();
            $table->integer('laundry_type_id')->nullable();
            $table->integer('wet_weight')->nullable();
            $table->string('color')->nullable();
            $table->string('tags')->nullable();
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
        Schema::dropIfExists('jobs');
    }
};
