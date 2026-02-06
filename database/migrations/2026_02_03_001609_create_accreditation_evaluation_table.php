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

            $table->foreignId('area_id')
                ->constrained('areas')
                ->cascadeOnDelete();

            // Who evaluated this area
            $table->foreignId('evaluated_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            // One evaluator can only evaluate the SAME area once
            $table->unique(
                [
                    'accred_info_id',
                    'program_id',
                    'level_id',
                    'area_id',
                    'evaluated_by',
                ],
                'ae_unique_per_area_per_user'
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
