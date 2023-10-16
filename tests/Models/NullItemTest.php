<?php
/**
 * Jingga
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

use Modules\ItemManagement\Models\NullItem;

/**
 * @internal
 */
final class NullItemTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\ItemManagement\Models\NullItem
     * @group module
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\ItemManagement\Models\Item', new NullItem());
    }

    /**
     * @covers Modules\ItemManagement\Models\NullItem
     * @group module
     */
    public function testId() : void
    {
        $null = new NullItem(2);
        self::assertEquals(2, $null->id);
    }

    /**
     * @covers Modules\ItemManagement\Models\NullItem
     * @group module
     */
    public function testJsonSerialize() : void
    {
        $null = new NullItem(2);
        self::assertEquals(['id' => 2], $null);
    }
}
