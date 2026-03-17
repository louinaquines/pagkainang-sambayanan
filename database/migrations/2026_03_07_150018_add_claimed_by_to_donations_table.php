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
        Schema::table('donations', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('claimed_by')->nullable(); // charity user ID
            $table->timestamp('claimed_at')->nullable();
            $table->foreign('claimed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            //
            $table->dropForeign(['claimed_by']);
            $table->dropColumn(['claimed_by', 'claimed_at']);
        });
    }
};
