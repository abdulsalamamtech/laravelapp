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
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('other_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('subject')->nullable();
            $table->string('purpose')->nullable();
            $table->string('organization')->nullable();
            $table->text('message')->nullable();
            $table->text('feedback')->nullable();
            // $table->enum('status', ['unread', 'read', 'answered', 'replied'])->default('unread');
            $table->string('status')->default('unread');
            $table->json('raw_data')->nullable();
            $table->foreignUuid('updated_by')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
