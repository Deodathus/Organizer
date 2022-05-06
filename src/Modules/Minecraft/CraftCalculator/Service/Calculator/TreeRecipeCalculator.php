<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\Service\Calculator;

use App\Modules\Minecraft\CraftCalculator\DTO\TreeCalculatorResult;
use App\Modules\Minecraft\CraftCalculator\DTO\TreeIngredientDTO;
use App\Modules\Minecraft\CraftCalculator\DTO\TreeRecipeResultDTO;
use App\Modules\Minecraft\CraftCalculator\Entity\Calculable;
use App\Modules\Minecraft\CraftCalculator\Entity\Ingredient;
use App\Modules\Minecraft\CraftCalculator\Entity\RecipeResult;
use Doctrine\Common\Collections\Collection;

final class TreeRecipeCalculator implements TreeRecipeCalculatorInterface
{
    public function calculate(Calculable $calculable, int $amount): TreeCalculatorResult
    {
        $results = $this->calculateRecipeResults($calculable->getResults(), $amount);
        $ingredients = $this->calculateIngredients($calculable->getIngredients(), $amount);

        return new TreeCalculatorResult(
            calculableId: $calculable->getId(),
            results: $results,
            ingredients: $ingredients
        );
    }

    /**
     * @param Collection<RecipeResult> $recipeResults
     * @return array
     */
    private function calculateRecipeResults(Collection $recipeResults, int $amount): array
    {
        $result = [];

        foreach ($recipeResults as $recipeResult) {
            $result[] = new TreeRecipeResultDTO(
                amount: $amount,
                itemId: $recipeResult->getItemId(),
                itemName: $recipeResult->getItemName()
            );
        }

        return $result;
    }

    /**
     * @param Collection<Ingredient> $ingredients
     */
    private function calculateIngredients(Collection $ingredients, int $amount): array
    {
        $result = [];

        foreach ($ingredients as $ingredient) {
            $calculatedIngredients = [];

            $ingredientAmount = $ingredient->getAmount() * $amount;
            $calculatedIngredients[] = $ingredient->getId();

            $result[] = new TreeIngredientDTO(
                amount: $ingredientAmount,
                itemId: $ingredient->getItemId(),
                itemName: $ingredient->getItemName(),
                asResult: $this->calculateTreeForIngredient($ingredient, $ingredientAmount, $calculatedIngredients)
            );
        }

        return $result;
    }

    private function calculateTreeForIngredient(Ingredient $ingredient, float $amount, array $calculatedIngredients): array
    {
        $tree = [];

        /** @var RecipeResult $asRecipeResult */
        foreach ($ingredient->getAsRecipeResult() as $asRecipeResult) {
            $recipe = $asRecipeResult->getRecipe();
            $ingredients = $recipe->getIngredients();

            /** @var Ingredient $subIngredient */
            foreach ($ingredients as $subIngredient) {
                if (in_array($subIngredient->getId(), $calculatedIngredients, true)) {
                    continue;
                }

                $amountForSubIngredient = $amount * $subIngredient->getAmount();
                $calculatedIngredients[] = $subIngredient->getId();

                $treeForSubIngredient = $this->calculateTreeForIngredient($subIngredient, $amountForSubIngredient, $calculatedIngredients);

                $tree[] = new TreeIngredientDTO(
                    amount: $amountForSubIngredient,
                    itemId: $subIngredient->getItemId(),
                    itemName: $subIngredient->getItemName(),
                    asResult: $treeForSubIngredient
                );
            }
        }

        return $tree;
    }
}