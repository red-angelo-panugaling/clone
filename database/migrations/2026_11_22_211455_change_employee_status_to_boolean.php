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
        $knownStatuses = ['active', 'inactive', 'deleted', 'hidden', '1', '0', 'true', 'false'];
        $unmappedStatuses = DB::table('employees')
            ->select('status')
            ->distinct()
            ->pluck('status')
            ->filter(fn ($status): bool => ! in_array(strtolower(trim((string) $status)), $knownStatuses, true));

        if ($unmappedStatuses->isNotEmpty()) {
            throw new RuntimeException('Employee statuses could not be normalized: '.$unmappedStatuses->implode(', '));
        }

        DB::table('employees')
            ->whereRaw('LOWER(TRIM(status)) IN (?, ?, ?)', ['active', '1', 'true'])
            ->update(['status' => '1']);

        DB::table('employees')
            ->whereRaw('LOWER(TRIM(status)) IN (?, ?, ?, ?, ?)', ['inactive', 'deleted', 'hidden', '0', 'false'])
            ->update(['status' => '0']);

        Schema::table('employees', function (Blueprint $table) {
            $table->boolean('status')->default(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('employees')->where('status', true)->update(['status' => 'active']);
        DB::table('employees')->where('status', false)->update(['status' => 'deleted']);

        Schema::table('employees', function (Blueprint $table) {
            $table->string('status', 20)->default('active')->change();
        });
    }
};
