<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area_parameter_mappings', function (Blueprint $table) {
            $table->id();

            // Always match parent table PK type (bigint unsigned)
            $table->foreignId('program_area_mapping_id')
                  ->constrained('program_area_mappings')
                  ->onDelete('cascade');

            $table->foreignId('parameter_id')
                  ->constrained('parameters')
                  ->onDelete('cascade');

            // SHORT unique constraint name (avoids MySQL 64-char limit)
            $table->unique(
                ['program_area_mapping_id', 'parameter_id'],
                'area_param_unique'
            );

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_parameter_mappings');
    }
};
