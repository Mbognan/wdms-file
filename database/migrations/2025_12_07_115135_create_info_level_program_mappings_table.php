<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('info_level_program_mappings', function (Blueprint $table) {
            $table->id();

            // Define columns
            $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
            $table->foreignId('level_id')->constrained('accreditation_levels')->onDelete('cascade');
            $table->foreignId('accreditation_info_id')->constrained('accreditation_infos')->onDelete('cascade');
            $table->enum('status', [
                'Pending',
                'In Progress',
                'Completed'
            ])->default('Pending')->index();
            // Unique index with short name
            $table->unique(
                ['program_id','level_id','accreditation_info_id'],
                'info_level_prog_level_acc_unique'
            );

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('info_level_program_mappings');
    }
};
