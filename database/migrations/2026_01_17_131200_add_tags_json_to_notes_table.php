<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration:
     * 1. Adds a JSON 'tags' column to support multiple tags per note
     * 2. Migrates existing data from the old 'tag' column to the new 'tags' column
     * 3. Drops the old 'tag' column
     *
     * @return void
     */
    public function up()
    {
        // Step 1: Add new JSON column for multiple tags
        Schema::table('notes', function (Blueprint $table) {
            $table->json('tags')->nullable()->after('cost')->comment('Array of tags for categorizing notes');
        });

        // Step 2: Migrate existing single tag data to new tags array format
        DB::statement("UPDATE notes SET tags = JSON_ARRAY(tag) WHERE tag IS NOT NULL AND tag != ''");

        // Step 3: Drop the old 'tag' column
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn('tag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Step 1: Re-add the old 'tag' column
        Schema::table('notes', function (Blueprint $table) {
            $table->string('tag')->nullable()->after('cost');
        });

        // Step 2: Migrate first tag back to single tag column
        DB::statement("UPDATE notes SET tag = JSON_UNQUOTE(JSON_EXTRACT(tags, '$[0]')) WHERE tags IS NOT NULL");

        // Step 3: Drop the 'tags' column
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn('tags');
        });
    }
};

