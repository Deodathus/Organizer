<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Transformer;

use App\Modules\Minecraft\Item\DTO\Recipe\IngredientDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\RecipeResultDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\StoreRecipeDTO;
use JetBrains\PhpStorm\Pure;

final class ArrayToRecipeTransformer implements ArrayToRecipeTransformerInterface
{
    #[Pure]
    public function transform(array $data): StoreRecipeDTO
    {
        $ingredients = [];
        $results = [];
        $itemInRecipeIds = [];

        foreach ($data['ingredients'] as $ingredient) {
            $ingredients[] = new IngredientDTO(amount: $ingredient['amount'], itemId: $ingredient['itemId']);

            $itemId = (int) $ingredient['itemId'];
            $itemInRecipeIds[$itemId] = $itemId;
        }

        foreach ($data['results'] as $result) {
            $results[] = new RecipeResultDTO(amount: $result['amount'], itemId: $result['itemId']);

            $itemId = (int) $result['itemId'];
            $itemInRecipeIds[$itemId] = $itemId;
        }

        return new StoreRecipeDTO(
            name: $data['name'],
            ingredients: $ingredients,
            results: $results,
            itemsInRecipeIds: $itemInRecipeIds
        );
    }
}
