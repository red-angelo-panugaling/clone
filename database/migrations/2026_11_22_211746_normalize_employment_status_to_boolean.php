<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('employees', 'employment_status')) {
            return;
        }

        DB::table('employees')->update([
            'employment_status' => DB::raw("CASE WHEN LOWER(TRIM(employment_status)) IN ('active', '1', 'true') THEN 1 ELSE 0 END"),
        ]);

        Schema::table('employees', function (Blueprint $table) {
            $table->boolean('employment_status')->default(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('employees', 'employment_status')) {
            return;
        }

        DB::table('employees')->where('employment_status', true)->update(['employment_status' => 'Active']);
        DB::table('employees')->where('employment_status', false)->update(['employment_status' => 'Inactive']);

        Schema::table('employees', function (Blueprint $table) {
            $table->string('employment_status')->default('Active')->change();
        });
    }
};
