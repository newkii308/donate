<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonationPage extends Model
{
    /** @use HasFactory<\Database\Factories\DonationPageFactory> */
    use HasFactory;

    protected $fillable = [
        'streamer_id',
        'title',
        'description',
        'min_amount',
        'max_amount',
        'quick_amounts',
        'allow_anonymous',
        'show_recent_donations',
        'thank_you_message',
        'theme',
        'accent_color',
    ];

    protected function casts(): array
    {
        return [
            'min_amount' => 'decimal:2',
            'max_amount' => 'decimal:2',
            'quick_amounts' => 'array',
            'allow_anonymous' => 'boolean',
            'show_recent_donations' => 'boolean',
        ];
    }

    public function streamer(): BelongsTo
    {
        return $this->belongsTo(Streamer::class);
    }
}
