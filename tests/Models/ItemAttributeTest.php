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

use Modules\Attribute\Models\Attribute;

/**
 * @internal
 */
final class AttributeTest extends \PHPUnit\Framework\TestCase
{
    private Attribute $attr;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->attr = new Attribute();
    }

    /**
     * @covers Modules\Attribute\Models\Attribute
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->attr->getId());
        self::assertInstanceOf('Modules\Attribute\Models\AttributeType', $this->attr->type);
        self::assertInstanceOf('Modules\Attribute\Models\AttributeValue', $this->attr->value);
    }

    /**
     * @covers Modules\Attribute\Models\Attribute
     * @group module
     */
    public function testSerialize() : void
    {
        self::assertEquals(
            [
                'id',
                'item',
                'type',
                'value',
            ],
            \array_keys($this->attr->jsonSerialize())
        );
    }
}
