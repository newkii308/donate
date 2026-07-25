<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OverlaySetting extends Model
{
    /** @use HasFactory<\Database\Factories\OverlaySettingFactory> */
    use HasFactory;

    protected $fillable = [
        'streamer_id',
        'theme',
        'alert_style',
        'font_family',
        'font_size',
        'font_weight',
        'font_color',
        'background_color',
        'border_radius',
        'shadow',
        'position',
        'width',
        'height',
        'animation',
        'animation_duration',
        'display_duration',
        'show_avatar',
        'image_media_id',
        'sound_enabled',
        'sound_preset',
        'sound_media_id',
        'sound_volume',
        'sound_delay',
        'tts_enabled',
        'tts_template',
        'tts_language',
        'tts_voice',
        'tts_rate',
        'tts_pitch',
        'tts_volume',
        'min_amount_to_show',
    ];

    protected function casts(): array
    {
        return [
            'font_size' => 'integer',
            'border_radius' => 'integer',
            'shadow' => 'boolean',
            'width' => 'integer',
            'height' => 'integer',
            'animation_duration' => 'integer',
            'display_duration' => 'integer',
            'show_avatar' => 'boolean',
            'sound_enabled' => 'boolean',
            'sound_volume' => 'integer',
            'sound_delay' => 'integer',
            'tts_enabled' => 'boolean',
            'tts_rate' => 'decimal:2',
            'tts_pitch' => 'decimal:2',
            'tts_volume' => 'integer',
            'min_amount_to_show' => 'decimal:2',
        ];
    }

    public function streamer(): BelongsTo
    {
        return $this->belongsTo(Streamer::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_media_id');
    }

    public function sound(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'sound_media_id');
    }
}
