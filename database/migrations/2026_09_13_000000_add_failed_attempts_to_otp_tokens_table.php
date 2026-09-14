<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('otp_tokens', function (Blueprint $table) {
            $table->unsignedInteger('failed_attempts')->default(0)->after('expires');
        });
    }

    public function down(): void
    {
        Schema::table('otp_tokens', function (Blueprint $table) {
            $table->dropColumn('failed_attempts');
        });
    }
};
