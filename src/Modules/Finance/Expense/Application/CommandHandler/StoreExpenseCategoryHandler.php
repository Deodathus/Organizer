<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Application\CommandHandler;

use App\Modules\Authentication\ModuleAPI\Application\DTO\UserDTO;
use App\Modules\Authentication\ModuleAPI\Application\Exception\UserDoesNotExist;
use App\Modules\Authentication\ModuleAPI\Application\Query\FetchUserIdByToken;
use App\Modules\Finance\Expense\Application\Command\StoreExpenseCategory;
use App\Modules\Finance\Expense\Application\DTO\CreatedExpenseCategory;
use App\Modules\Finance\Expense\Application\Exception\ExpenseCategoryCreatorDoesNotExistException;
use App\Modules\Finance\Expense\Domain\Entity\ExpenseCategory;
use App\Modules\Finance\Expense\Domain\Repository\ExpenseCategoryRepository;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryOwnerId;
use App\Shared\Application\Messenger\CommandHandler;
use App\Shared\Application\Messenger\QueryBus;

final readonly class StoreExpenseCategoryHandler implements CommandHandler
{
    public function __construct(
        private ExpenseCategoryRepository $expenseCategoryRepository,
        private QueryBus $queryBus
    ) {
    }

    public function __invoke(StoreExpenseCategory $storeExpenseCategoryCommand): CreatedExpenseCategory
    {
        try {
            /** @var UserDTO $expenseCategoryOwner */
            $expenseCategoryOwner = $this->queryBus->handle(
                new FetchUserIdByToken($storeExpenseCategoryCommand->ownerApiToken)
            );
        } catch (UserDoesNotExist $exception) {
            throw ExpenseCategoryCreatorDoesNotExistException::withToken($storeExpenseCategoryCommand->ownerApiToken);
        }

        $expenseCategory = ExpenseCategory::create(
            ExpenseCategoryOwnerId::fromString($expenseCategoryOwner->userId),
            $storeExpenseCategoryCommand->name
        );

        $this->expenseCategoryRepository->store($expenseCategory);

        return new CreatedExpenseCategory($expenseCategory->getId()->toString());
    }
}
