<?php

declare(strict_types=1);

namespace App\Tests\SharedInfrastructure\Unit\Application\Messenger;

use App\Shared\Application\Messenger\Command;
use App\Shared\Application\Messenger\CommandBus;

final class CommandBusStub implements CommandBus
{
    /**
     * @var array<string, bool> $handled
     */
    private array $handled = [];

    public function dispatch(Command $command): null
    {
        $this->handled[$command::class] = true;

        return null;
    }

    public function wasHandled(string $command): bool
    {
        if (array_key_exists($command, $this->handled)) {
            return $this->handled[$command];
        }

        return false;
    }
}
