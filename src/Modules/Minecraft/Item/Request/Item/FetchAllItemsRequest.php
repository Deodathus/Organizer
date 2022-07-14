<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Request\Item;

use App\Request\AbstractRequest;
use Assert\Assert;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

final class FetchAllItemsRequest extends AbstractRequest
{
    private function __construct(
        private readonly int $perPage,
        private readonly int $page
    ) {}

    public static function fromRequest(ServerRequest $request): AbstractRequest
    {
        try {
            $requestStack = $request->toArray();
        } catch (JsonException $exception) {}

        $perPage = $requestStack['perPage'] ?? 60;
        $page = $requestStack['page'] ?? 1;

        Assert::lazy()
            ->that($perPage, 'perPage')->integer()->min(1)->max(500)
            ->that($page, 'page')->integer()->min(1)
            ->verifyNow();

        return new self(
            perPage: $perPage,
            page: $page
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

    #[ArrayShape(['perPage' => "int", 'page' => "int"])]
    #[Pure]
    public function toArray(): array
    {
        return [
            'perPage' => $this->getPerPage(),
            'page' => $this->getPage(),
        ];
    }
}
