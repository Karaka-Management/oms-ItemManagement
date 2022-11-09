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

use Modules\ItemManagement\Models\ItemL11n;
use phpOMS\Localization\ISO639x1Enum;

/**
 * @internal
 */
final class ItemL11nTest extends \PHPUnit\Framework\TestCase
{
    private ItemL11n $l11n;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->l11n = new ItemL11n();
    }

    /**
     * @covers Modules\ItemManagement\Models\ItemL11n
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->l11n->getId());
        self::assertEquals('', $this->l11n->description);
        self::assertEquals(0, $this->l11n->item);
        self::assertEquals(ISO639x1Enum::_EN, $this->l11n->getLanguage());
        self::assertInstanceOf('Modules\ItemManagement\Models\ItemL11nType', $this->l11n->type);
    }

    /**
     * @covers Modules\ItemManagement\Models\ItemL11n
     * @group module
     */
    public function testNameInputOutput() : void
    {
        $this->l11n->description = 'TestName';
        self::assertEquals('TestName', $this->l11n->description);
    }

    /**
     * @covers Modules\ItemManagement\Models\ItemL11n
     * @group module
     */
    public function testSerialize() : void
    {
        $this->l11n->description  = 'Title';
        $this->l11n->item         = 2;
        $this->l11n->setLanguage(ISO639x1Enum::_DE);

        self::assertEquals(
            [
                'id'              => 0,
                'description'     => 'Title',
                'item'            => 2,
                'language'        => ISO639x1Enum::_DE,
            ],
            $this->l11n->jsonSerialize()
        );
    }
}
