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
            $table->integer('linen_product_id');
            $table->enum('linen_case', ['new', 'edit'])->nullable();
            $table->string('color')->nullable();
            $table->integer('wet_weight')->nullable();
            $table->integer('dry_weight')->nullable();
            $table->integer('iron_piece')->nullable();
            $table->integer('packing_piece')->nullable();
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
        Schema::dropIfExists('operations_linen_products');
    }
};
