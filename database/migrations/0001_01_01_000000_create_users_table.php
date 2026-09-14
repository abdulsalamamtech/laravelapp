<?php

use App\Enums\AppRole;
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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->string('password');
            // two_factor_auth - 2fa enabled or disabled
            $table->string('two_factor_auth')->nullable()->default('disable');
            $table->string('two_factor_type')->nullable()->default('email'); // email or sms or authenticator app
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('send_unverified_mail_at')->nullable();
            $table->string('app_role')->nullable()->default(AppRole::USER->value);
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('avatar')->nullable();
            $table->string('google_id')->nullable();
            $table->string('github_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('address')->nullable();
            $table->string('city')->nullable(); // city, town, lga
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('currency')->nullable()->default('NGN');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            // $table->foreignId('user_id')->nullable()->index();
            $table->foreignUuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
