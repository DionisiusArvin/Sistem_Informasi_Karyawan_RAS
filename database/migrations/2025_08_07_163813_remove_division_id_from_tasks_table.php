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
        Schema::table('tasks', function (Blueprint $table) {
             // Hapus foreign key constraint terlebih dahulu
            $table->dropForeign(['division_id']);
            // Hapus kolomnya
            $table->dropColumn('division_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Tambahkan kembali kolom division_id
            $table->foreignId('division_id')->constrained()->onDelete('cascade');
        });
    }
};
