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
        Schema::create('customer_operation_daily_summaries', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->date('operation_date');
            $table->float('total_wet_weight')->default(0.0);
            $table->float('total_dry_weight')->default(0.0);
            $table->integer('total_iron_piece')->default(0);
            $table->integer('total_packing_piece')->default(0);
            $table->float('total_edit_collect_weight')->default(0.0);
            $table->float('total_collect_weight')->default(0.0);
            $table->integer('total_collect_pack')->default(0);
            $table->integer('total_delivery_pack')->default(0);
            $table->float('total_billing_weight')->default(0.0);
            $table->float('total_billing_payment')->default(0.0);
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
        Schema::dropIfExists('customer_operation_daily_summaries');
    }
};