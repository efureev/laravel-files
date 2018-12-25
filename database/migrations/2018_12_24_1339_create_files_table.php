<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateFilesTable
 */
class CreateFilesTable extends Migration
{
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable()->index();
            $table->string('path', 2048)->nullable();
            $table->string('ext', 15)->nullable();
            $table->string('driver')->nullable()->index();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('mime')->nullable();
            $table->jsonb('params')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('files');
    }
}
