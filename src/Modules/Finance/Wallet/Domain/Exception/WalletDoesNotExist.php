<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\Exception;

final class WalletDoesNotExist extends \Exception
{
    public static function withId(string $id): self
    {
        return new self(
            sprintf('Wallet with given id does not exist! Given id: "%s"', $id)
        );
    }
}
