<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Exception;

final class WalletWithoutOwnersException extends \Exception
{
    public static function withId(string $walletId): self
    {
        return new self(
            sprintf('There is a wallet with no owners related to it! Wallet id: "%s"', $walletId)
        );
    }
}
