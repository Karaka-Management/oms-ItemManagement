<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\ItemManagement\tests\Models;

use Modules\ItemManagement\Models\NullItemAttributeValue;

/**
 * @internal
 */
final class NullItemAttributeValueTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\ItemManagement\Models\NullItemAttributeValue
     * @group framework
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\ItemManagement\Models\ItemAttributeValue', new NullItemAttributeValue());
    }

    /**
     * @covers Modules\ItemManagement\Models\NullItemAttributeValue
     * @group framework
     */
    public function testId() : void
    {
        $null = new NullItemAttributeValue(2);
        self::assertEquals(2, $null->getId());
    }
}
