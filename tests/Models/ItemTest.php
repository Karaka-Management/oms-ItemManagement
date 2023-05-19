<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\ItemManagement\tests\Models;

use Modules\Editor\Models\EditorDoc;
use Modules\ItemManagement\Models\Item;
use Modules\Attribute\Models\Attribute;
use Modules\ItemManagement\Models\ItemStatus;
use Modules\Media\Models\Media;
use phpOMS\Localization\BaseStringL11n;

/**
 * @internal
 */
final class ItemTest extends \PHPUnit\Framework\TestCase
{
    private Item $item;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->item = new Item();
    }

    /**
     * @covers Modules\ItemManagement\Models\Item
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->item->id);
        self::assertEquals('', $this->item->number);
        self::assertEquals(0, $this->item->successor);
        self::assertEquals('', $this->item->info);
        self::assertEquals(ItemStatus::ACTIVE, $this->item->getStatus());
        self::assertEquals([], $this->item->getNotes());
        self::assertEquals([], $this->item->getFiles());
        self::assertEquals([], $this->item->getAttributes());
        self::assertInstanceOf(BaseStringL11n::class, $this->item->getL11n(''));
        self::assertInstanceOf('phpOMS\Localization\Money', $this->item->salesPrice);
        self::assertInstanceOf('phpOMS\Localization\Money', $this->item->purchasePrice);
    }

    /**
     * @covers Modules\ItemManagement\Models\Item
     * @group module
     */
    public function testStatusInputOutput() : void
    {
        $this->item->setStatus(ItemStatus::INACTIVE);
        self::assertEquals(ItemStatus::INACTIVE, $this->item->getStatus());
    }

    /**
     * @covers Modules\ItemManagement\Models\Item
     * @group module
     */
    public function testFileInputOutput() : void
    {
        $this->item->addFile($temp = new Media());
        self::assertCount(1, $this->item->getFiles());
    }

    /**
     * @covers Modules\ItemManagement\Models\Item
     * @group module
     */
    public function testNoteInputOutput() : void
    {
        $this->item->addNote(new EditorDoc());
        self::assertCount(1, $this->item->getNotes());
    }

    /**
     * @covers Modules\ItemManagement\Models\Item
     * @group module
     */
    public function testAttributeInputOutput() : void
    {
        $this->item->addAttribute(new Attribute());
        self::assertCount(1, $this->item->getAttributes());
    }

    /**
     * @covers Modules\ItemManagement\Models\Item
     * @group module
     */
    public function testL11nInputOutput() : void
    {
        $this->item->addL11n($t = new BaseStringL11n()); // has by default '' as type
        self::assertEquals($t, $this->item->getL11n(''));
    }

    /**
     * @covers Modules\ItemManagement\Models\Item
     * @group module
     */
    public function testSerialize() : void
    {
        $this->item->number        = '123456';
        $this->item->setStatus(ItemStatus::INACTIVE);
        $this->item->info = 'Test info';

        self::assertEquals(
            [
                'id'            => 0,
                'number'        => '123456',
                'status'        => ItemStatus::INACTIVE,
                'info'          => 'Test info',
            ],
            $this->item->jsonSerialize()
        );
    }
}
