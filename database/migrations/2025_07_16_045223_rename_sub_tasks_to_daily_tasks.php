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
        // Mengubah nama tabel dari 'sub_tasks' menjadi 'daily_tasks'
        Schema::rename('sub_tasks', 'daily_tasks');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('daily_tasks', 'sub_tasks');
    }
};
