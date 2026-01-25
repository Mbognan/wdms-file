<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accreditation_assignments', function (Blueprint $table) {

            // 1️⃣ Drop wrong foreign keys
            $table->dropForeign(['accred_info_id']);
            $table->dropForeign(['level_id']);
            $table->dropForeign(['program_id']);

            // 2️⃣ Add correct foreign keys pointing to real tables
            $table->foreign('accred_info_id')
                ->references('id')->on('accreditation_infos')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('level_id')
                ->references('id')->on('accreditation_levels')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('program_id')
                ->references('id')->on('programs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('accreditation_assignments', function (Blueprint $table) {

            // Drop the corrected FKs
            $table->dropForeign(['accred_info_id']);
            $table->dropForeign(['level_id']);
            $table->dropForeign(['program_id']);

            // Restore old foreign keys pointing to info_level_program_mappings
            $table->foreign('accred_info_id')
                ->references('id')->on('info_level_program_mappings')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('level_id')
                ->references('id')->on('info_level_program_mappings')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('program_id')
                ->references('id')->on('info_level_program_mappings')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }
};
