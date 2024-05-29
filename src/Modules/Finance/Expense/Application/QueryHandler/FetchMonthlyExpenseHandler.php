<?php

namespace App\Modules\Finance\Expense\Application\QueryHandler;

use App\Modules\Authentication\ModuleAPI\Application\DTO\UserDTO;
use App\Modules\Authentication\ModuleAPI\Application\Exception\UserDoesNotExist;
use App\Modules\Authentication\ModuleAPI\Application\Query\FetchUserIdByToken;
use App\Modules\Finance\Expense\Application\Exception\ExpenseCategoryCreatorDoesNotExistException;
use App\Modules\Finance\Expense\Application\Query\FetchMonthlyExpense;
use App\Modules\Finance\Expense\Application\ReadModel\ExpenseReadModel;
use App\Modules\Finance\Expense\Application\ViewModel\MonthlyExpense;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseId;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseOwnerId;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Query\FetchTransactionsIdsByOwnerAndMonth;
use App\Shared\Application\Messenger\QueryBus;
use App\Shared\Application\Messenger\QueryHandler;

final readonly class FetchMonthlyExpenseHandler implements QueryHandler
{
    public function __construct(
        private ExpenseReadModel $expenseReadModel,
        private QueryBus $queryBus,
    ) {}

    /**
     * @return MonthlyExpense[]
     * @throws ExpenseCategoryCreatorDoesNotExistException
     */
    public function __invoke(FetchMonthlyExpense $query): array
    {
        try {
            /** @var UserDTO $owner */
            $owner = $this->queryBus->handle(new FetchUserIdByToken($query->ownerApiToken));
        } catch (UserDoesNotExist) {
            throw ExpenseCategoryCreatorDoesNotExistException::withToken($query->ownerApiToken);
        }

        $monthlyTransactionsIds = $this->queryBus->handle(
            new FetchTransactionsIdsByOwnerAndMonth(
                $owner->userId,
                $query->month
            )
        );

        return $this->expenseReadModel->fetchMonthlyExpenseByIds(
            array_map(static fn(string $id) => ExpenseId::fromString($id), $monthlyTransactionsIds),
            $query->month,
        );
    }
}