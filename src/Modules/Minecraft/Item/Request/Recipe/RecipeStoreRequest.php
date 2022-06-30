<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Request\Recipe;

use App\Request\AbstractRequest;
use Assert\Assert;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

final class RecipeStoreRequest extends AbstractRequest
{
    private function __construct(
        public readonly string $name,
        public readonly array $ingredients,
        public readonly array $results
    ) {}

    public static function fromRequest(ServerRequest $request): AbstractRequest
    {
        $requestStack = $request->toArray();

        $name = $requestStack['name'] ?? null;
        $ingredients = $requestStack['ingredients'] ?? null;
        $resultItemId = $requestStack['results'] ?? null;

        Assert::lazy()
            ->that($name, 'name')->string()->maxLength(244)
            ->that($ingredients, 'ingredients')->isArray()
            ->that($resultItemId, 'results')->isArray()
            ->verifyNow();

        return new self($name, $ingredients, $resultItemId);
    }

    #[ArrayShape(['name' => "string", 'ingredients' => "array", 'results' => "array"])]
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'ingredients' => $this->ingredients,
            'results' => $this->results,
        ];
    }
}
