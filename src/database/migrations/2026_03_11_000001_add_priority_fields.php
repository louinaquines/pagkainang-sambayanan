<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('population_count')->default(0)->after('area_severity');
            $table->tinyInteger('accessibility')->default(100)->after('population_count');
            // accessibility: 0-100 (100 = fully accessible, 0 = inaccessible)
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['population_count', 'accessibility']);
        });
    }
};
