<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Streamer = 'streamer';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'ຜູ້ດູແລລະບົບ',
            self::Streamer => 'ສະຕຣີມເມີ',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $role) => $role->value, self::cases());
    }
}
