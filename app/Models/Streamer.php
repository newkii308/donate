<?php

namespace App\Models;

use Database\Factories\StreamerFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Streamer extends Model
{
    /** @use HasFactory<StreamerFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'username',
        'overlay_key',
        'display_name',
        'description',
        'avatar_path',
        'qr_code_path',
        'truewallet_phone',
        'payment_method',
        'account_name',
        'account_number',
        'bank_name',
        'currency',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Streamer $streamer) {
            $streamer->overlay_key ??= self::generateOverlayKey();
        });
    }

    public static function generateOverlayKey(): string
    {
        do {
            $key = Str::lower(Str::random(40));
        } while (self::where('overlay_key', $key)->exists());

        return $key;
    }

    // Relationships ------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function donationPage(): HasOne
    {
        return $this->hasOne(DonationPage::class);
    }

    public function overlaySetting(): HasOne
    {
        return $this->hasOne(OverlaySetting::class);
    }

    public function donationGoal(): HasOne
    {
        return $this->hasOne(DonationGoal::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(NotificationQueue::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    // Scopes -------------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // Accessors ----------------------------------------------------------

    public function avatarUrl(): ?string
    {
        return $this->avatar_path ? Storage::disk('public')->url($this->avatar_path) : null;
    }

    public function qrCodeUrl(): ?string
    {
        return $this->qr_code_path ? Storage::disk('public')->url($this->qr_code_path) : null;
    }
}
