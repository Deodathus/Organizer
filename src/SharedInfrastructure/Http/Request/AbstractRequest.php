<?php

declare(strict_types=1);

namespace App\SharedInfrastructure\Http\Request;

use Symfony\Component\HttpFoundation\Request as ServerRequest;

abstract class AbstractRequest
{
    abstract public static function fromRequest(ServerRequest $request): self;

    abstract public function toArray(): array;
}
