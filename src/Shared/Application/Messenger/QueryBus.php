<?php

declare(strict_types=1);

namespace App\Shared\Application\Messenger;

interface QueryBus
{
    public function handle(Query $query): mixed;
}
