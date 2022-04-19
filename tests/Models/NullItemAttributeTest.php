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
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\ItemManagement\tests\Models;

use Modules\ItemManagement\Models\NullItemAttribute;

/**
 * @internal
 */
final class NullItemAttributeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\ItemManagement\Models\NullItemAttribute
     * @group framework
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\ItemManagement\Models\ItemAttribute', new NullItemAttribute());
    }

    /**
     * @covers Modules\ItemManagement\Models\NullItemAttribute
     * @group framework
     */
    public function testId() : void
    {
        $null = new NullItemAttribute(2);
        self::assertEquals(2, $null->getId());
    }
}
