<?php

declare(strict_types=1);

namespace App\Modules\Finance\Currency\Domain\ValueObject;

enum CurrencyCode: string
{
    case USD = 'USD';
    case PLN = 'PLN';
    case EUR = 'EUR';
    case CAD = 'CAD';
}
