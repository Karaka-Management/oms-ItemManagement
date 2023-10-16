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

use Modules\ItemManagement\Models\NullItemRelation;

/**
 * @internal
 */
final class NullItemRelationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\ItemManagement\Models\NullItemRelation
     * @group framework
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\ItemManagement\Models\ItemRelation', new NullItemRelation());
    }

    /**
     * @covers Modules\ItemManagement\Models\NullItemRelation
     * @group framework
     */
    public function testId() : void
    {
        $null = new NullItemRelation(2);
        self::assertEquals(2, $null->getId());
    }

    /**
     * @covers Modules\ItemManagement\Models\NullItemRelation
     * @group framework
     */
    public function testJsonSerialize() : void
    {
        $null = new NullItemRelation(2);
        self::assertEquals(['id' => 2], $null);
    }
}
