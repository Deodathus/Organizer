<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\Exception;

final class TransactionCurrencyIsDifferentWalletHas extends \Exception
{
    public static function withCurrenciesCodes(string $transactionCurrencyCode, string $walletCurrencyCode): self
    {
        return new self(
            sprintf(
                'Transaction currency is different than wallet\'s one!
                 Transaction currency code: "%s", Wallet currency code: "%s"',
                $transactionCurrencyCode,
                $walletCurrencyCode
            )
        );
    }
}
