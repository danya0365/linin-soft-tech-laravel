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
        // เพิ่ม service_status ให้ washing_machines
        Schema::table('washing_machines', function (Blueprint $table) {
            $table->enum('service_status', ['available', 'broken'])->default('available')->after('maximum_weight');
        });

        // เพิ่ม service_status ให้ dryer_machines
        Schema::table('dryer_machines', function (Blueprint $table) {
            $table->enum('service_status', ['available', 'broken'])->default('available')->after('maximum_weight');
        });

        // เพิ่ม service_status ให้ trucks
        Schema::table('trucks', function (Blueprint $table) {
            $table->enum('service_status', ['available', 'broken'])->default('available')->after('plate_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('washing_machines', function (Blueprint $table) {
            $table->dropColumn('service_status');
        });

        Schema::table('dryer_machines', function (Blueprint $table) {
            $table->dropColumn('service_status');
        });

        Schema::table('trucks', function (Blueprint $table) {
            $table->dropColumn('service_status');
        });
    }
};
