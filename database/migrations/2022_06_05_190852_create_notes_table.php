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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->mediumText('message');
            $table->string('image_url')->nullable();
            $table->decimal('cost', 15, 2)->default(0.00);
            $table->foreignId('washing_machine_id')->nullable()->constrained();
            $table->foreignId('dryer_machine_id')->nullable()->constrained();
            $table->foreignId('truck_id')->nullable()->constrained();
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
        Schema::dropIfExists('notes');
    }
};
