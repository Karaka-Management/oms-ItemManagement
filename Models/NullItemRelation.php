<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\ItemManagement\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\ItemManagement\Models;

/**
 * Null model
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class NullItemRelation extends ItemRelation
{
    /**
     * Constructor
     *
     * @param int $id Model id
     *
     * @since 1.0.0
     */
    public function __construct(int $id = 0)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() : mixed
    {
        return ['id' => $this->id];
    }
}
