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
        Schema::table('customer_operation_daily_summaries', function (Blueprint $table) {
            $table->decimal('total_edit_weight', 12, 2)->default(0)->after('total_edit_collect_weight')
                ->comment('Manual edit weight from operations.total_edit_weight');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_operation_daily_summaries', function (Blueprint $table) {
            $table->dropColumn('total_edit_weight');
        });
    }
};
