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

use Modules\ItemManagement\Models\ItemL11nType;

/**
 * @internal
 */
final class ItemL11nTypeTest extends \PHPUnit\Framework\TestCase
{
    private ItemL11nType $l11n;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->l11n = new ItemL11nType();
    }

    /**
     * @covers Modules\ItemManagement\Models\ItemL11nType
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->l11n->id);
        self::assertEquals('', $this->l11n->title);
    }

    /**
     * @covers Modules\ItemManagement\Models\ItemL11nType
     * @group module
     */
    public function testTitleInputOutput() : void
    {
        $this->l11n->title = 'TestName';
        self::assertEquals('TestName', $this->l11n->title);
    }

    /**
     * @covers Modules\ItemManagement\Models\ItemL11nType
     * @group module
     */
    public function testSerialize() : void
    {
        $this->l11n->title  = 'Title';

        self::assertEquals(
            [
                'id'        => 0,
                'title'     => 'Title',
            ],
            $this->l11n->jsonSerialize()
        );
    }
}
