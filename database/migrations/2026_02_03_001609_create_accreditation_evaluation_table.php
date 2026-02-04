<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accreditation_evaluations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('accred_info_id')
                ->constrained('accreditation_infos')
                ->cascadeOnDelete();

            $table->foreignId('level_id')
                ->constrained('accreditation_levels')
                ->cascadeOnDelete();

            $table->foreignId('program_id')
                ->constrained('programs')
                ->cascadeOnDelete();

            $table->foreignId('evaluated_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique(
                ['accred_info_id', 'program_id', 'level_id'],
                'ae_info_program_level_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accreditation_evaluations');
    }
};
