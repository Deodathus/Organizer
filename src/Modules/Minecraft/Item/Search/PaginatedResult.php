<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Search;

use Doctrine\ORM\Tools\Pagination\Paginator;

/** @deprecated  */
final class PaginatedResult
{
    public function __construct(
        private readonly Paginator $result,
        private readonly int $totalPages,
        private readonly int $totalCount
    ) {
    }

    public function getResult(): Paginator
    {
        return $this->result;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }
}
