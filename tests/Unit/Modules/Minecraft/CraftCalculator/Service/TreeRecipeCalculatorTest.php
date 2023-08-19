<?php

declare(strict_types=1);

namespace App\Tests\Unit\Modules\Minecraft\CraftCalculator\Service;

use App\Modules\Minecraft\CraftCalculator\Service\Calculator\TreeRecipeCalculator;
use App\Modules\Minecraft\Item\Adapter\Calculator\Model\Calculable;
use App\Modules\Minecraft\Item\Entity\Ingredient;
use App\Modules\Minecraft\Item\Entity\Recipe;
use App\Modules\Minecraft\Item\Entity\RecipeResult;
use App\Tests\Util\Minecraft\Recipe\TestRecipeFactory;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class TreeRecipeCalculatorTest extends TestCase
{
    private const RECIPE_AMOUNT = TestRecipeFactory::RECIPE_AMOUNT;

    private TreeRecipeCalculator $treeRecipeCalculator;

    private TestRecipeFactory $recipeFactory;

    private Recipe|MockObject $recipe;

    private Ingredient|MockObject $ingredient;

    private Recipe|MockObject $recipeForAsRecipeResultForIngredient;

    private RecipeResult|MockObject $recipeResult;

    public function setUp(): void
    {
        $this->treeRecipeCalculator = new TreeRecipeCalculator();

        $this->recipeFactory = new TestRecipeFactory();
    }

    public function testCalculateSingleItemRecipeWithTree(): void
    {
        $this->setUpRecipe();
        $this->recipe
            ->method('getIngredients')
            ->willReturn(new ArrayCollection([$this->ingredient]));

        $treeCalculatorResult = $this->treeRecipeCalculator->calculate(
            new Calculable($this->recipe),
            self::RECIPE_AMOUNT
        );

        $firstBranchIngredientAmount = $treeCalculatorResult->getIngredients()->first()->getItems()[0]->getAmount();
        $this->assertEquals(
            $this->ingredient->getAmount() * self::RECIPE_AMOUNT,
            $firstBranchIngredientAmount
        );

        $recipeResultAmount = $treeCalculatorResult->getRecipeResults()[0]->getAmount();
        $this->assertEquals(
            $this->recipeResult->getAmount() * self::RECIPE_AMOUNT,
            $recipeResultAmount
        );

        $secondBranchIngredientAmount = $treeCalculatorResult->getIngredients()->first()->getItems()[0]->getAmount();
        $this->assertEquals(
            ($treeCalculatorResult->getIngredients()->first()->getItems()[0]->getAmount() * self::RECIPE_AMOUNT) / $recipeResultAmount,
            $secondBranchIngredientAmount
        );
    }

    public function shouldNotCalculateIngredientsThatWasAlreadyCalculated(): void
    {
        $this->setUpRecipe();
        $this->recipe
            ->method('getIngredients')
            ->willReturn(new ArrayCollection([$this->ingredient]));
        $this->recipeForAsRecipeResultForIngredient
            ->method('getIngredients')
            ->willReturn(new ArrayCollection([$this->getIngredientForRecipeForAsRecipeResult()]));

        // TODO: test it

        $treeCalculatorResult = $this->treeRecipeCalculator->calculate(
            new Calculable($this->recipe),
            self::RECIPE_AMOUNT
        );
    }

    private function setUpRecipe(): void
    {
        $this->recipe = $this->recipeFactory->build();

        $this->ingredient = $this->setUpIngredient();
        $this->recipeResult = $this->recipeFactory->getRecipeResult();
    }

    private function setUpIngredient(): Ingredient|MockObject
    {
        $ingredient = $this->recipeFactory->getIngredient();

        $asRecipeResultForIngredient = $this->getRecipeResultsForIngredient();

        $ingredient
            ->method('getAsRecipeResult')
            ->willReturn([$asRecipeResultForIngredient]);

        return $ingredient;
    }

    private function getRecipeResultsForIngredient(): RecipeResult|MockObject
    {
        $recipe = $this->getRecipeForRecipeResult();

        $asRecipeResult = $this->createMock(RecipeResult::class);
        $asRecipeResult
            ->method('getRecipe')
            ->willReturn($recipe);
        $asRecipeResult
            ->method('getAmount')
            ->willReturn(self::RECIPE_AMOUNT);

        return $asRecipeResult;
    }

    private function getRecipeForRecipeResult(): Recipe|MockObject
    {
        $ingredientForRecipeAsRecipeResult = $this->getIngredientForRecipeForAsRecipeResult();

        $this->recipeForAsRecipeResultForIngredient = $this->createMock(Recipe::class);
        $this->recipeForAsRecipeResultForIngredient
            ->method('getIngredients')
            ->willReturn(new ArrayCollection([$ingredientForRecipeAsRecipeResult]));

        return $this->recipeForAsRecipeResultForIngredient;
    }

    private function getIngredientForRecipeForAsRecipeResult(): Ingredient|MockObject
    {
        $ingredientForRecipeAsRecipeResult = $this->createMock(Ingredient::class);
        $ingredientForRecipeAsRecipeResult
            ->method('getId')
            ->willReturn(3);
        $ingredientForRecipeAsRecipeResult
            ->method('getAmount')
            ->willReturn(self::RECIPE_AMOUNT);

        return $ingredientForRecipeAsRecipeResult;
    }
}
