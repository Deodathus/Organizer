<?php

declare(strict_types=1);

namespace App\Shared\Application\Result;

final readonly class PaginatedResult
{
    /**
     * @param array<mixed> $items
     */
    public function __construct(
        public array $items,
        public int $totalCount
    ) {}
}
