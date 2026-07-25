<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('streamer_id')->constrained()->cascadeOnDelete();
            $table->string('donor_name', 60);
            $table->decimal('amount', 12, 2);
            $table->string('currency', 8);
            $table->text('message')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->string('status', 16)->default('completed');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['streamer_id', 'created_at']);
            $table->index(['streamer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
