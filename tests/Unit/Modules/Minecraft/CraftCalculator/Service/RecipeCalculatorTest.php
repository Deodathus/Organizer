<?php

declare(strict_types=1);

namespace App\Tests\Unit\Modules\Minecraft\CraftCalculator\Service;

use App\Modules\Minecraft\CraftCalculator\DTO\IngredientDTO;
use App\Modules\Minecraft\CraftCalculator\DTO\ResultDTO;
use App\Modules\Minecraft\CraftCalculator\Service\Calculator\RecipeCalculator;
use App\Modules\Minecraft\Item\Adapter\Calculator\Model\Calculable;
use App\Modules\Minecraft\Item\Entity\Ingredient;
use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Entity\Recipe;
use App\Modules\Minecraft\Item\Entity\RecipeResult;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RecipeCalculatorTest extends TestCase
{
    private const RECIPE_AMOUNT = 5;

    private const RECIPE_NAME = 'Iron pickaxe recipe';

    private RecipeCalculator $recipeCalculator;

    private readonly Ingredient|MockObject $ironIngotAsIngredient;

    private readonly Ingredient|MockObject $stickAsIngredient;

    private readonly RecipeResult|MockObject $ironPickaxeAsRecipeResult;

    public function setUp(): void
    {
        $this->recipeCalculator = new RecipeCalculator();
    }

    public function testCalculateSingleItemRecipe(): void
    {
        $recipe = $this->createMock(Recipe::class);
        $recipe
            ->method('getName')
            ->willReturn(self::RECIPE_NAME);
        $recipe
            ->method('getIngredients')
            ->willReturn($this->buildIngredients());
        $recipe
            ->method('getResults')
            ->willReturn($this->buildRecipeResult());

        $calculatorResult = $this->recipeCalculator->calculate(
            new Calculable($recipe),
            self::RECIPE_AMOUNT
        );

        /** @var IngredientDTO $ironIngotAmountNeeded */
        $ironIngotAmountNeeded = $calculatorResult->getIngredients()->first();
        /** @var IngredientDTO $stickAmountNeeded */
        $stickAmountNeeded = $calculatorResult->getIngredients()->last();

        /** @var ResultDTO $ironPickaxeCreated */
        $ironPickaxeCreated = $calculatorResult->getResults()->first();

        $this->assertSame(
            $this->ironIngotAsIngredient->getAmount() * self::RECIPE_AMOUNT,
            $ironIngotAmountNeeded->getAmount()
        );
        $this->assertSame(
            $this->stickAsIngredient->getAmount() * self::RECIPE_AMOUNT,
            $stickAmountNeeded->getAmount()
        );

        $this->assertSame(
            $this->ironPickaxeAsRecipeResult->getAmount() * self::RECIPE_AMOUNT,
            $ironPickaxeCreated->getAmount()
        );
    }

    private function buildIngredients(): ArrayCollection
    {
        $ironIngot = $this->createMock(Item::class);
        $ironIngot
            ->method('getId')
            ->willReturn(1);
        $ironIngot
            ->method('getName')
            ->willReturn('Iron ingot');
        $stick = $this->createMock(Item::class);
        $stick
            ->method('getId')
            ->willReturn(2);
        $stick
            ->method('getName')
            ->willReturn('Stick');

        $this->ironIngotAsIngredient = $this->createMock(Ingredient::class);
        $this->ironIngotAsIngredient
            ->method('getId')
            ->willReturn(1);
        $this->ironIngotAsIngredient
            ->method('getAmount')
            ->willReturn(3);

        $this->stickAsIngredient = $this->createMock(Ingredient::class);
        $this->stickAsIngredient
            ->method('getId')
            ->willReturn(2);
        $this->stickAsIngredient
            ->method('getAmount')
            ->willReturn(2);

        return new ArrayCollection([$this->ironIngotAsIngredient, $this->stickAsIngredient]);
    }

    private function buildRecipeResult(): ArrayCollection
    {
        $ironPickaxe = $this->createMock(Item::class);
        $ironPickaxe
            ->method('getId')
            ->willReturn(3);
        $ironPickaxe
            ->method('getName')
            ->willReturn('Iron Pickaxe');

        $this->ironPickaxeAsRecipeResult = $this->createMock(RecipeResult::class);
        $this->ironPickaxeAsRecipeResult
            ->method('getId')
            ->willReturn(1);
        $this->ironPickaxeAsRecipeResult
            ->method('getAmount')
            ->willReturn(1);

        return new ArrayCollection([$this->ironPickaxeAsRecipeResult]);
    }
}
