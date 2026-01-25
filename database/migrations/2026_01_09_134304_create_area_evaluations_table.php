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
        Schema::create('area_evaluations', function (Blueprint $table) {
            $table->id();

            // 🔑 One evaluation per program-area
            $table->foreignId('program_area_mapping_id')
                ->constrained('program_area_mappings')
                ->cascadeOnDelete();

            // 👤 Internal Accessor (system user)
            $table->foreignId('internal_accessor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // 📌 Evaluation status
            $table->enum('status', [
                'not_started',
                'ongoing',
                'completed'
            ])->default('not_started');

            // 🕒 Completion timestamp
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // 🚫 Prevent duplicates
            $table->unique('program_area_mapping_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_evaluations');
    }
};
