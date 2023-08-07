<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\Service;

use App\Modules\Finance\Wallet\Application\DTO\WalletDTO;
use App\Shared\Domain\ValueObject\WalletId;

interface WalletPersisterInterface
{
    public function persist(WalletDTO $wallet): WalletId;
}
