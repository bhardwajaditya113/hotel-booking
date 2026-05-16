<?php

namespace App\Support;

final class Currency
{
    public static function inr(mixed $amount, int $fractionDigits = 0): string
    {
        $n = is_numeric($amount) ? (float) $amount : 0.0;

        return '₹'.number_format($n, $fractionDigits, '.', ',');
    }
}
