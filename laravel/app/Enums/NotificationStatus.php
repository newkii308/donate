<?php

namespace App\Enums;

enum NotificationStatus: string
{
    case Pending = 'pending';
    case Playing = 'playing';
    case Completed = 'completed';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $s) => $s->value, self::cases());
    }
}
