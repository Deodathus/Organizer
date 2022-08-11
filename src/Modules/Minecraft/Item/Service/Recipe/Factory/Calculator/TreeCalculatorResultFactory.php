<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Recipe\Factory\Calculator;

use App\Modules\Minecraft\CraftCalculator\DTO\TreeCalculatorResult as ExternalCalculatorResult;
use App\Modules\Minecraft\Item\DTO\Recipe\CalculatedTree\TreeCalculatorResult;
use App\Modules\Minecraft\Item\DTO\Recipe\CalculatedTree\TreeIngredientDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\CalculatedTree\TreeIngredientItemDTO;
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
            $treeIngredientItems = [];

            foreach ($ingredient->getItems() as $item) {
                $treeIngredientItems[] = new TreeIngredientItemDTO(
                    amount: $item->getAmount(),
                    itemId: $item->getItemId(),
                    itemName: $item->getItemName(),
                    asResult: $item->getAsResult()
                );
            }

            $ingredients[] = new TreeIngredientDTO($treeIngredientItems);
        }

        return new TreeCalculatorResult(
            calculableId: $calculatorResult->getCalculableId(),
            results: $results,
            ingredients: $ingredients
        );
    }
}
