<?php
declare(strict_types=1);

namespace App\Modules\Finance\Currency\Domain\ValueObject;

enum CurrencyCode: string
{
    case USD = 'usd';
    case PLN = 'pln';
    case EUR = 'eur';
}
