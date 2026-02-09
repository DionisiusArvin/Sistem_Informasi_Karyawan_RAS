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
        Schema::table('task_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('task_activities', 'progress_percent')) {
                $table->decimal('progress_percent', 5, 2)->nullable()->after('link_url');
            }
        });

        if (Schema::hasColumn('task_activities', 'progress_hours')) {
            DB::statement('UPDATE task_activities SET progress_percent = progress_hours WHERE progress_percent IS NULL');

            Schema::table('task_activities', function (Blueprint $table) {
                $table->dropColumn('progress_hours');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('task_activities', 'progress_hours')) {
                $table->decimal('progress_hours', 4, 2)->nullable()->after('link_url');
            }
        });

        if (Schema::hasColumn('task_activities', 'progress_percent')) {
            DB::statement('UPDATE task_activities SET progress_hours = progress_percent WHERE progress_hours IS NULL');

            Schema::table('task_activities', function (Blueprint $table) {
                $table->dropColumn('progress_percent');
            });
        }
    }
};
