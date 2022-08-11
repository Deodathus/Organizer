<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Recipe\Factory\Calculator;

use App\Modules\Minecraft\CraftCalculator\DTO\CalculatorResult as ExternalCalculatorResult;
use App\Modules\Minecraft\Item\DTO\Recipe\Calculated\CalculatorResult;
use App\Modules\Minecraft\Item\DTO\Recipe\Calculated\IngredientDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\Calculated\IngredientItemDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\Calculated\RecipeResultDTO;

final class CalculatorResultFactory
{
    public static function buildFromExternalResult(ExternalCalculatorResult $calculatorResult): CalculatorResult
    {
        $ingredients = [];
        foreach ($calculatorResult->getIngredients() as $ingredient) {
            $ingredientItems = [];

            foreach ($ingredient->getItems() as $item) {
                $ingredientItems[] = new IngredientItemDTO(
                    $item->getId(),
                    $item->getAmount(),
                    $item->getItemId(),
                    $item->getItemName()
                );
            }

            $ingredients[] = new IngredientDTO($ingredientItems);
        }

        $recipeResults = [];
        foreach ($calculatorResult->getResults() as $recipeResult) {
            $recipeResults[] = new RecipeResultDTO(
                id: $recipeResult->getId(),
                amount: $recipeResult->getAmount(),
                itemId: $recipeResult->getItemId(),
                itemName: $recipeResult->getItemName()
            );
        }

        return new CalculatorResult(
            calculableId: $calculatorResult->getCalculableId(),
            ingredients: $ingredients,
            results: $recipeResults
        );
    }
}
