<?php

declare(strict_types=1);

namespace App\Modules\Finance\Currency\ModuleAPI\Application\Enum;

enum SupportedCurrencies: string
{
    case PLN = 'PLN';
    case USD = 'USD';
    case EUR = 'EUR';
}
