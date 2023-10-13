<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Infrastructure\Http\Controller;

use App\Modules\Finance\Expense\Application\Query\FetchExpenseCategoriesByOwner;
use App\Modules\Finance\Expense\Infrastructure\Http\Request\FetchAllExpenseCategoriesRequest;
use App\Shared\Application\Messenger\QueryBus;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class FetchAllExpenseCategoriesController
{
    public function __construct(
        private QueryBus $queryBus
    ) {
    }

    public function __invoke(FetchAllExpenseCategoriesRequest $request): JsonResponse
    {
        return new JsonResponse([
            'items' => $this->queryBus->handle(new FetchExpenseCategoriesByOwner($request->requesterToken)),
        ]);
    }
}
