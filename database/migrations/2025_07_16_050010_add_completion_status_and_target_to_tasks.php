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
        // Menambahkan kolom ke tabel daily_tasks (sebelumnya sub_tasks)
        Schema::table('daily_tasks', function (Blueprint $table) {
            $table->enum('completion_status', ['tepat_waktu', 'terlambat', 'belum_selesai'])->default('belum_selesai')->after('progress');
            $table->text('daily_target')->nullable()->after('completion_status');
        });

        // Menambahkan kolom ke tabel ad_hoc_tasks
        Schema::table('ad_hoc_tasks', function (Blueprint $table) {
            $table->enum('completion_status', ['tepat_waktu', 'terlambat', 'belum_selesai'])->default('belum_selesai')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_tasks', function (Blueprint $table) {
            $table->dropColumn(['completion_status', 'daily_target']);
        });

        Schema::table('ad_hoc_tasks', function (Blueprint $table) {
            $table->dropColumn('completion_status');
        });
    }
};