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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_can_access_admin')->default(0)->after('role');
            $table->boolean('is_can_access_manager')->default(0)->after('is_can_access_admin');
            $table->boolean('is_can_access_supervisor')->default(0)->after('is_can_access_manager');
            $table->boolean('is_can_access_customer')->default(0)->after('is_can_access_supervisor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_can_access_admin');
            $table->dropColumn('is_can_access_supervisor');
            $table->dropColumn('is_can_access_customer');
        });
    }
};
