<?php
declare(strict_types=1);

namespace App\Tests\Unit\Modules\Minecraft\Item\Service\Factory;

use App\Modules\Minecraft\Item\DTO\Item\StoreItemDTO;
use App\Modules\Minecraft\Item\Entity\Fluid;
use App\Modules\Minecraft\Item\Entity\FluidCell;
use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Enum\ItemTypes;
use App\Modules\Minecraft\Item\Service\Item\Factory\ItemFactory;
use PHPUnit\Framework\TestCase;

final class ItemFactoryTest extends TestCase
{
    private const TEST_ITEM_KEY = 1;
    private const TEST_ITEM_SUB_KEY = 1;
    private const TEST_ITEM_NAME = 'Test item';
    private const TEST_ITEM_TAG = 'test_item_tag';
    private const TEST_FLUID_NAME = 'test_fluid_name';

    private ItemFactory $itemFactory;

    public function setUp(): void
    {
        $this->itemFactory = new ItemFactory();
    }

    /**
     * @test
     */
    public function shouldBuildItemType(): void
    {
        $itemTypeStoreDTO = new StoreItemDTO(
            ItemTypes::ITEM,
            self::TEST_ITEM_KEY,
            self::TEST_ITEM_SUB_KEY,
            self::TEST_ITEM_NAME,
            self::TEST_ITEM_TAG,
            null
        );

        $item = $this->itemFactory->build($itemTypeStoreDTO);

        $this->assertSame(Item::class, get_class($item));
        $this->assertSame(self::TEST_ITEM_KEY, $item->getKey());
        $this->assertSame(self::TEST_ITEM_SUB_KEY, $item->getSubKey());
        $this->assertSame(self::TEST_ITEM_NAME, $item->getName());
        $this->assertSame(self::TEST_ITEM_TAG, $item->getItemTag());
    }

    /**
     * @test
     */
    public function shouldBuildFluidType(): void
    {
        $itemTypeStoreDTO = new StoreItemDTO(
            ItemTypes::FLUID,
            self::TEST_ITEM_KEY,
            self::TEST_ITEM_SUB_KEY,
            self::TEST_ITEM_NAME,
            self::TEST_ITEM_TAG,
            self::TEST_FLUID_NAME
        );

        /** @var Fluid $item */
        $item = $this->itemFactory->build($itemTypeStoreDTO);

        $this->assertSame(Fluid::class, get_class($item));
        $this->assertSame(self::TEST_ITEM_KEY, $item->getKey());
        $this->assertSame(self::TEST_ITEM_SUB_KEY, $item->getSubKey());
        $this->assertSame(self::TEST_ITEM_NAME, $item->getName());
        $this->assertSame(self::TEST_ITEM_TAG, $item->getItemTag());
        $this->assertSame(self::TEST_FLUID_NAME, $item->getFluidName());
    }

    /**
     * @test
     */
    public function shouldBuildFluidCellType(): void
    {
        $itemTypeStoreDTO = new StoreItemDTO(
            ItemTypes::FLUID_CELL,
            self::TEST_ITEM_KEY,
            self::TEST_ITEM_SUB_KEY,
            self::TEST_ITEM_NAME,
            self::TEST_ITEM_TAG,
            self::TEST_FLUID_NAME
        );

        /** @var FluidCell $item */
        $item = $this->itemFactory->build($itemTypeStoreDTO);

        $this->assertSame(FluidCell::class, get_class($item));
        $this->assertSame(self::TEST_ITEM_KEY, $item->getKey());
        $this->assertSame(self::TEST_ITEM_SUB_KEY, $item->getSubKey());
        $this->assertSame(self::TEST_ITEM_NAME, $item->getName());
        $this->assertSame(self::TEST_ITEM_TAG, $item->getItemTag());
        $this->assertSame(self::TEST_FLUID_NAME, $item->getFluidName());
    }
}
