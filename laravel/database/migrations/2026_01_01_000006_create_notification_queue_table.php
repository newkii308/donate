<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('streamer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('donation_id')->nullable()->constrained()->nullOnDelete();
            $table->json('payload');                         // full alert data for the overlay
            $table->string('status', 16)->default('pending');
            $table->boolean('is_test')->default(false);
            $table->timestamp('played_at')->nullable();
            $table->timestamps();

            $table->index(['streamer_id', 'status', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_queue');
    }
};
