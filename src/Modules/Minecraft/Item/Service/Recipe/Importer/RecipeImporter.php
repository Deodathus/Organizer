<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Recipe\Importer;

use App\Modules\Minecraft\Item\DTO\Item\StoreItemDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\IngredientDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\RecipeResultDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\StoreRecipeDTO;
use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Exception\RecipeImporterException;
use App\Modules\Minecraft\Item\Repository\RecipeRepository;
use App\Modules\Minecraft\Item\Search\Filter\KeysFilter;
use App\Modules\Minecraft\Item\Service\Item\ItemFetcher;
use App\Modules\Minecraft\Item\Service\Item\ItemPersister;
use App\Modules\Minecraft\Item\Service\Recipe\Factory\RecipeFactoryInterface;
use JsonMachine\Exception\InvalidArgumentException;
use JsonMachine\Items;
use stdClass;

final class RecipeImporter
{
    public function __construct(
        private readonly ItemFetcher $itemFetcher,
        private readonly ItemPersister $itemPersister,
        private readonly RecipeRepository $recipeRepository,
        private readonly RecipeFactoryInterface $recipeFactory
    ) {}

    public function import(string $filePath): void
    {
        try {
            $recipes = Items::fromFile($filePath);
        } catch (InvalidArgumentException $exception) {
            throw RecipeImporterException::fromException($exception);
        }

        $recipesAdded = 0;
        $recipesToStore = [];
        $usedItems = [];

        foreach ($recipes as $recipe) {
            $ingredients = [];

            foreach ($recipe->ingredients as $ingredientStack) {
                if(is_array($ingredientStack)) {
                    foreach ($ingredientStack as $ingredient) {
                        $ingredients[] = $this->prepareIngredientDTO($ingredient, $usedItems);
                    }
                } else {
                    $ingredients[] = $this->prepareIngredientDTO($ingredientStack, $usedItems);
                }
            }

            $recipeResultItemKeys = $this->prepareItemKeys($recipe->recipeResult);
            if (isset($usedItems[$recipeResultItemKeys])) {
                /** @var Item $recipeResultItem */
                $recipeResultItem = $usedItems[$recipeResultItemKeys];
            } else {
                /** @var Item $recipeResultItem */
                $recipeResultItem = $this->itemFetcher->fetchByKeys(
                    keysFilter: new KeysFilter(
                        mainKey: $recipe->recipeResult->key,
                        subKey: $recipe->recipeResult->subKey
                    )
                );

                if ($recipeResultItem === null) {
                    $recipeResultItem = $this->storeItem(
                        storeItemDTO: new StoreItemDTO(
                            key: $recipe->recipeResult->key,
                            subKey: $recipe->recipeResult->subKey,
                            name: $recipe->recipeResult->name
                        )
                    );
                }

                $usedItems[$recipeResultItemKeys] = $recipeResultItem;
            }

            $recipeResult = new RecipeResultDTO(
                amount: $recipe->recipeResult->amount,
                itemId: $recipeResultItem->getId()
            );

            $recipesToStore[] = new StoreRecipeDTO(
                name: sprintf('%s recipe', $recipeResultItem->getName()),
                ingredients: $ingredients,
                results: [$recipeResult],
                itemsInRecipeIds: [] // can be empty in this case
            );

            if (count($recipesToStore) > 100) {
                $this->storeRecipes($recipesToStore, $usedItems);

                $recipesAdded += 100;

                $recipesToStore = [];
            }

            if ($recipesAdded > 1000) {
                $recipesAdded = 0;

                $usedItems = [];
            }
        }

        $this->recipeRepository->flush();
    }

    private function prepareItemKeys(stdClass $item): string
    {
        return sprintf('%s:%s', $item->key, $item->subKey);
    }

    private function prepareIngredientDTO(stdClass $ingredient, &$usedItems): IngredientDTO
    {
        $itemKeys = $this->prepareItemKeys($ingredient);

        if (isset($usedItems[$itemKeys])) {
            $item = $usedItems[$itemKeys];
        } else {
            $item = $this->itemFetcher->fetchByKeys(
                keysFilter: new KeysFilter(
                    mainKey: $ingredient->key,
                    subKey: $ingredient->subKey
                )
            );

            if ($item === null) {
                $item = $this->storeItem(
                    storeItemDTO: new StoreItemDTO(
                        key: $ingredient->key,
                        subKey: $ingredient->subKey,
                        name: $ingredient->name
                    )
                );

                $usedItems[$itemKeys] = $item;
            }

            $usedItems[$itemKeys] = $item;
        }

        return new IngredientDTO(
            amount: $ingredient->amount,
            itemId: $item->getId()
        );
    }

    private function storeItem(StoreItemDTO $storeItemDTO): Item
    {
        return $this->itemPersister->store($storeItemDTO);
    }

    private function storeRecipes(array $recipesToStore, array $usedItems): void
    {
        $itemsToUse = [];
        foreach ($usedItems as $usedItem) {
            $itemsToUse[$usedItem->getId()] = $usedItem;
        }

        foreach ($recipesToStore as $recipeToStore) {
            $recipe = $this->recipeFactory->build(recipeDTO: $recipeToStore, usedItems: $itemsToUse);

            $this->recipeRepository->store($recipe);
        }
    }
}
