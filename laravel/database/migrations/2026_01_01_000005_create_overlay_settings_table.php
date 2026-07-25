<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('overlay_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('streamer_id')->unique()->constrained()->cascadeOnDelete();

            // ธีมสำเร็จรูป (neon-purple, cyber-blue, ... , custom)
            $table->string('theme', 24)->default('neon-purple');

            // Typography
            $table->string('font_family')->default("'Noto Sans Lao', sans-serif");
            $table->unsignedSmallInteger('font_size')->default(30);
            $table->string('font_weight', 8)->default('700');
            $table->string('font_color', 32)->default('#ffffff');

            // Box
            $table->string('background_color', 64)->default('rgba(17,17,27,0.90)');
            $table->unsignedSmallInteger('border_radius')->default(16);
            $table->boolean('shadow')->default(true);
            $table->string('position', 24)->default('top-center');
            $table->unsignedSmallInteger('width')->default(480);
            $table->unsignedSmallInteger('height')->nullable();

            // Animation
            $table->string('animation', 24)->default('fade');     // fade | slide | bounce | zoom
            $table->unsignedInteger('animation_duration')->default(500);   // ms
            $table->unsignedInteger('display_duration')->default(8000);    // ms

            // Default alert media
            $table->boolean('show_avatar')->default(true);
            $table->foreignId('image_media_id')->nullable()->constrained('media')->nullOnDelete();

            // Sound
            $table->boolean('sound_enabled')->default(true);
            $table->string('sound_preset', 24)->nullable()->default('ding'); // เสียงในระบบ (public/sounds/<key>.wav)
            $table->foreignId('sound_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->unsignedSmallInteger('sound_volume')->default(80);     // 0-100
            $table->unsignedInteger('sound_delay')->default(0);            // ms before TTS/sound

            // TTS
            $table->boolean('tts_enabled')->default(true);
            $table->text('tts_template')->nullable();
            $table->string('tts_language', 16)->default('lo-LA');
            $table->string('tts_voice')->nullable();
            $table->decimal('tts_rate', 3, 2)->default(1.00);
            $table->decimal('tts_pitch', 3, 2)->default(1.00);
            $table->unsignedSmallInteger('tts_volume')->default(100);      // 0-100

            // Filtering
            $table->decimal('min_amount_to_show', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overlay_settings');
    }
};
