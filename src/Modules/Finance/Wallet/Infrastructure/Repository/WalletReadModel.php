<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Repository;

use App\Modules\Finance\Wallet\Application\ReadModel\WalletReadModel as WalletReadModelInterface;
use App\Modules\Finance\Wallet\Application\ViewModel\WalletViewModel;
use App\Modules\Finance\Wallet\Domain\Repository\WalletRepository;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;

final readonly class WalletReadModel implements WalletReadModelInterface
{
    public function __construct(
        private WalletRepository $walletRepository
    ) {
    }

    public function fetchAll(WalletOwnerExternalId $ownerId): array
    {
        $result = [];

        $wallets = $this->walletRepository->fetchByOwnerExternalId($ownerId);
        foreach ($wallets as $wallet) {
            $result[] = WalletViewModel::fromEntity($wallet);
        }

        return $result;
    }
}
