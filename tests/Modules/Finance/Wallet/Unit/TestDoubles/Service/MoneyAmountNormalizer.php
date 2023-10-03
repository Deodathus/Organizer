<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\Service;

final class MoneyAmountNormalizer
{
    public static function normalize(int $amount): int
    {
        return $amount / 100;
    }
}
