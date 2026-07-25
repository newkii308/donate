<?php

namespace App\Enums;

enum DonationStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'ລໍດຳເນີນການ',
            self::Completed => 'ສຳເລັດ',
            self::Failed => 'ບໍ່ສຳເລັດ',
            self::Rejected => 'ປະຕິເສດ',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $s) => $s->value, self::cases());
    }
}
