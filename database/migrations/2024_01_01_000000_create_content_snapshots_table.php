<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('filament-snapshots.table_name', 'content_snapshots'), function (Blueprint $table) {
            $table->id();
            $table->string('snapshotable_type');
            $table->unsignedBigInteger('snapshotable_id');
            $table->string('heading');
            $table->json('field_data')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['snapshotable_type', 'snapshotable_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('filament-snapshots.table_name', 'content_snapshots'));
    }
};