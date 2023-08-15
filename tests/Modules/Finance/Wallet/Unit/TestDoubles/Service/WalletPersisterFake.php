<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\Service;

use App\Modules\Finance\Wallet\Application\DTO\WalletDTO;
use App\Modules\Finance\Wallet\Application\Exception\WalletDoesNotExistException;
use App\Modules\Finance\Wallet\Application\Service\WalletPersisterInterface;
use App\Shared\Domain\ValueObject\WalletId;

final class WalletPersisterFake implements WalletPersisterInterface
{
    /** @var WalletDTO[] */
    private array $persisted = [];

    public function persist(WalletDTO $wallet): WalletId
    {
        $walletId = WalletId::generate();

        $this->persisted[$walletId->toString()] = $wallet;

        return $walletId;
    }

    public function findPersisted(WalletId $walletId): WalletDTO
    {
        if (array_key_exists($walletId->toString(), $this->persisted)) {
            return $this->persisted[$walletId->toString()];
        }

        throw WalletDoesNotExistException::withId($walletId->toString());
    }
}
