<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Recipe\Importer;

use App\Modules\Minecraft\Item\DTO\Recipe\Import\GregTech\GTMachine;
use App\Modules\Minecraft\Item\DTO\Recipe\Import\GregTech\GTRecipeDTO;
use App\Modules\Minecraft\Item\DTO\Recipe\Import\ItemDTO;
use App\Modules\Minecraft\Item\Enum\ItemTypes;
use App\Modules\Minecraft\Item\Exception\RecipeImporterException;
use JsonMachine\Exception\InvalidArgumentException;
use JsonMachine\Items;
use Symfony\Component\Messenger\MessageBusInterface;

final class GTRecipeImporter
{
    public function __construct(
        private readonly MessageBusInterface $messageBus
    ) {}

    public function import(string $filePath): void
    {
        try {
            $rawMachines = Items::fromFile($filePath);
        } catch (InvalidArgumentException $exception) {
            throw RecipeImporterException::fromException($exception);
        }

        $machines = [];

        foreach ($rawMachines as $rawMachine) {
            $recipes = [];

            foreach ($rawMachine->recipes as $rawRecipe) {
                $ingredients = [];
                $fluidIngredients = [];
                $recipeResults = [];
                $fluidRecipeResults = [];

                foreach ($rawRecipe->ingredients as $key => $rawMultiOreIngredient) {
                    $itemType = ItemTypes::ITEM;
                    if (property_exists($rawMultiOreIngredient, 'type')) {
                        $itemType = match ($rawMultiOreIngredient->type) {
                            default => ItemTypes::ITEM,
                            ItemTypes::FLUID_CELL->value => ItemTypes::FLUID_CELL,
                            ItemTypes::FLUID->value => ItemTypes::FLUID,
                        };
                    }

                    $itemTag = '';
                    if (property_exists($rawMultiOreIngredient, 'itemTag')) {
                        $itemTag = $rawMultiOreIngredient->itemTag;
                    }

                    $ingredient = new ItemDTO(
                        $itemType,
                        $rawMultiOreIngredient->key,
                        $rawMultiOreIngredient->subKey,
                        $rawMultiOreIngredient->name,
                        $itemTag,
                        $rawMultiOreIngredient->amount
                    );

                    $ingredients[$key] = $ingredient;
                }

                foreach ($rawRecipe->fluidIngredients as $key => $rawMultiOreFluidIngredient) {
                    $itemType = ItemTypes::ITEM;
                    if (property_exists($rawMultiOreFluidIngredient, 'type')) {
                        $itemType = match ($rawMultiOreFluidIngredient->type) {
                            default => ItemTypes::ITEM,
                            ItemTypes::FLUID_CELL->value => ItemTypes::FLUID_CELL,
                            ItemTypes::FLUID->value => ItemTypes::FLUID,
                        };
                    }

                    $itemName = '';
                    if (property_exists($rawMultiOreFluidIngredient, 'fluidName')) {
                        $itemName = $rawMultiOreFluidIngredient->fluidName;
                    }

                    $itemTag = '';
                    if (property_exists($rawMultiOreFluidIngredient, 'itemTag')) {
                        $itemTag = $rawMultiOreFluidIngredient->itemTag;
                    }

                    $ingredient = new ItemDTO(
                        $itemType,
                        $rawMultiOreFluidIngredient->key,
                        $rawMultiOreFluidIngredient->subKey,
                        $itemName,
                        $itemTag,
                        $rawMultiOreFluidIngredient->amount
                    );

                    $fluidIngredients[$key] = $ingredient;
                }

                foreach ($rawRecipe->recipeResults as $key => $rawMultiOreResult) {
                    $itemType = ItemTypes::ITEM;
                    if (property_exists($rawMultiOreResult, 'type')) {
                        $itemType = match ($rawMultiOreResult->type) {
                            default => ItemTypes::ITEM,
                            ItemTypes::FLUID_CELL->value => ItemTypes::FLUID_CELL,
                            ItemTypes::FLUID->value => ItemTypes::FLUID,
                        };
                    }

                    $itemTag = '';
                    if (property_exists($rawMultiOreResult, 'itemTag')) {
                        $itemTag = $rawMultiOreResult->itemTag;
                    }

                    $ingredient = new ItemDTO(
                        $itemType,
                        $rawMultiOreResult->key,
                        $rawMultiOreResult->subKey,
                        $rawMultiOreResult->name,
                        $itemTag,
                        $rawMultiOreResult->amount
                    );

                    $recipeResults[$key] = $ingredient;
                }

                foreach ($rawRecipe->fluidRecipeResults as $key => $rawMultiOreFluidResult) {
                    $itemType = ItemTypes::ITEM;
                    if (property_exists($rawMultiOreFluidResult, 'type')) {
                        $itemType = match ($rawMultiOreFluidResult->type) {
                            default => ItemTypes::ITEM,
                            ItemTypes::FLUID_CELL->value => ItemTypes::FLUID_CELL,
                            ItemTypes::FLUID->value => ItemTypes::FLUID,
                        };
                    }

                    $itemName = '';
                    if (property_exists($rawMultiOreFluidResult, 'fluidName')) {
                        $itemName = $rawMultiOreFluidResult->fluidName;
                    }

                    $itemTag = '';
                    if (property_exists($rawMultiOreFluidResult, 'itemTag')) {
                        $itemTag = $rawMultiOreFluidResult->itemTag;
                    }

                    $ingredient = new ItemDTO(
                        $itemType,
                        $rawMultiOreFluidResult->key,
                        $rawMultiOreFluidResult->subKey,
                        $itemName,
                        $itemTag,
                        $rawMultiOreFluidResult->amount
                    );

                    $fluidRecipeResults[$key] = $ingredient;
                }

                $recipe = new GTRecipeDTO(
                    $ingredients,
                    $fluidIngredients,
                    $recipeResults,
                    $fluidRecipeResults,
                    $rawRecipe->recipeChances,
                    $rawRecipe->isElectrical,
                    $rawRecipe->euPerTick,
                    $rawRecipe->duration,
                    $rawRecipe->generatedEnergy
                );

                $recipes[] = $recipe;
            }

            $machine = new GTMachine(
                $rawMachine->machineName,
                $rawMachine->generatedEnergyMultiplier,
                $recipes
            );

            $machines[] = $machine;
        }

        dd($machines);
    }
}
