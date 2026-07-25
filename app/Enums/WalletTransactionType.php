<?php

namespace App\Enums;

enum WalletTransactionType: string
{
    case DonationCredit = 'donation_credit';
    case WithdrawalReserve = 'withdrawal_reserve';
    case WithdrawalReversal = 'withdrawal_reversal';
    case AdminAdjustment = 'admin_adjustment';

    public function label(): string
    {
        return match ($this) {
            self::DonationCredit => 'ລາຍຮັບໂດເນດ',
            self::WithdrawalReserve => 'ກັນຍອດຖອນ',
            self::WithdrawalReversal => 'ຄືນຍອດຖອນ',
            self::AdminAdjustment => 'ປັບຍອດໂດຍແອັດມິນ',
        };
    }
}
