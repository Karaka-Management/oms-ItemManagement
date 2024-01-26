<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\ItemManagement\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\ItemManagement\Models;

/**
 * Item relation
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class ItemRelation implements \JsonSerializable
{
    /**
     * Article ID.
     *
     * @var int
     * @since 1.0.0
     */
    public int $id = 0;

    /**
     * Item source.
     *
     * @var int
     * @since 1.0.0
     */
    public int $source = 0;

    /**
     * Item destination.
     *
     * @var int
     * @since 1.0.0
     */
    public int $destination = 0;

    /**
     * Relation type.
     *
     * @var ItemRelationType
     * @since 1.0.0
     */
    public ItemRelationType $type;

    /**
     * Constructor.
     *
     * @param ItemRelationType $type Item relation type
     *
     * @since 1.0.0
     */
    public function __construct(?ItemRelationType $type = null)
    {
        $this->type = $type ?? new ItemRelationType();
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id'          => $this->id,
            'source'      => $this->source,
            'destination' => $this->destination,
            'type'        => $this->type,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() : mixed
    {
        return $this->toArray();
    }
}
