<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Search;

final class FilterBus
{
    public function __construct(
        private readonly int $perPage,
        private readonly int $page,
        private readonly ?string $searchPhrase
    ) {}

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getSearchPhrase(): ?string
    {
        return $this->searchPhrase;
    }
}