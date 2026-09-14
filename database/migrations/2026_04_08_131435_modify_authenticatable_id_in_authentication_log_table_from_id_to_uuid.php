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
        $tableName = config('authentication-log.table_name', 'authentication_log');

        if (! Schema::hasTable($tableName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) {
            // Option A: Explicitly change to string
            $table->string('authenticatable_id')->change();

            // Option B: If you prefer the cleaner morph syntax
            // $table->uuidMorphs('authenticatable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = config('authentication-log.table_name', 'authentication_log');

        if (! Schema::hasTable($tableName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) {
            // $table->unsignedBigInteger('authenticatable_id')->change();
            // option b
            $table->dropMorphs('authenticatable');
        });
    }
};
