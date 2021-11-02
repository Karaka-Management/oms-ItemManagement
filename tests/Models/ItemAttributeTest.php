<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\ItemManagement\tests\Models;

use Modules\ItemManagement\Models\ItemAttribute;

/**
 * @internal
 */
final class ItemAttributeTest extends \PHPUnit\Framework\TestCase
{
    private ItemAttribute $attr;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->attr = new ItemAttribute();
    }

    /**
     * @covers Modules\ItemManagement\Models\ItemAttribute
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->attr->getId());
        self::assertInstanceOf('Modules\ItemManagement\Models\ItemAttributeType', $this->attr->type);
        self::assertInstanceOf('Modules\ItemManagement\Models\ItemAttributeValue', $this->attr->value);
    }

    /**
     * @covers Modules\ItemManagement\Models\ItemAttribute
     * @group module
     */
    public function testSerialize() : void
    {
        self::assertEquals(
            [
                'id',
                'type',
                'value',
            ],
            \array_keys($this->attr->jsonSerialize())
        );
    }
}
