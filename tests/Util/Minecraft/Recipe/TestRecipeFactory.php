<?php
declare(strict_types=1);

namespace App\Tests\Util\Minecraft\Recipe;

use App\Modules\Minecraft\Item\Entity\Ingredient;
use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Entity\Recipe;
use App\Modules\Minecraft\Item\Entity\RecipeResult;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class TestRecipeFactory extends TestCase
{
    public const RECIPE_AMOUNT = 5;

    private const RECIPE_RESULT_AMOUNT = 1;

    private Recipe|MockObject $recipe;

    private Ingredient|MockObject $ingredient;

    private RecipeResult|MockObject $recipeResult;

    public function build(): Recipe|MockObject
    {
        $this->ingredient = $this->buildIngredient();
        $this->recipeResult = $this->buildRecipeResult();

        return $this->buildRecipe($this->ingredient, $this->recipeResult);
    }

    public function getIngredient(): Ingredient|MockObject
    {
        return $this->ingredient;
    }

    public function getRecipeResult(): RecipeResult|MockObject
    {
        return $this->recipeResult;
    }

    private function buildIngredient(): Ingredient|MockObject
    {
        $itemForIngredient = $this->buildItemForIngredient();

        $ingredient = $this->createMock(Ingredient::class);
        $ingredient
            ->method('getAmount')
            ->willReturn(self::RECIPE_AMOUNT);
        $ingredient
            ->method('getItem')
            ->willReturn($itemForIngredient);
        $ingredient
            ->method('getItemId')
            ->willReturn($itemForIngredient->getId());
        $ingredient
            ->method('getItemName')
            ->willReturn($itemForIngredient->getName());

        return $ingredient;
    }

    private function buildItemForIngredient(): Item|MockObject
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

        return $itemForIngredient;
    }

    private function buildRecipeResult(): RecipeResult|MockObject
    {
        $itemForResult = $this->buildItemForRecipeResult();

        $recipeResult = $this->createMock(RecipeResult::class);
        $recipeResult
            ->method('getAmount')
            ->willReturn(self::RECIPE_RESULT_AMOUNT);
        $recipeResult
            ->method('getItem')
            ->willReturn($itemForResult);
        $recipeResult
            ->method('getItemId')
            ->willReturn($itemForResult->getId());
        $recipeResult
            ->method('getItemName')
            ->willReturn($itemForResult->getName());

        return $recipeResult;
    }

    private function buildItemForRecipeResult(): Item|MockObject
    {
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

        return $itemForResult;
    }

    private function buildRecipe(Ingredient $ingredient, RecipeResult $recipeResult): Recipe|MockObject
    {
        $recipe = $this->createMock(Recipe::class);
        $recipe
            ->method('getId')
            ->willReturn(1);
        $recipe
            ->method('getName')
            ->willReturn('Test recipe name');
        $recipe
            ->method('getIngredients')
            ->willReturn(new ArrayCollection([$ingredient]));
        $recipe
            ->method('getResults')
            ->willReturn(new ArrayCollection([$recipeResult]));

        return $recipe;
    }
}
