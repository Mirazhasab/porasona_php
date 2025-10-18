<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_accesses', function (Blueprint $table) {
            if (!Schema::hasColumn('user_accesses', 'notes')) {
                $table->boolean('notes')->default(false)->after('mcq_management');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_accesses', function (Blueprint $table) {
            if (Schema::hasColumn('user_accesses', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
