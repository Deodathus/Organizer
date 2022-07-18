<?php

declare(strict_types=1);

namespace App\Tests\Unit\Modules\Minecraft\CraftCalculator\Service;

use App\Modules\Minecraft\CraftCalculator\DTO\IngredientDTO;
use App\Modules\Minecraft\CraftCalculator\DTO\ResultDTO;
use App\Modules\Minecraft\CraftCalculator\Service\Calculator\RecipeCalculator;
use App\Modules\Minecraft\Item\Adapter\Calculator\Model\Calculable;
use App\Modules\Minecraft\Item\Entity\Ingredient;
use App\Modules\Minecraft\Item\Entity\Recipe;
use App\Modules\Minecraft\Item\Entity\RecipeResult;
use App\Tests\Util\Minecraft\Recipe\TestRecipeFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RecipeCalculatorTest extends TestCase
{
    private const RECIPE_AMOUNT = TestRecipeFactory::RECIPE_AMOUNT;

    private RecipeCalculator $recipeCalculator;

    private TestRecipeFactory $recipeFactory;

    private Recipe|MockObject $recipe;

    private Ingredient $ingredient;

    private RecipeResult $recipeResult;

    public function setUp(): void
    {
        $this->recipeCalculator = new RecipeCalculator();

        $this->recipeFactory = new TestRecipeFactory();
    }

    public function testCalculateSingleItemRecipe(): void
    {
        $this->setUpRecipe();

        $calculatorResult = $this->recipeCalculator->calculate(
            new Calculable($this->recipe),
            self::RECIPE_AMOUNT
        );

        /** @var IngredientDTO $calculatorResultIngredient */
        $calculatorResultIngredient = $calculatorResult->getIngredients()->first();

        /** @var ResultDTO $calculatorResultRecipeResult */
        $calculatorResultRecipeResult = $calculatorResult->getResults()->first();

        $this->assertSame(
            $this->ingredient->getAmount() * self::RECIPE_AMOUNT,
            $calculatorResultIngredient->getAmount()
        );

        $this->assertSame(
            $this->recipeResult->getAmount() * self::RECIPE_AMOUNT,
            $calculatorResultRecipeResult->getAmount()
        );
    }

    private function setUpRecipe(): void
    {
        $this->recipe = $this->recipeFactory->build();
        $this->ingredient = $this->recipeFactory->getIngredient();
        $this->recipeResult = $this->recipeFactory->getRecipeResult();
    }
}
