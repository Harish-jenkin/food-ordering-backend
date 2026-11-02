<?php

namespace App\Support;

class Money
{
    public static function round(float|int|string $amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }
}
