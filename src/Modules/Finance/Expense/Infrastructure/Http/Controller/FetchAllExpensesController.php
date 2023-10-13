<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Infrastructure\Http\Controller;

use App\Modules\Finance\Expense\Application\Query\FetchExpensesByOwner;
use App\Modules\Finance\Expense\Infrastructure\Http\Request\FetchAllExpensesRequest;
use App\Shared\Application\Messenger\QueryBus;
use App\Shared\Application\Result\PaginatedResult;
use App\SharedInfrastructure\Http\Headers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class FetchAllExpensesController
{
    public function __construct(
        private QueryBus $queryBus
    ) {
    }

    public function __invoke(FetchAllExpensesRequest $request): JsonResponse
    {
        /** @var PaginatedResult $paginatedResult */
        $paginatedResult = $this->queryBus->handle(
            new FetchExpensesByOwner(
                $request->requesterToken,
                $request->page,
                $request->perPage
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
