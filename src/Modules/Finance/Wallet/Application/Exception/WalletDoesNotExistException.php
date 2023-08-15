<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Exception;

final class WalletDoesNotExistException extends \Exception
{
    public static function withId(string $id): self
    {
        return new self(
            sprintf('Wallet with given id does not exist! Given id: "%s"', $id)
        );
    }

    public static function withIdAndRequesterId(string $id, string $requesterId): self
    {
        return new self(
            sprintf(
                'Wallet with given id does not exist or requester does not own it! Given id: "%s", requester id: "%s"',
                $id,
                $requesterId
            )
        );
    }
}
