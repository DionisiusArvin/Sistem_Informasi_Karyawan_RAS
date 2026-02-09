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
        Schema::table('admin_tasks', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('description');
            $table->string('file_path')->nullable()->after('status');
            $table->text('notes')->nullable()->after('file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_tasks', function (Blueprint $table) {
            //
        });
    }
};
