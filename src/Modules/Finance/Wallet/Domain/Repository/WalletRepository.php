<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\Repository;

use App\Modules\Finance\Wallet\Domain\Entity\Wallet;
use App\Modules\Finance\Wallet\Domain\Exception\WalletDoesNotExist;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrency;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;
use App\Shared\Domain\ValueObject\WalletId;

interface WalletRepository
{
    /**
     * @throws WalletDoesNotExist
     */
    public function fetchById(WalletId $walletId): Wallet;

    public function store(Wallet $wallet): void;

    /**
     * @return Wallet[]
     */
    public function fetchByOwnerExternalId(WalletOwnerExternalId $ownerId, int $perPage = 10, int $page = 1): array;

    /**
     * @throws WalletDoesNotExist
     */
    public function fetchByIdAndOwnerExternalId(WalletId $walletId, WalletOwnerExternalId $ownerId): Wallet;

    /**
     * @throws WalletDoesNotExist
     */
    public function fetchWalletCurrency(WalletId $walletId): WalletCurrency;

    public function doesWalletBelongToOwner(WalletId $walletId, WalletOwnerExternalId $ownerId): bool;
}
