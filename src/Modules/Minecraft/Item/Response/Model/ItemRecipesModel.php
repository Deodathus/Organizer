<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Response\Model;

use JetBrains\PhpStorm\ArrayShape;

final class ItemRecipesModel
{
    /**
     * @param RecipeModel[] $asIngredient
     * @param RecipeModel[] $asResult
     */
    public function __construct(
        private readonly int $itemId,
        private readonly array $asIngredient,
        private readonly array $asResult
    ) {
    }

    public function getItemId(): int
    {
        return $this->itemId;
    }

    public function getAsIngredient(): array
    {
        return $this->asIngredient;
    }

    public function getAsResult(): array
    {
        return $this->asResult;
    }

    #[ArrayShape(['itemId' => 'int', 'recipes' => 'array[]'])]
    public function toArray(): array
    {
        $asIngredient = [];
        $asResult = [];

        foreach ($this->getAsIngredient() as $ingredientRecipe) {
            $asIngredient[] = $ingredientRecipe->toArray();
        }

        foreach ($this->getAsResult() as $resultRecipe) {
            $asResult[] = $resultRecipe->toArray();
        }

        return [
            'itemId' => $this->getItemId(),
            'recipes' => [
                'asIngredient' => $asIngredient,
                'asResult' => $asResult,
            ],
        ];
    }
}
