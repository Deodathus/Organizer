<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\Mother;

use App\Modules\Finance\Wallet\Application\DTO\WalletDTO;

final class WalletDTOMother
{
    public const NAME = 'Test wallet';
    public const CREATOR_ID = 'creator-id';
    public const START_BALANCE = '100';
    public const DECIMAL_START_BALANCE = '100.25';
    public const CURRENCY_ID = 'currency-id';
    public const CURRENCY_CODE = 'PLN';

    public static function createWithDefaults(
        string $startBalance = self::START_BALANCE,
        string $name = self::NAME,
        string $creatorId = self::CREATOR_ID,
        string $currencyId = self::CURRENCY_ID,
        string $currencyCode = self::CURRENCY_CODE
    ): WalletDTO {
        return new WalletDTO(
            $name,
            $creatorId,
            $startBalance,
            $currencyId,
            $currencyCode
        );
    }
}
