<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\Exception;

final class WalletBalanceIsNotEnoughToProceedTransaction extends \Exception
{
    public static function withNumbers(string $walletBalance, string $transactionAmount): self
    {
        return new self(
            sprintf(
                'Wallet balance is not enough to proceed the transaction!
                 Wallet balance: %s, Transaction amount: "%s"',
                $walletBalance,
                $transactionAmount
            )
        );
    }
}
