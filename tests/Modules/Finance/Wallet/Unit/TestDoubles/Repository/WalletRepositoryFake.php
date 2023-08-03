<?php
declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\Repository;

use App\Modules\Finance\Wallet\Application\Exception\WalletDoesNotExistException;
use App\Modules\Finance\Wallet\Domain\Entity\Wallet;
use App\Modules\Finance\Wallet\Domain\Repository\WalletRepository;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletId;

final class WalletRepositoryFake implements WalletRepository
{
    /** @var Wallet[] $storedWallets */
    private array $storedWallets = [];

    public function store(Wallet $wallet): void
    {
        $this->storedWallets[$wallet->getId()->toString()] = $wallet;
    }

    public function findStored(WalletId $walletId): Wallet
    {
        foreach ($this->storedWallets as $wallet) {
            if ($wallet->getId()->toString() === $walletId->toString()) {
                return $this->storedWallets[$walletId->toString()];
            }
        }

        throw WalletDoesNotExistException::withId($walletId->toString());
    }
}
