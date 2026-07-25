<?php

namespace App\Models;

use App\Enums\MediaType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    /** @use HasFactory<\Database\Factories\MediaFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'media';

    protected $fillable = [
        'streamer_id',
        'type',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'type' => MediaType::class,
            'size' => 'integer',
            'meta' => 'array',
        ];
    }

    public function streamer(): BelongsTo
    {
        return $this->belongsTo(Streamer::class);
    }

    public function scopeOfType(Builder $query, MediaType|string $type): Builder
    {
        return $query->where('type', $type instanceof MediaType ? $type->value : $type);
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function humanSize(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1).' '.$units[$i];
    }
}
