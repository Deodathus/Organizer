<?php
declare(strict_types=1);

namespace App\Tests\Unit\Modules\Minecraft\Item\Service\Factory;

use App\Modules\Minecraft\Item\DTO\Recipe\IngredientDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\RecipeResultDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\StoreRecipeDTO;
use App\Modules\Minecraft\Item\Entity\Ingredient;
use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Entity\RecipeResult;
use App\Modules\Minecraft\Item\Exception\RecipeFactoryBuildException;
use App\Modules\Minecraft\Item\Service\Recipe\Factory\RecipeFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RecipeFactoryTest extends TestCase
{
    private const FIRST_ITEM_ID = 1;

    private const SECOND_ITEM_ID = 2;

    private const THIRD_ITEM_ID = 3;

    private const FIRST_INGREDIENT_AMOUNT = 1;

    private const SECOND_INGREDIENT_AMOUNT = 2;

    private const FIRST_RECIPE_RESULT_AMOUNT = 3;

    private readonly RecipeFactory $recipeFactory;

    /** @var Item|MockObject[] $items */
    private readonly array $items;

    /** @var IngredientDTO[] $ingredients */
    private readonly array $ingredients;

    private readonly array $recipeResults;

    public function setUp(): void
    {
        $this->recipeFactory = new RecipeFactory();

        $this->setUpItems();

        $this->ingredients = [
            self::FIRST_ITEM_ID => new IngredientDTO(amount: self::FIRST_INGREDIENT_AMOUNT, itemId: self::FIRST_ITEM_ID),
            self::SECOND_ITEM_ID => new IngredientDTO(amount: self::SECOND_INGREDIENT_AMOUNT, itemId: self::SECOND_ITEM_ID),
        ];
        $this->recipeResults = [
            self::THIRD_ITEM_ID => new RecipeResultDTO(amount: self::FIRST_RECIPE_RESULT_AMOUNT, itemId: self::THIRD_ITEM_ID),
        ];
    }

    /**
     * @test
     */
    public function shouldBuildDefaultRecipe(): void
    {
        $storeRecipeDTO = new StoreRecipeDTO(
            name: 'Recipe',
            ingredients: $this->ingredients,
            results: $this->recipeResults,
            itemsInRecipeIds: [1,2,3]
        );

        $recipe = $this->recipeFactory->build($storeRecipeDTO, $this->items);

        /** @var Ingredient $ingredient */
        $ingredient = $recipe->getIngredients()->first();
        /** @var Ingredient $secondIngredient */
        $secondIngredient = $recipe->getIngredients()->last();
        /** @var RecipeResult $recipeResult */
        $recipeResult = $recipe->getResults()->first();

        $this->assertSame('Recipe', $recipe->getName());

        $this->assertSame($ingredient->getItemId(), self::FIRST_ITEM_ID);
        $this->assertSame($ingredient->getAmount(), self::FIRST_INGREDIENT_AMOUNT);

        $this->assertSame($secondIngredient->getItemId(), self::SECOND_ITEM_ID);
        $this->assertSame($secondIngredient->getAmount(), self::SECOND_INGREDIENT_AMOUNT);

        $this->assertSame($recipeResult->getItemId(), self::THIRD_ITEM_ID);
        $this->assertSame($recipeResult->getAmount(), self::FIRST_RECIPE_RESULT_AMOUNT);
    }

    /**
     * @test
     */
    public function shouldThrowException(): void
    {
        $ingredients = $this->ingredients;
        $invalidIngredient = $this->createMock(Ingredient::class);
        $invalidIngredient
            ->method('getItemId')
            ->willReturn(4);
        $ingredients[4] = $invalidIngredient;

        $storeRecipeDTO = new StoreRecipeDTO(
            name: 'Recipe',
            ingredients: $ingredients,
            results: $this->recipeResults,
            itemsInRecipeIds: [1,2,3]
        );

        $this->expectException(RecipeFactoryBuildException::class);

        $this->recipeFactory->build($storeRecipeDTO, $this->items);
    }

    private function setUpItems(): void
    {
        $firstItem = $this->createMock(Item::class);
        $firstItem
            ->method('getId')
            ->willReturn(self::FIRST_ITEM_ID);

        $secondItem = $this->createMock(Item::class);
        $secondItem
            ->method('getId')
            ->willReturn(self::SECOND_ITEM_ID);

        $thirdItem = $this->createMock(Item::class);
        $thirdItem
            ->method('getId')
            ->willReturn(self::THIRD_ITEM_ID);

        $this->items = [
            self::FIRST_ITEM_ID => $firstItem,
            self::SECOND_ITEM_ID => $secondItem,
            self::THIRD_ITEM_ID => $thirdItem,
        ];
    }
}
