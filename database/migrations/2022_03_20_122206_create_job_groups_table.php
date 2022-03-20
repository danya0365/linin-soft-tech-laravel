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
            $table->integer('customer_id');
            $table->integer('employee_id');
            $table->float('wet_weight')->default(0.0);
            $table->float('dry_weight')->default(0.0);
            $table->integer('total_pieces')->default(0);
            $table->enum('operation_status', ['progress', 'packing', 'collect']);
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
