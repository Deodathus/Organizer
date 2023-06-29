<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Application\Query;

use App\Shared\Application\Messenger\Query;

final class GetMe implements Query
{
    public function __construct(
        public readonly string $token
    ) {}
}
