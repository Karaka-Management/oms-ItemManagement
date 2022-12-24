<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\ItemManagement\tests\Models;

use Modules\ItemManagement\Models\ItemAttributeValue;

/**
 * @internal
 */
final class ItemAttributeValueTest extends \PHPUnit\Framework\TestCase
{
    private ItemAttributeValue $attr;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->attr = new ItemAttributeValue();
    }

    /**
     * @covers Modules\ItemManagement\Models\ItemAttributeValue
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->attr->getId());
        self::assertEquals(0, $this->attr->type);
        self::assertNull($this->attr->getValue());
        self::assertFalse($this->attr->isDefault);
    }

    /**
     * @covers Modules\ItemManagement\Models\ItemAttributeValue
     * @group module
     */
    public function testValueIntInputOutput() : void
    {
        $this->attr->setValue(1);
        self::assertEquals(1, $this->attr->getValue());
    }

    /**
     * @covers Modules\ItemManagement\Models\ItemAttributeValue
     * @group module
     */
    public function testValueFloatInputOutput() : void
    {
        $this->attr->setValue(1.1);
        self::assertEquals(1.1, $this->attr->getValue());
    }

    /**
     * @covers Modules\ItemManagement\Models\ItemAttributeValue
     * @group module
     */
    public function testValueStringInputOutput() : void
    {
        $this->attr->setValue('test');
        self::assertEquals('test', $this->attr->getValue());
    }

    /**
     * @covers Modules\ItemManagement\Models\ItemAttributeValue
     * @group module
     */
    public function testValueDateInputOutput() : void
    {
        $this->attr->setValue($dat = new \DateTime('now'));
        self::assertEquals($dat->format('Y-m-d'), $this->attr->getValue()->format('Y-m-d'));
    }

    /**
     * @covers Modules\ItemManagement\Models\ItemAttributeValue
     * @group module
     */
    public function testSerialize() : void
    {
        $this->attr->type = 1;
        $this->attr->setValue('test');
        $this->attr->isDefault = true;

        self::assertEquals(
            [
                'id'           => 0,
                'type'         => 1,
                'valueInt'     => null,
                'valueStr'     => 'test',
                'valueDec'     => null,
                'valueDat'     => null,
                'isDefault'    => true,
            ],
            $this->attr->jsonSerialize()
        );
    }
}
