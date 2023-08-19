<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Transformer;

use App\Modules\Minecraft\Item\DTO\Recipe\IngredientDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\IngredientItemDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\RecipeResultDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\StoreRecipeDTO;
use Doctrine\Common\Collections\ArrayCollection;

final class ArrayToRecipeTransformer implements ArrayToRecipeTransformerInterface
{
    public function transform(array $data): StoreRecipeDTO
    {
        $ingredients = [];
        $results = [];
        $itemInRecipeIds = [];

        foreach ($data['ingredients'] as $ingredientItems) {
            $preparedIngredientItems = [];

            foreach ($ingredientItems as $ingredientItem) {
                $preparedIngredientItems[] = new IngredientItemDTO(
                    amount: $ingredientItem['amount'],
                    itemId: $ingredientItem['itemId']
                );

                $itemId = (int) $ingredientItem['itemId'];
                $itemInRecipeIds[$itemId] = $itemId;
            }

            $ingredients[] = new IngredientDTO(items: new ArrayCollection($preparedIngredientItems));
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
