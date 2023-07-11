<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\Repository;

use App\Modules\Finance\Wallet\Domain\Entity\Wallet;

interface WalletRepository
{
    public function store(Wallet $wallet): void;
}
