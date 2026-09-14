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
        Schema::create('waitlists', function (Blueprint $table) {
            $table->uuid('id')->primary(); // title, body, action [unread, read] status [success]
            $table->string('type')->default('beta'); // alfa, beta testing
            $table->string('source')->default('landing-page'); // landing page, beta
            $table->string('ip_address')->nullable();
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('status')->default('pending'); // pending, invited, rejected
            $table->json('data')->nullable(); // JSON payload containing your custom data
            $table->timestamp('invited_at')->nullable(); // Timestamp when the user is invited
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waitlists');
    }
};
