<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\QueryHandler;

use App\Modules\Finance\Wallet\Application\Exception\CannotFindRequesterIdentityException;
use App\Modules\Finance\Wallet\Application\Query\FetchAllWallets;
use App\Modules\Finance\Wallet\Application\ReadModel\WalletReadModel;
use App\Modules\Finance\Wallet\Application\Service\OwnerFetcher;
use App\Modules\Finance\Wallet\Application\ViewModel\WalletViewModel;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;
use App\Shared\Application\Messenger\QueryHandler;

final readonly class FetchAllWalletsHandler implements QueryHandler
{
    public function __construct(
        private WalletReadModel $walletReadModel,
        private OwnerFetcher $ownerFetcher
    ) {
    }

    /**
     * @throws CannotFindRequesterIdentityException
     *
     * @return WalletViewModel[]
     */
    public function __invoke(FetchAllWallets $fetchAllWalletsQuery): array
    {
        $ownerId = $this->ownerFetcher->fetchByToken($fetchAllWalletsQuery->requesterToken)->userId;

        return $this->walletReadModel->fetchAll(
            WalletOwnerExternalId::fromString($ownerId),
            $fetchAllWalletsQuery->perPage,
            $fetchAllWalletsQuery->page
        );
    }
}
