<?php

namespace App\Models;

use App\Enums\DonationStatus;
use Database\Factories\DonationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donation extends Model
{
    /** @use HasFactory<DonationFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'streamer_id',
        'donor_name',
        'amount',
        'platform_fee',
        'net_amount',
        'currency',
        'transfer_reference',
        'message',
        'is_anonymous',
        'status',
        'verified_at',
        'verified_by',
        'rejection_reason',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'is_anonymous' => 'boolean',
            'status' => DonationStatus::class,
            'verified_at' => 'datetime',
        ];
    }

    public function streamer(): BelongsTo
    {
        return $this->belongsTo(Streamer::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function walletTransaction(): HasOne
    {
        return $this->hasOne(WalletTransaction::class);
    }

    // Accessors ----------------------------------------------------------

    /**
     * The donor name shown publicly (respects anonymity).
     */
    public function displayName(): string
    {
        return $this->is_anonymous ? 'ບໍ່ປະສົງອອກນາມ' : $this->donor_name;
    }

    // Scopes -------------------------------------------------------------

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', DonationStatus::Completed->value);
    }

    public function scopeForStreamer(Builder $query, int $streamerId): Builder
    {
        return $query->where('streamer_id', $streamerId);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('donor_name', 'like', "%{$term}%")
                ->orWhere('message', 'like', "%{$term}%")
                ->orWhere('transfer_reference', 'like', "%{$term}%");
        });
    }
}
