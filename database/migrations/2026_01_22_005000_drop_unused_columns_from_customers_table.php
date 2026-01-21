<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * ลบ columns ที่ไม่จำเป็นออกจาก customers table
     * เนื่องจากข้อมูลเหล่านี้ควรดึงจาก CustomerOperationDailySummary แทน
     * เพื่อให้ได้ข้อมูล real-time และไม่ซ้ำซ้อน
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'total_wet_weight',
                'total_dry_weight',
                'total_billing_weight',
                'total_edit_weight',
                'total_billing_payment',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->float('total_wet_weight')->default(0);
            $table->float('total_dry_weight')->default(0);
            $table->float('total_billing_weight')->default(0);
            $table->float('total_edit_weight')->default(0);
            $table->float('total_billing_payment')->default(0.0);
        });
    }
};
