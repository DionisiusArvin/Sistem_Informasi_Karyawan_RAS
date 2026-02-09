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
            $table->renameColumn('sub_task_id', 'daily_task_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_activities', function (Blueprint $table) {
            $table->renameColumn('daily_task_id', 'sub_task_id');
        });
    }
};
