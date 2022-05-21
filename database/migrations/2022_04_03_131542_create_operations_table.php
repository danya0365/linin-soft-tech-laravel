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
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->enum('operation_type', OperationType::getValues());
            $table->integer('employee_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('wash_employee_id')->nullable();
            $table->integer('dry_employee_id')->nullable();
            $table->integer('iron_employee_id')->nullable();
            $table->integer('packing_employee_id')->nullable();
            $table->integer('collect_employee_id')->nullable();
            $table->integer('deliver_employee_id')->nullable();
            $table->integer('washing_machine_id')->nullable();
            $table->integer('dryer_machine_id')->nullable();
            $table->integer('truck_id')->nullable();
            $table->float('total_wet_weight')->nullable();
            $table->float('total_dry_weight')->nullable();
            $table->integer('total_iron_piece')->nullable();
            $table->integer('total_packing_piece')->nullable();
            $table->float('total_collect_weight')->nullable();
            $table->integer('total_collect_pack')->nullable();
            $table->integer('total_deliver_pack')->nullable();
            $table->float('total_billing_weight')->nullable();
            $table->float('total_billing_payment')->nullable();
            $table->string('colors')->nullable();
            $table->string('search_tags')->nullable();
            $table->enum('status', ['in-progress', 'close']);
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
        Schema::dropIfExists('operations');
    }
};