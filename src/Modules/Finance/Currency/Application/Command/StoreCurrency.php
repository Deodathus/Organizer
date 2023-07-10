<?php
declare(strict_types=1);

namespace App\Modules\Finance\Currency\Application\Command;

use App\Shared\Application\Messenger\Command;

final class StoreCurrency implements Command
{
    public function __construct(
        public readonly string $code
    ) {}
}
