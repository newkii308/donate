<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonationGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'streamer_id',
        'title',
        'target_amount',
        'current_amount',
        'bottom_text',
        'theme_id',
        'progress_style',
        'effects',
        'favorite_themes',
        'show_title',
        'show_current',
        'show_target',
        'show_percentage',
        'show_bottom_text',
        'celebration_enabled',
        'celebration_type',
        'celebration_message',
        'primary_color',
        'secondary_color',
        'background_style',
        'border_style',
        'border_radius',
        'shadow_style',
        'font_family',
        'font_weight',
        'font_color',
    ];

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'current_amount' => 'decimal:2',
            'effects' => 'array',
            'favorite_themes' => 'array',
            'show_title' => 'boolean',
            'show_current' => 'boolean',
            'show_target' => 'boolean',
            'show_percentage' => 'boolean',
            'show_bottom_text' => 'boolean',
            'celebration_enabled' => 'boolean',
            'border_radius' => 'integer',
        ];
    }

    public function streamer(): BelongsTo
    {
        return $this->belongsTo(Streamer::class);
    }
}
