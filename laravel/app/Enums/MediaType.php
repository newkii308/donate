<?php

namespace App\Enums;

enum MediaType: string
{
    case Image = 'image';
    case Animation = 'animation';
    case Audio = 'audio';

    public function label(): string
    {
        return match ($this) {
            self::Image => 'ຮູບພາບ',
            self::Animation => 'ພາບເຄື່ອນໄຫວ',
            self::Audio => 'ສຽງ',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $t) => $t->value, self::cases());
    }
}
