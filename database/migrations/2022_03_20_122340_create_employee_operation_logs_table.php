<?php

use App\Enums\OperationType;
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
        Schema::create('employee_operation_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id');
            $table->enum('operation_type', OperationType::getValues());
            $table->enum('action_type', ['start', 'progress', 'stop']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_operation_logs');
    }
};
