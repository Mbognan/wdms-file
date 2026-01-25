<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();

            // Foreign key columns (match parent PK types if needed)
            $table->foreignId('accred_info_id');
            $table->foreignId('level_id');
            $table->foreignId('program_id');
            $table->foreignId('area_id');
            $table->foreignId('parameter_id');
            $table->foreignId('subparameter_id');
            $table->foreignId('indicator_id');
            $table->foreignId('rated_by');

            $table->tinyInteger('rating')->nullable();
            $table->text('recommendations');

            // Use timestamps helper for created_at and updated_at
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
