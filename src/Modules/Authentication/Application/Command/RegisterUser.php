<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Application\Command;

use App\Modules\Authentication\Application\CommandHandler\RegisterUserHandler;
use App\Shared\Application\Messenger\Command;

/**
 * @see RegisterUserHandler
 */
final class RegisterUser implements Command
{
    public function __construct(
        public readonly string $id
    ) {}
}
