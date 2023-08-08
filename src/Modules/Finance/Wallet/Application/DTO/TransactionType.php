<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\DTO;

enum TransactionType: string
{
    case DEPOSIT = 'DEPOSIT';
    case WITHDRAW = 'WITHDRAW';
    case EXPENSE = 'EXPENSE';
    case TRANSFER_INCOME = 'TRANSFER_INCOME';
    case TRANSFER_CHARGE = 'TRANSFER_CHARGE';
}
