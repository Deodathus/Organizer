<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\Exception;

use Money\Money;

final class WalletBalanceIsNotEnoughToProceedTransaction extends \Exception
{
    public static function withNumbers(Money $walletBalance, Money $transactionAmount): self
    {
        return new self(
            sprintf(
                'Wallet balance is not enough to proceed the transaction!
                 Wallet balance: %s, Transaction amount: "%s"',
                $walletBalance->getAmount(),
                $transactionAmount->getAmount()
            )
        );
    }
}
