<?php
declare(strict_types=1);

namespace App\Modules\Finance\Expense\Application\Exception;

use App\Shared\Application\Exception\WithPreviousExceptionBase;

final class CannotRegisterExpenseBecauseWalletBalanceIsNotEnoughToProceedException extends WithPreviousExceptionBase
{
}
