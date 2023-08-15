<?php

declare(strict_types=1);

namespace App\Request;

use Symfony\Component\HttpFoundation\Request as ServerRequest;

/**
 * @deprecated use App\SharedInfrastructure\Http\Request\AbstractRequest instead
 */
abstract class AbstractRequest
{
    abstract public static function fromRequest(ServerRequest $request): self;

    abstract public function toArray(): array;
}
