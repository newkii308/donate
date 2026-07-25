<?php

namespace App\Support;

class Money
{
    /**
     * Format an amount for display, dropping trailing ".00" and adding
     * thousands separators. e.g. 100.00 -> "100", 1234.50 -> "1,234.50"
     */
    public static function format(float|string $amount): string
    {
        $value = (float) $amount;

        if (floor($value) == $value) {
            return number_format($value, 0);
        }

        return number_format($value, 2);
    }
}
