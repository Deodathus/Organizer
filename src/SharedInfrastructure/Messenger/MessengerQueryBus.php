<?php

declare(strict_types=1);

namespace App\SharedInfrastructure\Messenger;

use App\Shared\Application\Messenger\Query;
use App\Shared\Application\Messenger\QueryBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class MessengerQueryBus implements QueryBus
{
    public function __construct(private MessageBusInterface $queryBus) {}

    public function handle(Query $query): mixed
    {
        return $this->queryBus->dispatch($query)->last(HandledStamp::class)?->getResult();
    }
}