<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('daily_task_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        //
    }
};
