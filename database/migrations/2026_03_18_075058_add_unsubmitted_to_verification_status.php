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
        DB::statement("ALTER TABLE users MODIFY COLUMN verification_status ENUM('pending', 'approved', 'rejected', 'unsubmitted') DEFAULT 'unsubmitted'");
        Schema::table('verification_status', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         DB::statement("ALTER TABLE users MODIFY COLUMN verification_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
        Schema::table('verification_status', function (Blueprint $table) {
            //
        });
    }
};
