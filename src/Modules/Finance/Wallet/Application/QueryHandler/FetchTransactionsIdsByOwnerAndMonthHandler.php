<?php

namespace App\Modules\Finance\Wallet\Application\QueryHandler;

use App\Modules\Finance\Wallet\Domain\Repository\TransactionRepository;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionCreator;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Query\FetchTransactionsIdsByOwnerAndMonth;
use App\Shared\Application\Messenger\QueryHandler;

final readonly class FetchTransactionsIdsByOwnerAndMonthHandler implements QueryHandler
{
    public function __construct(
        private TransactionRepository $transactionRepository,
    ) {}

    /** @return array<string> */
    public function __invoke(FetchTransactionsIdsByOwnerAndMonth $query): array
    {
        return $this->transactionRepository->fetchTransactionsIdsByOwnerAndMonth(
            TransactionCreator::fromString($query->ownerId),
            $query->month,
        );
    }
}