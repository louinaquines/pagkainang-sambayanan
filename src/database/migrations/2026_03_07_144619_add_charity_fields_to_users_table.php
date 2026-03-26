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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('organization_name')->nullable();
            $table->text('organization_description')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('address')->nullable();
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])
                ->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn([
                'organization_name',
                'organization_description',
                'contact_number',
                'address',
                'verification_status',
            ]);
        });
    }
};
