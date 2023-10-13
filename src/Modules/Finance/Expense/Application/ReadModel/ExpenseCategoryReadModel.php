<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Application\ReadModel;

use App\Modules\Finance\Expense\Application\ViewModel\ExpenseCategoryViewModel;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryOwnerId;

interface ExpenseCategoryReadModel
{
    /**
     * @return ExpenseCategoryViewModel[]
     */
    public function fetchAllByOwner(ExpenseCategoryOwnerId $ownerId): array;
}
