<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_area_mappings', function (Blueprint $table) {
            $table->id();

            // Correct FK columns
            $table->foreignId('info_level_program_mapping_id')
                  ->constrained('info_level_program_mappings')
                  ->onDelete('cascade');

            $table->foreignId('area_id')
                  ->constrained('areas')
                  ->onDelete('cascade');

            // Short unique index name (avoids MySQL 64-char limit)
            $table->unique(
                ['info_level_program_mapping_id', 'area_id'],
                'prg_area_map_unique'
            );

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_area_mappings');
    }
};
