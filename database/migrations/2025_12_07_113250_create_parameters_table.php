<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parameters', function (Blueprint $table) {
            $table->id();
            $table->string('parameter_name');

            // Use foreignId instead — cleaner and correct
            $table->foreignId('area_id')
                  ->constrained('areas')
                  ->onDelete('cascade');

            $table->index('parameter_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parameters');
    }
};
