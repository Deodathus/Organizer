<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\Repository;

use App\Modules\Finance\Wallet\Domain\Entity\Wallet;
use App\Modules\Finance\Wallet\Domain\Exception\WalletDoesNotExist;
use App\Shared\Domain\ValueObject\WalletId;

interface WalletRepository
{
    /**
     * @throws WalletDoesNotExist
     */
    public function fetchById(WalletId $walletId): Wallet;

    public function store(Wallet $wallet): void;
}
