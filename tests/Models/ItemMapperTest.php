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

use Modules\ItemManagement\Models\Item;
use Modules\ItemManagement\Models\ItemMapper;

/**
 * @internal
 */
final class ItemMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Modules\ItemManagement\Models\ItemMapper
     * @group module
     */
    public function testCR() : void
    {
        $item         = new Item();
        $item->number = '123456789';

        $id = ItemMapper::create()->execute($item);
        self::assertGreaterThan(0, $item->id);
        self::assertEquals($id, $item->id);
    }
}
