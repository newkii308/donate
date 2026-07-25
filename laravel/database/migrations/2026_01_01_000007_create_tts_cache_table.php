<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tts_cache', function (Blueprint $table) {
            $table->id();
            $table->string('hash', 64)->unique();           // sha1 of text + voice params
            $table->text('text');
            $table->string('language', 16);
            $table->string('voice')->nullable();
            $table->string('disk', 32)->nullable();
            $table->string('path')->nullable();             // cached audio file (null for browser TTS)
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tts_cache');
    }
};
