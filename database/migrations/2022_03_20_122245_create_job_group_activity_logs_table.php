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
        Schema::create('job_group_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('job_group_id');
            $table->integer('employee_id');
            $table->enum('log_type', ['status', 'dry_weight', 'total_pieces']);
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
        Schema::dropIfExists('job_group_activity_logs');
    }
};
