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
        Schema::table('ai_write_drafts', function (Blueprint $table) {
            $table->string('action', 16)->default('create')->after('entity_key'); // create | update | delete
        });

        Schema::table('ai_write_audits', function (Blueprint $table) {
            $table->string('action', 16)->default('create')->after('entity_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ai_write_drafts', function (Blueprint $table) {
            $table->dropColumn('action');
        });

        Schema::table('ai_write_audits', function (Blueprint $table) {
            $table->dropColumn('action');
        });
    }
};
