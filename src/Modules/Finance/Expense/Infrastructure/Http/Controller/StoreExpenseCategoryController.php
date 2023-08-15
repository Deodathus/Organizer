<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Infrastructure\Http\Controller;

use App\Modules\Finance\Expense\Application\Command\StoreExpenseCategory;
use App\Modules\Finance\Expense\Application\DTO\CreatedExpenseCategory;
use App\Modules\Finance\Expense\Infrastructure\Http\Request\StoreExpenseCategoryRequest;
use App\Shared\Application\Messenger\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class StoreExpenseCategoryController
{
    public function __construct(
        private CommandBus $commandBus
    ) {
    }

    public function __invoke(StoreExpenseCategoryRequest $request): JsonResponse
    {
        /** @var CreatedExpenseCategory $createdCategoryId */
        $createdCategoryId = $this->commandBus->dispatch(
            new StoreExpenseCategory($request->creatorToken, $request->name)
        );

        return new JsonResponse(
            [
                'id' => $createdCategoryId->id,
            ],
            Response::HTTP_CREATED
        );
    }
}
