<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Contract\Recipe\Exception;

use Exception;
use Throwable;

final class RecipeNotFound extends Exception
{
    public static function fromException(Throwable $throwable): self
    {
        return new self($throwable->getMessage(), 0, $throwable);
    }
}