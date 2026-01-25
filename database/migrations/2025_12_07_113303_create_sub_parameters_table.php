<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('sub_parameter_name');
            $table->index('sub_parameter_name');

            // Missing column added + FK
            $table->foreignId('parameter_id')
                  ->constrained('parameters')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_parameters');
    }
};
