<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   Modules\ItemManagement\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\ItemManagement\Models;

use phpOMS\Contract\ArrayableInterface;

/**
 * Item class.
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
class ItemAttribute implements \JsonSerializable, ArrayableInterface
{
    /**
     * Id.
     *
     * @var int
     * @since 1.0.0
     */
    protected int $id = 0;

    /**
     * Item this attribute belongs to
     *
     * @var int
     * @since 1.0.0
     */
    protected int $item = 0;

    /**
     * Attribute type the attribute belongs to
     *
     * @var int|ItemAttributeType
     * @since 1.0.0
     */
    protected $type = 0;

    /**
     * Attribute value the attribute belongs to
     *
     * @var int|ItemAttributeValue
     * @since 1.0.0
     */
    protected $value = 0;

    /**
     * Get id
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param int|ItemAttributeType $type Type
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setType($type) : void
    {
        $this->type = $type;
    }

    /**
     * Set value
     *
     * @param int|ItemAttributeValue $type Type
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setValue($value) : void
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
