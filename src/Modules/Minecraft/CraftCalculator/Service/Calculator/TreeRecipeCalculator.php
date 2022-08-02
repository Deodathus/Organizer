<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\Service\Calculator;

use App\Modules\Minecraft\CraftCalculator\DTO\TreeCalculatorResult;
use App\Modules\Minecraft\CraftCalculator\DTO\TreeIngredientDTO;
use App\Modules\Minecraft\CraftCalculator\DTO\TreeRecipeResultDTO;
use App\Modules\Minecraft\CraftCalculator\Model\CalculableInterface;
use App\Modules\Minecraft\Item\Entity\Ingredient;
use App\Modules\Minecraft\Item\Entity\RecipeResult;
use Doctrine\Common\Collections\Collection;
use RuntimeException;

final class TreeRecipeCalculator implements TreeRecipeCalculatorInterface
{
    private const TREE_DEPTH = 5;

    public function calculate(CalculableInterface $calculable, int $amount): TreeCalculatorResult
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
            $alreadyCalculatedIngredients = [];
            $treeDepth = 0;

            $ingredientAmount = $ingredient->getAmount() * $amount;
            $alreadyCalculatedIngredients[] = $ingredient->getId();

            $result[] = new TreeIngredientDTO(
                amount: $ingredientAmount,
                itemId: $ingredient->getItemId(),
                itemName: $ingredient->getItemName(),
                asResult: $this->calculateTreeForIngredient(
                    $ingredient,
                    $ingredientAmount,
                    $alreadyCalculatedIngredients,
                    $treeDepth
                )
            );
        }

        return $result;
    }

    private function calculateTreeForIngredient(
        Ingredient $ingredient,
        float $amount,
        array $alreadyCalculatedIngredients,
        int $treeDepth
    ): array {
        $tree = [];

        /** @var RecipeResult $asResult */
        foreach ($ingredient->getAsRecipeResult() as $asResult) {
            $resultRecipe = $asResult->getRecipe();
            $resultRecipeIngredients = $resultRecipe->getIngredients();

            /** @var Ingredient $resultRecipeIngredient */
            foreach ($resultRecipeIngredients as $resultRecipeIngredient) {
                if (
                    $this->treeDepthIsTooBig($treeDepth)
                    || $this->ingredientWasAlreadyCalculated(
                        $resultRecipeIngredient->getId(),
                        $alreadyCalculatedIngredients
                    )
                ) {
                    continue;
                }

                $resultAmount = $asResult->getAmount();
                if ($resultAmount === 0 || $resultAmount < 0) {
                    throw new RuntimeException('Division by zero!');
                }

                $amountForResultRecipeIngredient = ($amount * $resultRecipeIngredient->getAmount()) / $resultAmount;
                $alreadyCalculatedIngredients[] = $resultRecipeIngredient->getId();
                $treeDepth++;

                $treeForSubIngredient = $this->calculateTreeForIngredient(
                    $resultRecipeIngredient,
                    $amountForResultRecipeIngredient,
                    $alreadyCalculatedIngredients,
                    $treeDepth
                );

                $tree[] = new TreeIngredientDTO(
                    amount: $amountForResultRecipeIngredient,
                    itemId: $resultRecipeIngredient->getItemId(),
                    itemName: $resultRecipeIngredient->getItemName(),
                    asResult: $treeForSubIngredient
                );
            }
        }

        return $tree;
    }

    private function ingredientWasAlreadyCalculated(int $subIngredientId, array $alreadyCalculatedIngredients): bool
    {
        return in_array($subIngredientId, $alreadyCalculatedIngredients, true);
    }

    private function treeDepthIsTooBig(int $treeDepth): bool
    {
        return $treeDepth >= self::TREE_DEPTH;
    }
}