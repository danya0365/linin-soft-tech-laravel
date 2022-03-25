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
            $table->integer('job_group_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('employee_id');
            $table->integer('wash_employee_id')->nullable();
            $table->integer('dry_employee_id')->nullable();
            $table->integer('iron_employee_id')->nullable();
            $table->enum('job_case', ['new', 'edit'])->nullable();
            $table->integer('washing_machine_id')->nullable();
            $table->integer('dryer_machine_id')->nullable();
            $table->integer('linen_type_id')->nullable();
            $table->string('tags')->nullable();
            $table->integer('wet_weight')->nullable();
            $table->string('color')->nullable();
            $table->enum('status', ['wash', 'dry', 'iron']);
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
