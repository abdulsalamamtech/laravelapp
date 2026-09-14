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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary(); // title, body, action [unread, read] status [success]
            $table->string('type'); // The fully qualified class name (e.g., App\Notifications\InvoicePaid)
            // $table->morphs('notifiable');
            $table->uuidMorphs('notifiable'); // The morph class type of the recipient (e.g., App\Models\User) + id
            // $table->text('data');
            $table->json('data'); // JSON payload containing your custom notification data
            $table->timestamp('read_at')->nullable(); // Timestamp when the user read it; NULL means unread
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
