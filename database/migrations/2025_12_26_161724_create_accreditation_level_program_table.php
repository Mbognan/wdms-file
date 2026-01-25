<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accreditation_level_program', function (Blueprint $table) {
            $table->id();

            $table->foreignId('accreditation_level_id')
                ->constrained('accreditation_levels')
                ->cascadeOnDelete();

            $table->foreignId('program_id')
                ->constrained('programs')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique([
                'accreditation_level_id',
                'program_id'
            ], 'accreditation_level_program_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accreditation_level_program');
    }
};
