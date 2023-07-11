<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Service;

use App\Modules\Finance\Wallet\Application\DTO\WalletDTO;
use App\Modules\Finance\Wallet\Domain\Entity\Wallet;
use App\Modules\Finance\Wallet\Domain\Entity\WalletOwner;
use App\Modules\Finance\Wallet\Domain\Repository\WalletRepository;
use App\Modules\Finance\Wallet\Domain\Service\WalletPersisterInterface;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletBalance;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrencyId;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletId;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;
use Money\Currency;
use Money\Money;

final class WalletPersister implements WalletPersisterInterface
{
    public function __construct(
        private readonly WalletRepository $repository
    ) {}

    public function persist(WalletDTO $wallet): WalletId
    {
        $owner = WalletOwner::create(
            WalletOwnerExternalId::fromString($wallet->creatorId)
        );

        $createdWallet = Wallet::create(
            $wallet->name,
            [$owner],
            new WalletBalance(new Money($wallet->startBalance, new Currency($wallet->currencyCode))),
            WalletCurrencyId::fromString($wallet->currencyId)
        );

        $this->repository->store($createdWallet);

        return $createdWallet->getId();
    }
}
