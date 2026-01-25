<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accreditation_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('accred_info_id')->constrained('accreditation_infos')->onDelete('cascade');
            $table->foreignId('level_id')->constrained('accreditation_levels')->onDelete('cascade');
            $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
            $table->foreignId('area_id')->constrained('program_area_mappings')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('parameter_id')->nullable()->constrained('area_parameter_mappings')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('subparameter_id')->nullable()->constrained('parameter_subparameter_mappings')->onDelete('cascade')->onUpdate('cascade');

            // Define indicator_id if needed
            $table->unsignedBigInteger('indicator_id')->nullable();

            // Short unique index name
            $table->unique(['area_id','parameter_id','subparameter_id','indicator_id','accred_info_id','level_id','program_id'], 'uq_assignment');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accreditation_assignments');
    }
};
