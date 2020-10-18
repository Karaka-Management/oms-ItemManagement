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
use phpOMS\Localization\ISO3166TwoEnum;
use phpOMS\Localization\ISO639x1Enum;

/**
 * Item class.
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
class ItemAttributeValue implements \JsonSerializable, ArrayableInterface
{
    protected int $id = 0;

    protected $type = 0;

    protected ?int $valueInt = null;

    protected ?string $valueStr = null;

    protected ?float $valueDec = null;

    protected ?\DateTime $valueDat = null;

    protected bool $isDefault = false;

    protected string $language = ISO639x1Enum::_EN;

    protected string $country = ISO3166TwoEnum::_USA;

    /**
     * Constructor.
     *
     * @param string $description Title
     *
     * @since 1.0.0
     */
    public function __construct($type = 0, $value = '', string $language = ISO639x1Enum::_EN)
    {
        $this->type  = $type;

        if (\is_string($value)) {
            $this->valueStr = $value;
        } elseif (\is_int($value)) {
            $this->valueInt = $value;
        } elseif (\is_float($value)) {
            $this->valueDec = $value;
        } elseif ($value instanceof \DateTimeInterface) {
            $this->valueDat = $value;
        }

        $this->language    = $language;
    }

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
