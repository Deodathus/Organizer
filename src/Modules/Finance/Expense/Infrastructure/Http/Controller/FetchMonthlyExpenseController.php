<?php

namespace App\Modules\Finance\Expense\Infrastructure\Http\Controller;

use App\Modules\Finance\Expense\Application\Query\FetchMonthlyExpense;
use App\Modules\Finance\Expense\Application\ViewModel\MonthlyExpense;
use App\Modules\Finance\Expense\Infrastructure\Http\Request\FetchMonthlyExpenseRequest;
use App\Shared\Application\Messenger\QueryBus;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class FetchMonthlyExpenseController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(FetchMonthlyExpenseRequest $request): JsonResponse
    {
        /** @var MonthlyExpense[] $monthlyExpenses */
        $monthlyExpenses = $this->queryBus->handle(
            new FetchMonthlyExpense(
                $request->requestToken,
                $request->month
            )
        );

        return new JsonResponse(array_map(static fn (MonthlyExpense $expense) => $expense->toArray(), $monthlyExpenses));
    }
}