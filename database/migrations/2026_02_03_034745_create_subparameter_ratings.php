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
        Schema::create('subparameter_ratings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('evaluation_id')
                ->constrained('accreditation_evaluations')
                ->cascadeOnDelete();

            $table->foreignId('subparameter_id')
                ->constrained('sub_parameters')
                ->cascadeOnDelete();

            $table->foreignId('rating_option_id')
                ->constrained('rating_options')
                ->restrictOnDelete();

            $table->tinyInteger('score')->unsigned()->nullable();

            $table->timestamps();

            $table->unique(['evaluation_id', 'subparameter_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subparameter_ratings');
    }
};
