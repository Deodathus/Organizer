<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Recipe\Importer;

use App\Modules\Minecraft\Item\DTO\Recipe\Import\IngredientDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\Import\ItemDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\Import\RecipeDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\Import\RecipeResultDTO;
use App\Modules\Minecraft\Item\Enum\ItemTypes;
use App\Modules\Minecraft\Item\Exception\RecipeImporterException;
use App\Modules\Minecraft\Item\Messenger\Message\ImportRecipe;
use JsonMachine\Exception\InvalidArgumentException;
use JsonMachine\Items;
use stdClass;
use Symfony\Component\Messenger\MessageBusInterface;

final class RecipeImporter
{
    private const UNDEFINED_NAME = 'Undefined';
    private const FLUID_TYPE = 'fluid';
    private const UNDEFINED_ITEM_TAG = 'Undefined item tag';

    public function __construct(
        private readonly MessageBusInterface $messageBus
    ) {
    }

    public function import(string $filePath): void
    {
        try {
            $recipes = Items::fromFile($filePath);
        } catch (InvalidArgumentException $exception) {
            throw RecipeImporterException::fromException($exception);
        }

        foreach ($recipes as $recipe) {
            $preparedIngredients = [];
            $preparedResults = [];

            foreach ($recipe->ingredients as $ingredient) {
                if ($this->areManyItemsFitAsIngredient($ingredient)) {
                    $preparedIngredient = $this->prepareIngredientWithManyItems($ingredient);
                } else {
                    $preparedIngredient = $this->prepareIngredientWithSingleItem($ingredient);
                }

                $preparedIngredients[] = $preparedIngredient;
            }

            if ($this->areManyItemsAsResult($recipe->recipeResult)) {
                // TODO: implement this for recipes with multiple result items like GT recipes etc.
            } else {
                $preparedResults[] = $this->prepareRecipeResultWithSingleItem($recipe->recipeResult);
            }

            $recipeToStore = new RecipeDTO(
                ingredients: $preparedIngredients,
                recipeResults: $preparedResults
            );

            $this->messageBus->dispatch(
                new ImportRecipe(
                    recipe: $recipeToStore
                )
            );
        }
    }

    private function areManyItemsFitAsIngredient($ingredient): bool
    {
        return is_array($ingredient);
    }

    /**
     * @param stdClass[] $ingredientItems
     *
     * @return IngredientDTO
     */
    private function prepareIngredientWithManyItems(array $ingredientItems): IngredientDTO
    {
        $ingredients = [];

        foreach ($ingredientItems as $ingredientItem) {
            $ingredients[] = $this->prepareItemDTO($ingredientItem);
        }

        return new IngredientDTO(
            items: $ingredients
        );
    }

    private function prepareIngredientWithSingleItem(stdClass $ingredient): IngredientDTO
    {
        return new IngredientDTO(
            items: [
                $this->prepareItemDTO($ingredient),
            ]
        );
    }

    private function prepareItemDTO(stdClass $ingredient): ItemDTO
    {
        $name = self::UNDEFINED_NAME;
        if (property_exists($ingredient, 'name')) {
            $name = $ingredient->name;
        } elseif (property_exists($ingredient, 'type') && $ingredient->type === self::FLUID_TYPE) {
            $name = $ingredient->fluidName;
        }

        $itemType = ItemTypes::ITEM;
        if (property_exists($ingredient, 'type')) {
            $itemType = match ($ingredient->type) {
                default => ItemTypes::ITEM,
                ItemTypes::FLUID_CELL->value => ItemTypes::FLUID_CELL,
                ItemTypes::FLUID->value => ItemTypes::FLUID,
            };
        }

        return new ItemDTO(
            itemType: $itemType,
            key: $ingredient->key,
            subKey: $ingredient->subKey,
            name: $name,
            itemTag: self::UNDEFINED_ITEM_TAG,
            amount: $ingredient->amount
        );
    }

    private function areManyItemsAsResult($recipeResult): bool
    {
        return is_array($recipeResult);
    }

    private function prepareRecipeResultWithSingleItem(stdClass $recipeResult): RecipeResultDTO
    {
        return new RecipeResultDTO(
            items: [
                $this->prepareItemDTO($recipeResult),
            ]
        );
    }
}
