<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('streamer_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->decimal('min_amount', 12, 2)->default(1000);
            $table->decimal('max_amount', 12, 2)->nullable();
            $table->json('quick_amounts')->nullable();          // suggested buttons e.g. [20,50,100]
            $table->boolean('allow_anonymous')->default(true);
            $table->boolean('show_recent_donations')->default(true);
            $table->string('thank_you_message')->nullable();
            $table->string('theme', 16)->default('dark');       // dark | light
            $table->string('accent_color', 16)->default('#6d28d9');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_pages');
    }
};
