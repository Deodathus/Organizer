<?php

declare(strict_types=1);

namespace App\SharedInfrastructure\Messenger;

use App\Shared\Application\Messenger\Command;
use App\Shared\Application\Messenger\CommandBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class MessengerCommandBus implements CommandBus
{
    public function __construct(private readonly MessageBusInterface $commandBus) {}

    public function dispatch(Command $command): mixed
    {
        return $this->commandBus->dispatch($command)->last(HandledStamp::class)?->getResult();
    }
}