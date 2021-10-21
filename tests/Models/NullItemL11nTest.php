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

use Modules\ItemManagement\Models\NullItemL11n;

/**
 * @internal
 */
final class NullItemL11nTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\ItemManagement\Models\NullItemL11n
     * @group framework
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\ItemManagement\Models\ItemL11n', new NullItemL11n());
    }

    /**
     * @covers Modules\ItemManagement\Models\NullItemL11n
     * @group framework
     */
    public function testId() : void
    {
        $null = new NullItemL11n(2);
        self::assertEquals(2, $null->getId());
    }
}
