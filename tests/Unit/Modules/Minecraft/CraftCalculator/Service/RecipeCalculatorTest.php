<?php
declare(strict_types=1);

namespace App\Tests\Unit\Modules\Minecraft\CraftCalculator\Service;

use App\Modules\Minecraft\CraftCalculator\DTO\IngredientDTO;
use App\Modules\Minecraft\CraftCalculator\DTO\ResultDTO;
use App\Modules\Minecraft\CraftCalculator\Service\Calculator\RecipeCalculator;
use App\Modules\Minecraft\Item\Entity\Ingredient;
use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Entity\Recipe;
use App\Modules\Minecraft\Item\Entity\RecipeResult;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class RecipeCalculatorTest extends TestCase
{
    private const RECIPE_AMOUNT = 5;

    private RecipeCalculator $recipeCalculator;

    private Recipe $recipe;

    private Ingredient $ingredient;

    private RecipeResult $recipeResult;

    public function setUp(): void
    {
        $this->recipeCalculator = new RecipeCalculator();
    }

    public function testCalculateSingleItemRecipe(): void
    {
        $this->setUpRecipe();

        $calculatorResult = $this->recipeCalculator->calculate($this->recipe, self::RECIPE_AMOUNT);

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
        $itemForIngredient = $this->createMock(Item::class);
        $itemForIngredient
            ->method('getId')
            ->willReturn(1);
        $itemForIngredient
            ->method('getName')
            ->willReturn('Recipe ingredient item');
        $itemForIngredient
            ->method('getKey')
            ->willReturn(1);

        $itemForResult = $this->createMock(Item::class);
        $itemForResult
            ->method('getId')
            ->willReturn(2);
        $itemForResult
            ->method('getName')
            ->willReturn('Recipe result item');
        $itemForResult
            ->method('getKey')
            ->willReturn(2);

        $this->ingredient = new Ingredient(self::RECIPE_AMOUNT, $itemForIngredient);
        $this->recipeResult = new RecipeResult(1, $itemForResult);

        $this->recipe = $this->createMock(Recipe::class);
        $this->recipe
            ->method('getId')
            ->willReturn(1);
        $this->recipe
            ->method('getName')
            ->willReturn('Test recipe name');
        $this->recipe
            ->method('getIngredients')
            ->willReturn(new ArrayCollection([$this->ingredient]));
        $this->recipe
            ->method('getResults')
            ->willReturn(new ArrayCollection([$this->recipeResult]));
    }
}
