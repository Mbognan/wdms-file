<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accreditation_info_level', function (Blueprint $table) {
            $table->id();

            $table->foreignId('accreditation_info_id')
                ->constrained('accreditation_infos')
                ->cascadeOnDelete();

            $table->foreignId('accreditation_level_id')
                ->constrained('accreditation_levels')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique([
                'accreditation_info_id',
                'accreditation_level_id'
            ], 'accreditation_info_level_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accreditation_info_level');
    }
};
