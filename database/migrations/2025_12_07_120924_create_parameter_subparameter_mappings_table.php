<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parameter_subparameter_mappings', function (Blueprint $table) {
            $table->id();

            // Define columns as bigint unsigned
            $table->unsignedBigInteger('area_parameter_mapping_id');
            $table->unsignedBigInteger('subparameter_id');

            // Short foreign key names
            $table->foreign('area_parameter_mapping_id', 'fk_param_sub_area_mapping')
                  ->references('id')->on('area_parameter_mappings')
                  ->onDelete('cascade');

            $table->foreign('subparameter_id', 'fk_param_sub_subparameter')
                  ->references('id')->on('sub_parameters')
                  ->onDelete('cascade');

            // Short unique index
            $table->unique(['area_parameter_mapping_id', 'subparameter_id'], 'param_subparam_unique');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parameter_subparameter_mappings');
    }
};
