<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Recipe\Importer;

use App\Modules\Minecraft\Item\DTO\Item\StoreItemDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\Import\IngredientDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\Import\ItemDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\Import\RecipeDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\Import\RecipeResultDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\IngredientDTO as StoreIngredientDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\IngredientItemDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\RecipeResultDTO as StoreRecipeResultDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\StoreRecipeDTO;
use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Enum\ItemTypes;
use App\Modules\Minecraft\Item\Repository\RecipeRepository;
use App\Modules\Minecraft\Item\Search\Filter\KeysFilter;
use App\Modules\Minecraft\Item\Service\Item\ItemFetcher;
use App\Modules\Minecraft\Item\Service\Item\ItemPersister;
use App\Modules\Minecraft\Item\Service\Recipe\Factory\RecipeFactoryInterface;
use Doctrine\Common\Collections\ArrayCollection;

final class RecipeImportProcessor implements RecipeImportProcessorInterface
{
    public function __construct(
        private readonly ItemPersister $itemPersister,
        private readonly ItemFetcher $itemFetcher,
        private readonly RecipeFactoryInterface $recipeFactory,
        private readonly RecipeRepository $recipeRepository
    ) {}

    public function process(RecipeDTO $recipeDTO): void
    {
        $usedItems = [];

        $ingredients = $this->prepareIngredients($recipeDTO->getIngredients(), $usedItems);
        $results = $this->prepareRecipeResults($recipeDTO->getRecipeResults(), $usedItems);

        $recipeName = $this->prepareRecipeName($results, $usedItems);

        $storeRecipeDTO = new StoreRecipeDTO(
            name: $recipeName,
            ingredients: $ingredients,
            results: $results,
            itemsInRecipeIds: [] // can be empty in this case
        );

        $recipe = $this->recipeFactory->build(
            recipeDTO: $storeRecipeDTO,
            usedItems: $usedItems
        );

        $this->recipeRepository->store(recipe: $recipe);
        $this->recipeRepository->flush();
    }

    /**
     * @param StoreRecipeResultDTO[] $results
     * @param Item[] $usedItems
     */
    private function prepareRecipeName(array $results, array $usedItems): string
    {
        $recipeName = 'Undefined recipe';
        if (count($results) > 0) {
            $recipeResultsNames = [];

            foreach ($results as $result) {
                $recipeResultsNames[] = isset($usedItems[$result->getItemId()])
                    ? ($usedItems[$result->getItemId()])->getName()
                    : 'Undefined';
            }

            $recipeName = implode('&', $recipeResultsNames);
            $recipeName .= ' Recipe';
        }

        return $recipeName;
    }

    /**
     * @param IngredientDTO[] $ingredients
     * @param int[] $usedItems
     *
     * @return StoreIngredientDTO[]
     */
    private function prepareIngredients(array $ingredients, array &$usedItems): array
    {
        $preparedIngredients = [];

        foreach ($ingredients as $ingredient) {
            $ingredientItems = [];

            foreach ($ingredient->getItems() as $rawIngredientItems) {
                $ingredientItem = $rawIngredientItems;

                $item = $this->prepareItem(
                    itemDTO: $ingredientItem
                );
                $usedItems[$item->getId()] = $item;

                $ingredientItems[] = new IngredientItemDTO(
                    amount: $ingredientItem->getAmount(),
                    itemId: $item->getId()
                );
            }

            $preparedIngredients[] = new StoreIngredientDTO(new ArrayCollection($ingredientItems));
        }

        return $preparedIngredients;
    }

    /**
     * @param RecipeResultDTO[] $recipeResults
     * @param int[] $usedItems
     *
     * @return StoreRecipeResultDTO[]
     */
    private function prepareRecipeResults(array $recipeResults, array &$usedItems): array
    {
        $preparedRecipeResults = [];

        foreach ($recipeResults as $recipeResult) {
            $recipeResultItem = $recipeResult->getItems()[0];

            $item = $this->prepareItem(
                itemDTO: $recipeResultItem
            );
            $usedItems[$item->getId()] = $item;

            $preparedRecipeResults[] = new StoreRecipeResultDTO(
                amount: $recipeResultItem->getAmount(),
                itemId:  $item->getId()
            );
        }

        return $preparedRecipeResults;
    }

    private function prepareItem(ItemDTO $itemDTO): Item
    {
        $item = $this->itemFetcher->fetchByKeys(
            keysFilter: new KeysFilter(
                mainKey: $itemDTO->getKey(),
                subKey: $itemDTO->getSubKey()
            )
        );

        if (!$item) {
            $item = $this->itemPersister->store(
                new StoreItemDTO(
                    itemType: ItemTypes::ITEM,
                    key: $itemDTO->getKey(),
                    subKey: $itemDTO->getSubKey(),
                    name: $itemDTO->getName(),
                    itemTag: $itemDTO->getItemTag(),
                    fluidName: ''
                )
            );
        }

        return $item;
    }
}
