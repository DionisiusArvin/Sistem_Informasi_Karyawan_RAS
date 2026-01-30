<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_activities', function (Blueprint $table) {

            if (!Schema::hasColumn('task_activities', 'daily_task_id')) {
                $table->foreignId('daily_task_id')
                      ->nullable()
                      ->constrained('daily_tasks')
                      ->nullOnDelete();
            }

            if (!Schema::hasColumn('task_activities', 'ad_hoc_task_id')) {
                $table->foreignId('ad_hoc_task_id')
                      ->nullable()
                      ->constrained('ad_hoc_tasks')
                      ->nullOnDelete();
            }

            if (!Schema::hasColumn('task_activities', 'link_url')) {
                $table->string('link_url')->nullable();
            }
        });
    }

    public function down(): void
    {
        //
    }
};

