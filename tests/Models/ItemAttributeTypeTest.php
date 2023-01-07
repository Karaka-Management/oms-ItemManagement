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
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\ItemManagement\tests\Models;

use Modules\ItemManagement\Models\ItemAttributeType;
use phpOMS\Localization\BaseStringL11n;

/**
 * @internal
 */
final class ItemAttributeTypeTest extends \PHPUnit\Framework\TestCase
{
    private ItemAttributeType $attr;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->attr = new ItemAttributeType();
    }

    /**
     * @covers Modules\ItemManagement\Models\ItemAttributeType
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->attr->getId());
        self::assertEquals('', $this->attr->name);
        self::assertEquals('', $this->attr->validationPattern);
        self::assertFalse($this->attr->custom);
        self::assertFalse($this->attr->isRequired);
        self::assertEquals('', $this->attr->getL11n());
    }

    /**
     * @covers Modules\ItemManagement\Models\ItemAttributeType
     * @group module
     */
    public function testL11nInputOutput() : void
    {
        $this->attr->setL11n('Test');
        self::assertEquals('Test', $this->attr->getL11n());

        $this->attr->setL11n(new BaseStringL11n('NewTest'));
        self::assertEquals('NewTest', $this->attr->getL11n());
    }

    /**
     * @covers Modules\ItemManagement\Models\ItemAttributeType
     * @group module
     */
    public function testSerialize() : void
    {
        $this->attr->name              = 'Test';
        $this->attr->validationPattern = 'Pattern';
        $this->attr->custom            = true;
        $this->attr->isRequired        = true;

        self::assertEquals(
            [
                'id'                => 0,
                'name'              => 'Test',
                'validationPattern' => 'Pattern',
                'custom'            => true,
                'isRequired'        => true,
            ],
            $this->attr->jsonSerialize()
        );
    }
}
