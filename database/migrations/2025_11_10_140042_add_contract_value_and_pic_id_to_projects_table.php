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
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'contract_value')) {
                $table->decimal('contract_value', 15, 2)->nullable()->after('category');
            }

            if (!Schema::hasColumn('projects', 'pic_id')) {
                $table->unsignedBigInteger('pic_id')->nullable()->after('contract_value');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['contract_value', 'pic_id']);
        });
    }
};
