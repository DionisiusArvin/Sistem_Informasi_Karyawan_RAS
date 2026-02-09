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
         Schema::create('task_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_task_id')->constrained('sub_tasks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('activity_type', ['upload_pekerjaan', 'permintaan_revisi', 'komentar_biasa']);
            $table->text('notes')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_activities');
    }
};
