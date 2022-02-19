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

use Modules\ItemManagement\Models\NullItem;

/**
 * @internal
 */
final class NullItemTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\ItemManagement\Models\NullItem
     * @group framework
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\ItemManagement\Models\Item', new NullItem());
    }

    /**
     * @covers Modules\ItemManagement\Models\NullItem
     * @group framework
     */
    public function testId() : void
    {
        $null = new NullItem(2);
        self::assertEquals(2, $null->getId());
    }
}
