<?php

namespace App\Models;

use App\Enums\NotificationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationQueue extends Model
{
    /** @use HasFactory<\Database\Factories\NotificationQueueFactory> */
    use HasFactory;

    protected $table = 'notification_queue';

    protected $fillable = [
        'streamer_id',
        'donation_id',
        'payload',
        'status',
        'is_test',
        'played_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'status' => NotificationStatus::class,
            'is_test' => 'boolean',
            'played_at' => 'datetime',
        ];
    }

    public function streamer(): BelongsTo
    {
        return $this->belongsTo(Streamer::class);
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', NotificationStatus::Pending->value);
    }
}
