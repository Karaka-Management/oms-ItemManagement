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

use Modules\ItemManagement\Models\NullAttributeValue;

/**
 * @internal
 */
final class NullAttributeValueTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\ItemManagement\Models\NullAttributeValue
     * @group framework
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\Attribute\Models\AttributeValue', new NullAttributeValue());
    }

    /**
     * @covers Modules\ItemManagement\Models\NullAttributeValue
     * @group framework
     */
    public function testId() : void
    {
        $null = new NullAttributeValue(2);
        self::assertEquals(2, $null->getId());
    }
}
