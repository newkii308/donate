<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('streamer_id')->unique()->constrained()->cascadeOnDelete();

            // Goal metrics
            $table->string('title')->default('ຊື້ໄມໂຄຣໂຟນໃໝ່');
            $table->decimal('target_amount', 12, 2)->default(1000000.00);
            $table->decimal('current_amount', 12, 2)->default(375000.00);
            $table->string('bottom_text')->default('ຂອບໃຈທຸກການສະໜັບສະໜູນ! ❤️');

            // Theme & Progress config
            $table->string('theme_id', 32)->default('minimal');
            $table->string('progress_style', 32)->default('fill'); // fill, water, wave, gradient, glow, shine, rainbow, segmented, pixel
            $table->json('effects')->nullable(); // JSON array of active effects: e.g. ["glow", "shine"]
            $table->json('favorite_themes')->nullable(); // Array of favorited theme IDs

            // Toggles
            $table->boolean('show_title')->default(true);
            $table->boolean('show_current')->default(true);
            $table->boolean('show_target')->default(true);
            $table->boolean('show_percentage')->default(true);
            $table->boolean('show_bottom_text')->default(true);

            // Celebration
            $table->boolean('celebration_enabled')->default(true);
            $table->string('celebration_type', 32)->default('confetti'); // confetti, firework, golden-shine, sparkle
            $table->string('celebration_message')->default('ບັນລຸເປົ້າໝາຍແລ້ວ! 🎉');

            // Custom theme properties (when theme_id == 'custom')
            $table->string('primary_color', 32)->default('#8b5cf6');
            $table->string('secondary_color', 32)->default('#ec4899');
            $table->string('background_style')->default('rgba(17,17,27,0.9)');
            $table->string('border_style')->default('1px solid rgba(255,255,255,.12)');
            $table->unsignedSmallInteger('border_radius')->default(16);
            $table->string('shadow_style')->default('0 18px 50px rgba(0,0,0,.5)');
            $table->string('font_family')->default("'Noto Sans Lao', sans-serif");
            $table->string('font_weight', 8)->default('700');
            $table->string('font_color', 32)->default('#ffffff');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_goals');
    }
};
