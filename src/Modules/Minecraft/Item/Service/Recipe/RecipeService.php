<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Recipe;

use App\Modules\Minecraft\Item\DTO\Recipe\StoreRecipeDTO;
use App\Modules\Minecraft\Item\Entity\Recipe;
use App\Modules\Minecraft\Item\Exception\CannotFetchItemsException;
use App\Modules\Minecraft\Item\Exception\RecipeDoesNotExist;
use App\Modules\Minecraft\Item\Exception\RecipeStoreException;
use App\Modules\Minecraft\Item\Repository\ItemRepository;
use App\Modules\Minecraft\Item\Repository\RecipeRepository;
use App\Modules\Minecraft\Item\Service\Recipe\Factory\RecipeFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class RecipeService implements RecipeServiceInterface
{
    public function __construct(
        private readonly RecipeFactoryInterface $recipeFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly RecipeRepository $recipeRepository,
        private readonly ItemRepository $itemRepository
    ) {}

    /**
     * @throws RecipeDoesNotExist
     */
    public function fetch(int $id): Recipe
    {
        if ($recipe = $this->recipeRepository->fetch($id)) {
            return $recipe;
        }

        throw new RecipeDoesNotExist(sprintf('Recipe ID: %d', $id));
    }

    /**
     * @throws RecipeStoreException
     */
    public function store(StoreRecipeDTO $recipeDTO): int
    {
        try {
            $items = $this->itemRepository->fetchByIds($recipeDTO->getItemsInRecipeIds());
        } catch (CannotFetchItemsException $exception) {
            throw RecipeStoreException::fromException($exception);
        }

        $recipe = $this->recipeFactory->build($recipeDTO, $items);

        $this->entityManager->persist($recipe);
        $this->entityManager->flush();

        return $recipe->getId();
    }
}
