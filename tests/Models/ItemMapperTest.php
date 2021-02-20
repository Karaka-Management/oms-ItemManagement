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

use Modules\ItemManagement\Models\Item;
use Modules\ItemManagement\Models\ItemMapper;

/**
 * @internal
 */
class ItemMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\ItemManagement\Models\ItemMapper
     * @group module
     */
    public function testCR() : void
    {
        $item         = new Item();
        $item->number = '123456789';

        $id = ItemMapper::create($item);
        self::assertGreaterThan(0, $item->getId());
        self::assertEquals($id, $item->getId());
    }

    /**
     * @group volume
     * @group module
     * @coversNothing
     */
    public function testItemVolume() : void
    {
        for ($i = 0; $i < 100; ++$i) {
            $item         = new Item();
            $item->number = (string) \mt_rand(100000, 999999);

            ItemMapper::create($item);
        }
    }
}
