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
            // Link to confession - which religious confession this donation is for
            $table->foreignId('confession_id')->nullable()->constrained('confessions')->nullOnDelete();
            // Link to branch - which specific branch this donation is for
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            // Translatable donation name (e.g., "Light a Candle", "Support Church")
            $table->json('name');
            // Translatable description explaining what the donation is for
            $table->json('description')->nullable();
            // Purpose code/tag for categorizing donations (e.g., "candle", "sorokoust", "general")
            $table->string('purpose')->nullable();
            // Minimum donation amount allowed
            $table->integer('min_amount')->default(1);
            // Maximum donation amount allowed (null = no limit)
            $table->integer('max_amount')->nullable();
            // Currency code (default XTR - Telegram Stars)
            $table->string('currency')->default('XTR');
            // Emoji icon to display in the donation list
            $table->string('emoji')->nullable();
            // Whether this donation option is currently available
            $table->boolean('active')->default(true);
            // Display order in the donations list
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['confession_id', 'active']);
            $table->index(['branch_id', 'active']);
            $table->index('order');
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
