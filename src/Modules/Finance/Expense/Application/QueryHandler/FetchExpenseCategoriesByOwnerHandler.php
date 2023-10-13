<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Application\QueryHandler;

use App\Modules\Authentication\ModuleAPI\Application\DTO\UserDTO;
use App\Modules\Authentication\ModuleAPI\Application\Exception\UserDoesNotExist;
use App\Modules\Authentication\ModuleAPI\Application\Query\FetchUserIdByToken;
use App\Modules\Finance\Expense\Application\Exception\ExpenseCategoryCreatorDoesNotExistException;
use App\Modules\Finance\Expense\Application\Query\FetchExpenseCategoriesByOwner;
use App\Modules\Finance\Expense\Application\ReadModel\ExpenseCategoryReadModel;
use App\Modules\Finance\Expense\Application\ViewModel\ExpenseCategoryViewModel;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryOwnerId;
use App\Shared\Application\Messenger\QueryBus;
use App\Shared\Application\Messenger\QueryHandler;

final readonly class FetchExpenseCategoriesByOwnerHandler implements QueryHandler
{
    public function __construct(
        private ExpenseCategoryReadModel $categoryReadModel,
        private QueryBus $queryBus
    ) {
    }

    /**
     * @return ExpenseCategoryViewModel[]
     */
    public function __invoke(FetchExpenseCategoriesByOwner $query): array
    {
        try {
            /** @var UserDTO $owner */
            $owner = $this->queryBus->handle(new FetchUserIdByToken($query->ownerApiToken));
        } catch (UserDoesNotExist) {
            throw ExpenseCategoryCreatorDoesNotExistException::withToken($query->ownerApiToken);
        }

        return $this->categoryReadModel->fetchAllByOwner(ExpenseCategoryOwnerId::fromString($owner->userId));
    }
}
