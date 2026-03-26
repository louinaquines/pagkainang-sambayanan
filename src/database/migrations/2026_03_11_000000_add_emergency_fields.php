<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add area_severity to users (for charity's area)
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('area_severity')->default(1)->after('verification_status');
            // 1 = low, 2 = medium, 3 = high, 4 = critical
        });

        // Add area_severity and expiry_time to donations
        Schema::table('donations', function (Blueprint $table) {
            $table->tinyInteger('area_severity')->default(1)->after('status');
            $table->timestamp('expires_at')->nullable()->after('area_severity');
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['area_severity', 'expires_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('area_severity');
        });
    }
};
