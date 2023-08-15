<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\ModuleAPI\Application\DTO;

enum SupportedTransactionType: string
{
    case EXPENSE = 'EXPENSE';
}
