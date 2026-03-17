<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('charity_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('charity_id');
            $table->string('food_name');
            $table->text('description')->nullable();
            $table->string('quantity')->nullable(); // e.g. "50 packs", "10 kg"
            $table->string('urgency')->default('normal'); // normal, urgent, critical
            $table->string('status')->default('open'); // open, fulfilled
            $table->timestamps();

            $table->foreign('charity_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charity_requests');
    }
};