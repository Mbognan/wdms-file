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
        Schema::create('rating_options', function (Blueprint $table) {
            $table->id();

            $table->string('label'); // e.g. Available, Not Applicable

            $table->tinyInteger('min_score')->unsigned()->nullable();
            $table->tinyInteger('max_score')->unsigned()->nullable();

            $table->boolean('applicable')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rating_options');
    }
};
