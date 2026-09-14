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
        Schema::create('pending_email_changes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('old_email')->index();
            $table->string('pending_email')->index();
            $table->string('status')->default('pending')->index();
            $table->timestamp('initiated_at');
            $table->timestamp('old_confirmed_at')->nullable();
            $table->timestamp('new_confirmed_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('effective_at')->nullable();
            $table->timestamp('changed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancelled_reason')->nullable();
            $table->string('confirming_token_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_email_changes');
    }
};
