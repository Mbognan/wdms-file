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
        Schema::create('program_final_verdicts', function (Blueprint $table) {
            $table->id();


            $table->foreignId('program_id')
                ->constrained('programs')
                ->cascadeOnDelete();

            $table->foreignId('accred_info_id')
                ->constrained('accreditation_infos')
                ->cascadeOnDelete();




            $table->foreignId('decided_by')
                ->constrained('users')
                ->cascadeOnDelete();


            $table->enum('status', ['revisit', 'completed']);


            $table->date('revisit_until')->nullable();


            $table->text('comments');


            $table->timestamp('finalized_at')->nullable();

            $table->timestamps();


            $table->unique(['program_id', 'accred_info_id']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_final_verdicts');
    }
};
