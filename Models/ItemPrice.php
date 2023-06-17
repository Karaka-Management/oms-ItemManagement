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

use phpOMS\Stdlib\Base\Exception\InvalidEnumValue;
use phpOMS\Stdlib\Base\FloatInt;

/**
 * Account class.
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class ItemPrice implements \JsonSerializable
{
    /**
     * ID.
     *
     * @var int
     * @since 1.0.0
     */
    public int $id = 0;

    public string $name = '';

    public string $currency = '';

    public FloatInt $price;

    public int $status = ItemPriceStatus::ACTIVE;

    public int $minQuantity = 0;

    public int $relativeDiscount = 0;

    public int $absoluteDiscount = 0;

    public int $relativeUnitDiscount = 0;

    public int $absoluteUnitDiscount = 0;

    public string $promocode = '';

    public ?\DateTime $start = null;

    public ?\DateTime $end = null;

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
    }

    /**
     * Get status
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getStatus() : int
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param int $status Price status
     *
     * @return void
     *
     * @throws InvalidEnumValue
     *
     * @since 1.0.0
     */
    public function setStatus(int $status) : void
    {
        if (!ItemPriceStatus::isValidValue($status)) {
            throw new InvalidEnumValue((string) $status);
        }

        $this->status = $status;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id'                             => $this->id,
            'name'                           => $this->name,
            'currency'                       => $this->currency,
            'price'                          => $this->price,
            'status'                         => $this->status,
            'minQuantity'                    => $this->minQuantity,
            'relativeDiscount'               => $this->relativeDiscount,
            'absoluteDiscount'               => $this->absoluteDiscount,
            'relativeUnitDiscount'           => $this->relativeUnitDiscount,
            'absoluteUnitDiscount'           => $this->absoluteUnitDiscount,
            'promocode'                      => $this->promocode,
            'start'                          => $this->start,
            'end'                            => $this->end,
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
