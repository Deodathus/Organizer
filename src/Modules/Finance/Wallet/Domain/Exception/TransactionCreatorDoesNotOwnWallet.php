<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\Exception;

final class TransactionCreatorDoesNotOwnWallet extends \Exception
{
    public static function withId(string $transactionCreatorId): self
    {
        return new self(
            sprintf(
                'Transaction creator does not own the wallet! Transaction creator id: "%s"',
                $transactionCreatorId
            )
        );
    }
}
