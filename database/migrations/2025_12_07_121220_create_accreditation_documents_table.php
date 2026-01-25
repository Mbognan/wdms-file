<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accreditation_documents', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('file_path');

            // FK columns as foreignId to match parent PK type
            $table->foreignId('upload_by')->nullable()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('accred_info_id')->constrained('accreditation_infos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('level_id')->constrained('accreditation_levels')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('program_id')->constrained('programs')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('area_id')->constrained('program_area_mappings')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('parameter_id')->nullable()->constrained('area_parameter_mappings')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('subparameter_id')->nullable()->constrained('parameter_subparameter_mappings')->onDelete('cascade')->onUpdate('cascade');
            $table->index('file_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accreditation_documents');
    }
};
