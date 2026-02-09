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
        Schema::table('task_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('task_activities', 'progress_percent')) {
                $table->decimal('progress_percent', 5, 2)->nullable()->after('link_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_activities', function (Blueprint $table) {
            $table->dropColumn('progress_percent');
        });
    }
};
