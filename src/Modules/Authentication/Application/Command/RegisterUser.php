<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Application\Command;

use App\Shared\Application\Messenger\Command;

final class RegisterUser implements Command
{
    public function __construct(
        public readonly string $id
    ) {}
}
