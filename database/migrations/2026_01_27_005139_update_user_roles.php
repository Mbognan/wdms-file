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
        Schema::table('users', function (Blueprint $table) {
           DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('UNVERIFIED USER', 'ADMIN', 'TASK FORCE', 'TASK FORCE CHAIR', 'INTERNAL ASSESSOR', 'ACCREDITOR') DEFAULT 'UNVERIFIED USER'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
           DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('ADMIN', 'TASK FORCE', 'TASK FORCE CHAIR', 'ACCREDITOR', 'INTERNAL ACCESSOR') DEFAULT 'TASK FORCE'");
        });
    }
};
