<?php
declare(strict_types=1);

namespace App\Modules\Finance\Expense\Infrastructure\Http\Controller;

use App\Modules\Finance\Expense\Application\Command\StoreExpense;
use App\Modules\Finance\Expense\Application\DTO\CreatedExpense;
use App\Modules\Finance\Expense\Infrastructure\Http\Request\StoreExpenseRequest;
use App\Shared\Application\Messenger\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class StoreExpenseController
{
    public function __construct(
        private CommandBus $commandBus
    ) {}

    public function __invoke(StoreExpenseRequest $storeExpenseRequest): JsonResponse
    {
        /** @var CreatedExpense $createdExpense */
        $createdExpense = $this->commandBus->dispatch(
            new StoreExpense(
                $storeExpenseRequest->walletId,
                $storeExpenseRequest->ownerApiToken,
                $storeExpenseRequest->categoryId,
                $storeExpenseRequest->amount,
                $storeExpenseRequest->currencyCode,
                $storeExpenseRequest->comment
            )
        );

        return new JsonResponse(
            [
                'id' => $createdExpense->expenseId,
            ],
            Response::HTTP_CREATED
        );
    }
}
