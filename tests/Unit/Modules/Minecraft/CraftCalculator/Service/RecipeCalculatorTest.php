<?php
declare(strict_types=1);

namespace App\Tests\Unit\Modules\Minecraft\CraftCalculator\Service;

use App\Modules\Minecraft\CraftCalculator\DTO\IngredientDTO;
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
    private const ITEM_AMOUNT_FOR_RECIPE = 1;

    private RecipeCalculator $recipeCalculator;

    private Recipe $recipe;

    private Ingredient $ingredient;

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

        $this->assertSame(
            $this->ingredient->getAmount() * self::RECIPE_AMOUNT,
            $calculatorResultIngredient->getAmount()
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

        $itemForResult = new Item('Recipe result item', 2, null);

        $this->ingredient = new Ingredient(self::RECIPE_AMOUNT, $itemForIngredient);
        $recipeResult = new RecipeResult(1, $itemForResult);

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
            ->willReturn(new ArrayCollection([$recipeResult]));
    }
}