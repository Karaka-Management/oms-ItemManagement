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
use phpOMS\Localization\ISO639x1Enum;

/**
 * Item Attribute Type class.
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
class ItemAttributeType implements \JsonSerializable, ArrayableInterface
{
    /**
     * Id
     *
     * @var int
     * @since 1.0.0
     */
    protected int $id = 0;

    /**
     * Name/string identifier by which it can be found/categorized
     *
     * @var string
     * @since 1.0.0
     */
    protected string $name = '';

    /**
     * Which field data type is required (string, int, ...) in the value
     *
     * @var int
     * @since 1.0.0
     */
    protected int $fields = 0;

    /**
     * Is a custom value allowed (e.g. custom string)
     *
     * @var bool
     * @since 1.0.0
     */
    protected bool $custom = false;

    /**
     * Localization
     *
     * @var int|int[]|ItemAttributeTypeL11n|ItemAttributeTypeL11n[]
     */
    protected $l11n = 0;

    /**
     * Possible default attribute values
     *
     * @var array
     */
    protected array $defaults = [];

    /**
     * Constructor.
     *
     * @param string $name Name/identifier of the attribute type
     *
     * @since 1.0.0
     */
    public function __construct(string $name = '')
    {
        $this->name = $name;
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
     * Set name
     *
     * @param string $name Name
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    /**
     * Set l11n
     *
     * @param string|ItemAttributeTypeL11n $l11n Tag article l11n
     * @param string                       $lang Language
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setL11n($l11n, string $lang = ISO639x1Enum::_EN) : void
    {
        if ($l11n instanceof ItemAttributeTypeL11n) {
            $this->l11n = $l11n;
        } elseif ($this->l11n instanceof ItemAttributeTypeL11n && \is_string($l11n)) {
            $this->l11n->setl11n($l11n);
        } elseif (\is_string($l11n)) {
            $this->l11n = new ItemAttributeTypeL11n();
            $this->l11n->setl11n($l11n);
            $this->l11n->setLanguage($lang);
        }
    }

    /**
     * Set fields
     *
     * @param int $fields Fields
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setFields(int $fields) : void
    {
        $this->fields = $fields;
    }

    /**
     * Set custom
     *
     * @param bool $custom FieldsCustom
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setCustom(bool $custom) : void
    {
        $this->custom = $custom;
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
