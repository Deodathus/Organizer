<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Http\Controller;

use App\Modules\Finance\Wallet\Application\Query\FetchWalletTransactions;
use App\Modules\Finance\Wallet\Infrastructure\Http\Request\FetchWalletTransactionsRequest;
use App\Shared\Application\Messenger\QueryBus;
use App\Shared\Application\Result\PaginatedResult;
use App\SharedInfrastructure\Http\Headers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class FetchWalletTransactionsController
{
    public function __construct(
        private QueryBus $queryBus
    ) {
    }

    public function __invoke(FetchWalletTransactionsRequest $request): JsonResponse
    {
        /** @var PaginatedResult $paginatedResult */
        $paginatedResult = $this->queryBus->handle(
            new FetchWalletTransactions(
                $request->walletId,
                $request->requesterToken,
                $request->perPage,
                $request->page
            )
        );

        return new JsonResponse(
            [
                'items' => $paginatedResult->items,
            ],
            Response::HTTP_OK,
            [
                Headers::TOTAL_COUNT_HEADER->value => $paginatedResult->totalCount,
            ]
        );
    }
}
