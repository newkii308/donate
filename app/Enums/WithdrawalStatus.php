<?php

namespace App\Enums;

enum WithdrawalStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Paid = 'paid';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'ລໍຖ້າກວດສອບ',
            self::Approved => 'ອະນຸມັດແລ້ວ',
            self::Paid => 'ໂອນເງິນແລ້ວ',
            self::Rejected => 'ປະຕິເສດ',
            self::Cancelled => 'ຍົກເລີກ',
        };
    }
}
