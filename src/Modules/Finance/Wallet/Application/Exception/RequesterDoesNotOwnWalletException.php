<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Exception;

final class RequesterDoesNotOwnWalletException extends \Exception
{
    public static function withRequesterIdAndWalletId(string $requesterId, string $walletId): self
    {
        return new self(
            sprintf(
                'Requester does not own the wallet! Requester id: "%s", wallet id: "%s"',
                $requesterId,
                $walletId
            )
        );
    }
}
