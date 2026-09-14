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
        Schema::create('otp_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->nullable()->references('id')->on('users')->onDelete('set null');
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('type')->nullable();
            $table->string('token');
            $table->timestamp('expires');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_tokens');
    }
};
