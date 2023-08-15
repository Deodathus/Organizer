<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Fixture\Recipe;

use App\DataFixtures\BaseFixture;
use App\Modules\Minecraft\Item\DTO\Recipe\IngredientDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\RecipeResultDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\StoreRecipeDTO;
use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Fixture\Item\ItemFixture;
use App\Modules\Minecraft\Item\Service\Recipe\Factory\RecipeFactoryInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

final class RecipeFixture extends BaseFixture implements DependentFixtureInterface
{
    private const AMOUNT = 30;

    private RecipeFactoryInterface $recipeFactory;

    public function __construct(RecipeFactoryInterface $recipeFactory)
    {
        $this->recipeFactory = $recipeFactory;
    }

    protected function prepareLoader(ObjectManager $manager): void
    {
        $items = $manager->getRepository(Item::class)->fetchAll();

        $this->createMany(
            self::AMOUNT,
            function (Generator $faker) use ($items) {
                $ingredients = $this->prepareIngredients($faker, $items);
                $recipeResults = $this->prepareRecipeResults($faker, $items);

                $usedItems = [];

                foreach ($ingredients as $ingredient) {
                    $itemId = $ingredient->getItemId();

                    $usedItems[$itemId] = $itemId;
                }

                foreach ($recipeResults as $recipeResult) {
                    $itemId = $recipeResult->getItemId();

                    $usedItems[$itemId] = $itemId;
                }

                return $this->recipeFactory->build(
                    new StoreRecipeDTO($faker->userName(), $ingredients, $recipeResults, $usedItems),
                    $items
                );
            },
            $manager
        );
    }

    public function getDependencies(): array
    {
        return [
            ItemFixture::class,
        ];
    }

    /**
     * @param Item[] $items
     *
     * @return IngredientDTO[]
     */
    private function prepareIngredients(Generator $faker, array $items): array
    {
        $ingredients = [];
        $ingredientItems = $faker->randomElements(
            $items,
            $faker->numberBetween(1, 4)
        );

        /** @var Item $item */
        foreach ($ingredientItems as $item) {
            $ingredients[] = new IngredientDTO($faker->numberBetween(1, 30), $item->getId());
        }

        return $ingredients;
    }

    /**
     * @param Item[] $items
     *
     * @return RecipeResultDTO[]
     */
    private function prepareRecipeResults(Generator $faker, array $items): array
    {
        $recipeResults = [];
        $recipeResultsItems = $faker->randomElements(
            $items,
            $faker->numberBetween(1, 4)
        );

        /** @var Item $item */
        foreach ($recipeResultsItems as $item) {
            $recipeResults[] = new RecipeResultDTO($faker->numberBetween(1, 50), $item->getId());
        }

        return $recipeResults;
    }
}
