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

            $table->addColumn(config('files.table.id'), 'id')->primary();
            $table->addColumn(config('files.table.id'), 'parent_id')->nullable()->index();

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
        Schema::drop(config('files.table.name'));
    }
}
