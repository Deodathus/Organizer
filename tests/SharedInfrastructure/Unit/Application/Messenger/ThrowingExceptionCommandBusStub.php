<?php
declare(strict_types=1);

namespace App\Tests\SharedInfrastructure\Unit\Application\Messenger;

use App\Shared\Application\Messenger\Command;
use App\Shared\Application\Messenger\CommandBus;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final readonly class ThrowingExceptionCommandBusStub implements CommandBus
{
    public function __construct(
        private \Throwable $throwable
    ) {}

    public function dispatch(Command $command): mixed
    {
        throw new HandlerFailedException(
            new Envelope(new \stdClass()),
            [
                $this->throwable,
            ]
        );
    }
}
