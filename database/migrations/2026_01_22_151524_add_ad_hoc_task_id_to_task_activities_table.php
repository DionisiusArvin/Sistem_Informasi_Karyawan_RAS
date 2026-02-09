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
        $table->foreignId('ad_hoc_task_id')
              ->nullable()
              ->constrained('ad_hoc_tasks')
              ->cascadeOnDelete();
    });
}

public function down(): void
{
    Schema::table('task_activities', function (Blueprint $table) {
        $table->dropForeign(['ad_hoc_task_id']);
        $table->dropColumn('ad_hoc_task_id');
    });
}
};
