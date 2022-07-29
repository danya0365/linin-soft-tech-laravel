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
        Schema::create('operations_linen_products', function (Blueprint $table) {
            $table->id();
            $table->integer('operation_id');
            $table->integer('linen_product_id')->nullable();
            $table->enum('linen_case', ['new', 'edit'])->nullable();
            $table->string('color')->nullable();
            $table->float('wet_weight')->nullable();
            $table->float('dry_weight')->nullable();
            $table->integer('iron_piece')->nullable();
            $table->integer('packing_piece')->nullable();
            $table->float('collect_weight')->nullable();
            $table->integer('collect_pack')->nullable();
            $table->integer('deliver_pack')->nullable();
            $table->integer('deliver_operation_id')->nullable();
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
        Schema::dropIfExists('operations_linen_products');
    }
};
