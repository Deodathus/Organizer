<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Application\Command;

use App\Shared\Application\Messenger\Command;

class LogInUser implements Command
{
    public function __construct(
        private readonly string $token
    ) {}
}
