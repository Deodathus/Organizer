<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\QueryHandler;

use App\Modules\Finance\Wallet\Application\Exception\CannotFindRequesterIdentityException;
use App\Modules\Finance\Wallet\Application\Exception\RequesterDoesNotOwnWalletException;
use App\Modules\Finance\Wallet\Application\Exception\WalletDoesNotExistException;
use App\Modules\Finance\Wallet\Application\Query\FetchWalletTransactions;
use App\Modules\Finance\Wallet\Application\ReadModel\TransactionReadModel;
use App\Modules\Finance\Wallet\Application\Service\OwnerFetcher;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;
use App\Shared\Application\Messenger\QueryHandler;
use App\Shared\Application\Result\PaginatedResult;
use App\Shared\Domain\ValueObject\WalletId;

final readonly class FetchWalletTransactionsHandler implements QueryHandler
{
    public function __construct(
        private TransactionReadModel $transactionReadModel,
        private OwnerFetcher $ownerFetcher
    ) {
    }

    /**
     * @throws RequesterDoesNotOwnWalletException
     * @throws CannotFindRequesterIdentityException
     * @throws WalletDoesNotExistException
     */
    public function __invoke(FetchWalletTransactions $fetchWalletTransactionsQuery): PaginatedResult
    {
        return $this->transactionReadModel->fetchByWallet(
            WalletOwnerExternalId::fromString(
                $this->ownerFetcher->fetchByToken($fetchWalletTransactionsQuery->requesterToken)->userId
            ),
            WalletId::fromString($fetchWalletTransactionsQuery->walletId),
            $fetchWalletTransactionsQuery->perPage,
            $fetchWalletTransactionsQuery->page
        );
    }
}
