<?php
declare(strict_types=1);

namespace App\SharedInfrastructure\Http\Response;

use Assert\InvalidArgumentException;

final class ValidationErrorResponse
{
    public static function getResponseContent(InvalidArgumentException ...$errors): array
    {
        return array_map(
            static fn (InvalidArgumentException $error): array => [
                'property' => $error->getPropertyPath(),
                'error' => $error->getMessage(),
            ],
            $errors
        );
    }
}
