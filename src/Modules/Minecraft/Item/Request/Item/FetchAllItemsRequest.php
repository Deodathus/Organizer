<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Request\Item;

use App\Request\AbstractRequest;
use Assert\Assert;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

final class FetchAllItemsRequest extends AbstractRequest
{
    private function __construct(
        private readonly int $perPage,
        private readonly int $page,
        private readonly ?string $searchPhrase
    ) {
    }

    public static function fromRequest(ServerRequest $request): AbstractRequest
    {
        $perPage = (int) ($request->query->get('perPage') ?? 60);
        $page = (int) ($request->query->get('page') ?? 1);
        $searchPhrase = $request->query->get('searchPhrase');

        Assert::lazy()
            ->that($perPage, 'perPage')->integer()->min(1)->max(500)
            ->that($page, 'page')->integer()->min(1)
            ->verifyNow();

        return new self(
            perPage: $perPage,
            page: $page,
            searchPhrase: $searchPhrase
        );
    }

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

    #[ArrayShape(['perPage' => 'int', 'page' => 'int', 'searchPhrase' => 'null|string'])]
    #[Pure]
    public function toArray(): array
    {
        return [
            'perPage' => $this->getPerPage(),
            'page' => $this->getPage(),
            'searchPhrase' => $this->getSearchPhrase(),
        ];
    }
}
