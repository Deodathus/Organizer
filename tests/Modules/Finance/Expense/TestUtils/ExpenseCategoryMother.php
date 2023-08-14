<?php
declare(strict_types=1);

namespace App\Tests\Modules\Finance\Expense\TestUtils;

use App\Modules\Finance\Expense\Domain\Entity\ExpenseCategory;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryOwnerId;

final readonly class ExpenseCategoryMother
{
    public const CATEGORY_NAME = 'Test';

    public static function create(ExpenseCategoryOwnerId $ownerId): ExpenseCategory
    {
        return ExpenseCategory::create(
            $ownerId,
            self::CATEGORY_NAME
        );
    }
}
