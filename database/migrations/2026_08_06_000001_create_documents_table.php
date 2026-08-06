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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title', 255);
            $table->string('original_filename', 255);
            $table->string('mime_type', 100);
            $table->string('extension', 20);
            $table->unsignedBigInteger('size_bytes');
            $table->string('storage_path', 500);
            $table->string('kind', 10); // image | pdf | office
            $table->string('status', 10); // pending | ready | failed
            $table->string('fail_reason', 500)->nullable();
            $table->longText('extracted_text')->nullable();
            $table->text('chunks_json')->nullable();
            $table->unsignedInteger('page_count')->nullable();
            $table->unsignedInteger('extracted_chars')->default(0);
            $table->timestamps();
            $table->index(['user_id']);
            $table->index(['status']);
            $table->index(['status', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
