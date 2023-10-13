<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Infrastructure\Http\Controller;

use App\Modules\Finance\Expense\Application\Query\FetchExpensesByOwner;
use App\Modules\Finance\Expense\Infrastructure\Http\Request\FetchAllExpensesRequest;
use App\Shared\Application\Messenger\QueryBus;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class FetchAllExpensesController
{
    public function __construct(
        private QueryBus $queryBus
    ) {
    }

    public function __invoke(FetchAllExpensesRequest $request): JsonResponse
    {
        return new JsonResponse([
            'items' => $this->queryBus->handle(
                new FetchExpensesByOwner(
                    $request->requesterToken,
                    $request->page,
                    $request->perPage
                )
            ),
        ]);
    }
}
