<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\Service\Calculator;

use App\Modules\Minecraft\CraftCalculator\DTO\TreeCalculatorResult;
use App\Modules\Minecraft\CraftCalculator\DTO\TreeIngredientDTO;
use App\Modules\Minecraft\CraftCalculator\Entity\Calculable;
use App\Modules\Minecraft\CraftCalculator\Entity\Ingredient;
use App\Modules\Minecraft\Item\Entity\RecipeResult;
use Doctrine\Common\Collections\Collection;

final class TreeRecipeCalculator implements TreeRecipeCalculatorInterface
{
    public function calculate(Calculable $calculable, int $amount): TreeCalculatorResult
    {
        $ingredients = $this->calculateIngredients($calculable->getIngredients(), $amount);

        return new TreeCalculatorResult($calculable->getId(), $ingredients);
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
            $calculatedIngredients[] = $ingredient;

            $result[] = new TreeIngredientDTO(
                amount: $ingredientAmount,
                itemId: $ingredient->getItemId(),
                asResult: $this->calculateTreeForIngredient($ingredient, $amount, $calculatedIngredients)
            );
        }

        return $result;
    }

    private function calculateTreeForIngredient(Ingredient $ingredient, int $amount, array $calculatedIngredients): array
    {
        $tree = [];

        /** @var RecipeResult $asRecipeResult */
        foreach ($ingredient->getAsRecipeResult() as $asRecipeResult) {
            $recipe = $asRecipeResult->getRecipe();
            $ingredients = $recipe->getIngredients();

            /** @var Ingredient $subIngredient */
            foreach ($ingredients as $subIngredient) {
                if (in_array($subIngredient, $calculatedIngredients, true)) {
                    continue;
                }

                $amountForSubIngredient = $amount * ($subIngredient->getAmount() * $ingredient->getAmount());
                $calculatedIngredients[] = $subIngredient;

                $treeForSubIngredient = $this->calculateTreeForIngredient($subIngredient, $amountForSubIngredient, $calculatedIngredients);

                $tree[] = new TreeIngredientDTO(
                    $amountForSubIngredient,
                    $subIngredient->getItemId(),
                    $treeForSubIngredient
                );
            }
        }

        return $tree;
    }
}