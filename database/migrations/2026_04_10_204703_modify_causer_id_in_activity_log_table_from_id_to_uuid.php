<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Schema::connection(config('activitylog.database_connection'))->table(config('activitylog.table_name'), function (Blueprint $table) {
        //     $table->uuid('causer_id')->nullable()->change();
        //     $table->uuid('subject_id')->nullable()->change();
        // });
        $tableName = config('activitylog.table_name');
        $connection = config('activitylog.database_connection');

        // Optional: If you don't care about keeping local/staging logs,
        // uncomment the line below to completely clear data and avoid conflicts:
        // DB::connection($connection)->table($tableName)->truncate();

        Schema::connection($connection)->table($tableName, function (Blueprint $table) {
            // changing to varchar/uuid is usually allowed by MySQL,
            // but we ensure it is nullable to avoid constraint issues.
            $table->uuid('causer_id')->nullable()->change();
            $table->uuid('subject_id')->nullable()->change();
        });
    }

    public function down()
    {
        // Schema::connection(config('activitylog.database_connection'))->table(config('activitylog.table_name'), function (Blueprint $table) {
        //     $table->unsignedBigInteger('causer_id')->change();
        //     $table->unsignedBigInteger('subject_id')->change();
        // });
        $tableName = config('activitylog.table_name');
        $connection = config('activitylog.database_connection');

        // CRITICAL FIX: Set alphanumeric UUID strings to null before changing back to BigInt.
        // Otherwise, MySQL throws the 1265 Data truncated warning.
        DB::connection($connection)->table($tableName)
            ->whereRaw("causer_id REGEXP '[^0-9]'")
            ->update(['causer_id' => null]);

        DB::connection($connection)->table($tableName)
            ->whereRaw("subject_id REGEXP '[^0-9]'")
            ->update(['subject_id' => null]);

        Schema::connection($connection)->table($tableName, function (Blueprint $table) {
            // Modify back to BigInt. Must be nullable since we set text UUIDs to null.
            $table->unsignedBigInteger('causer_id')->nullable()->change();
            $table->unsignedBigInteger('subject_id')->nullable()->change();
        });
    }
};
