<?php

use App\Enums\AccreditationStatus;
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
        // Normalize existing data
        DB::table('accreditation_infos')
            ->whereNull('status')
            ->update(['status' => AccreditationStatus::ONGOING->value]);

        DB::table('accreditation_infos')
            ->whereNotIn('status', array_column(AccreditationStatus::cases(), 'value'))
            ->update(['status' => AccreditationStatus::ONGOING->value]);

        // Ensure column definition is correct
        Schema::table('accreditation_infos', function (Blueprint $table) {
            $table->string('status')
                ->default(AccreditationStatus::ONGOING->value)
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to rollback
    }
};
