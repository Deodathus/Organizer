<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\ModuleAPI\Application\Enum;

enum SupportedTransactionType: string
{
    case EXPENSE = 'EXPENSE';
}
