<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Application\QueryHandler;

use App\Modules\Authentication\ModuleAPI\Application\DTO\UserDTO;
use App\Modules\Authentication\ModuleAPI\Application\Exception\UserDoesNotExist;
use App\Modules\Authentication\ModuleAPI\Application\Query\FetchUserIdByToken;
use App\Modules\Finance\Expense\Application\Exception\ExpenseCategoryCreatorDoesNotExistException;
use App\Modules\Finance\Expense\Application\Query\FetchExpensesByOwner;
use App\Modules\Finance\Expense\Application\ReadModel\ExpenseReadModel;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseOwnerId;
use App\Shared\Application\Messenger\QueryBus;
use App\Shared\Application\Messenger\QueryHandler;
use App\Shared\Application\Result\PaginatedResult;

final readonly class FetchExpensesByOwnerHandler implements QueryHandler
{
    public function __construct(
        private ExpenseReadModel $expenseReadModel,
        private QueryBus $queryBus
    ) {
    }

    public function __invoke(FetchExpensesByOwner $query): PaginatedResult
    {
        try {
            /** @var UserDTO $owner */
            $owner = $this->queryBus->handle(new FetchUserIdByToken($query->ownerApiToken));
        } catch (UserDoesNotExist) {
            throw ExpenseCategoryCreatorDoesNotExistException::withToken($query->ownerApiToken);
        }

        return $this->expenseReadModel->fetchByOwner(
            ExpenseOwnerId::fromString(
                $owner->userId
            ),
            $query->page,
            $query->perPage
        );
    }
}
