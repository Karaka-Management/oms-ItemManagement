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

use Modules\ItemManagement\Models\NullItemAttributeType;

/**
 * @internal
 */
final class NullItemAttributeTypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\ItemManagement\Models\NullItemAttributeType
     * @group framework
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\ItemManagement\Models\ItemAttributeType', new NullItemAttributeType());
    }

    /**
     * @covers Modules\ItemManagement\Models\NullItemAttributeType
     * @group framework
     */
    public function testId() : void
    {
        $null = new NullItemAttributeType(2);
        self::assertEquals(2, $null->getId());
    }
}
