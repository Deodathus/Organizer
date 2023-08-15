<?php

declare(strict_types=1);

namespace App\Shared\Application\Messenger;

interface CommandBus
{
    public function dispatch(Command $command): mixed;
}
