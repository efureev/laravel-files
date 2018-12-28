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
        Schema::create(config('files.table.name'), function (Blueprint $table): void {
            $typeId = config('files.table.id');

            if (!in_array($typeId, ['integer', 'string', 'uuid', 'unsignedBigInteger', 'unsignedInteger'])) {
                throw new \TypeError('id type must have be: \'integer\', \'string\', \'uuid\',\'unsignedBigInteger\',\'unsignedInteger\'');
            }

            switch ($typeId) {
                case 'uuid':
                    $table->uuid('id')->primary();
                    $table->uuid('parent_id')->nullable()->index();
                    break;
                case 'string':
                    $table->string('id')->primary();
                    $table->string('parent_id')->nullable()->index();
                    break;
                case 'integer':
                    $table->integer('id', true)->primary();
                    $table->integer('parent_id')->nullable()->index();
                    break;
                case 'unsignedInteger':
                    $table->unsignedInteger('id', true)->primary();
                    $table->unsignedInteger('parent_id')->nullable()->index();
                    break;
                case 'unsignedBigInteger':
                    $table->unsignedBigInteger('id', true)->primary();
                    $table->unsignedBigInteger('parent_id')->nullable()->index();
                    break;
            }

            $table->string('path', 2048)->nullable();
            $table->string('ext', 15)->nullable();
            $table->string('driver')->nullable()->index();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('mime')->nullable();
            $table->string('key', 191)->nullable()->index();
            $table->jsonb('params')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop(config('files.table.name'));
    }
}
