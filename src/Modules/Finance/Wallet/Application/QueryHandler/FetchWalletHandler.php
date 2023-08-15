<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\QueryHandler;

use App\Modules\Finance\Wallet\Application\Query\FetchWallet;
use App\Modules\Finance\Wallet\Application\ReadModel\WalletReadModel;
use App\Modules\Finance\Wallet\Application\Service\OwnerFetcher;
use App\Modules\Finance\Wallet\Application\ViewModel\WalletViewModel;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;
use App\Shared\Application\Messenger\QueryHandler;
use App\Shared\Domain\ValueObject\WalletId;

final readonly class FetchWalletHandler implements QueryHandler
{
    public function __construct(
        private WalletReadModel $walletReadModel,
        private OwnerFetcher $ownerFetcher
    ) {
    }

    public function __invoke(FetchWallet $fetchWalletQuery): WalletViewModel
    {
        $ownerId = $this->ownerFetcher->fetchByToken($fetchWalletQuery->requesterToken)->userId;

        return $this->walletReadModel->fetchOne(
            WalletId::fromString($fetchWalletQuery->walletId),
            WalletOwnerExternalId::fromString($ownerId)
        );
    }
}
