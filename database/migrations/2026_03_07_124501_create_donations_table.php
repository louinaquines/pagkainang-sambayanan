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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();

            $table->string('description'); // For food descriptions [cite: 20, 26]
            $table->string('target_audience'); // e.g., seniors, children [cite: 20, 26]
            $table->string('status')->default('available'); // To track if completed [cite: 30, 35]
            $table->string('feedback_photo')->nullable(); // For mandatory transparency [cite: 23, 29]
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Links to the Donor

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
    
};
