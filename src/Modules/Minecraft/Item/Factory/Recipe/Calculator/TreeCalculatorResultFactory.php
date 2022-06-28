<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Factory\Recipe\Calculator;

use App\Modules\Minecraft\CraftCalculator\DTO\TreeCalculatorResult as ExternalCalculatorResult;
use App\Modules\Minecraft\Item\DTO\Recipe\CalculatedTree\TreeCalculatorResult;
use App\Modules\Minecraft\Item\DTO\Recipe\CalculatedTree\TreeIngredientDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\CalculatedTree\TreeRecipeResultDTO;

final class TreeCalculatorResultFactory
{
    public static function buildFromExternalResult(ExternalCalculatorResult $calculatorResult): TreeCalculatorResult
    {
        $results = [];
        foreach ($calculatorResult->getRecipeResults() as $recipeResult) {
            $results[] = new TreeRecipeResultDTO(
                amount: $recipeResult->getAmount(),
                itemId: $recipeResult->getItemId(),
                itemName: $recipeResult->getItemName()
            );
        }

        $ingredients = [];
        foreach ($calculatorResult->getIngredients() as $ingredient) {
            $ingredients[] = new TreeIngredientDTO(
                amount: $ingredient->getAmount(),
                itemId: $ingredient->getItemId(),
                itemName: $ingredient->getItemName(),
                asResult: $ingredient->getAsResult()
            );
        }

        return new TreeCalculatorResult(
            calculableId: $calculatorResult->getCalculableId(),
            results: $results,
            ingredients: $ingredients
        );
    }
}
