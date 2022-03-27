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
        Schema::create('job_groups', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id')->nullable();
            $table->integer('employee_id');
            $table->integer('pickup_employee_id')->nullable();
            $table->integer('packing_employee_id')->nullable();
            $table->integer('collect_employee_id')->nullable();
            $table->float('wet_weight')->default(0.0);
            $table->float('dry_weight')->default(0.0);
            $table->integer('total_pieces')->default(0);
            $table->enum('operation_status', ['pickup', 'progress', 'packing', 'collect', 'close']);
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
        Schema::dropIfExists('job_groups');
    }
};
