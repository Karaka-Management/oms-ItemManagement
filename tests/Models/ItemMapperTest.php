<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
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
     * @group volume
     * @group module
     * @coversNothing
     */
    public function testItemVolume() : void
    {
        for ($i = 0; $i < 100; ++$i) {
            $item = new Item();
            $item->setNumber((string) \mt_rand(100000, 999999));

            ItemMapper::create($item);
        }
    }
}
