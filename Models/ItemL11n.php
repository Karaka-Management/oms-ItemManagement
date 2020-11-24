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
 * Localization of the item class.
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
class ItemL11n implements \JsonSerializable, ArrayableInterface
{
    /**
     * Article ID.
     *
     * @var int
     * @since 1.0.0
     */
    protected int $id = 0;

    /**
     * Item ID.
     *
     * @var int
     * @since 1.0.0
     */
    protected int $item = 0;

    /**
     * Item ID.
     *
     * @var int|ItemL11nType
     * @since 1.0.0
     */
    protected $type = 0;

    /**
     * Language.
     *
     * @var string
     * @since 1.0.0
     */
    protected string $language = ISO639x1Enum::_EN;

    /**
     * Title.
     *
     * @var string
     * @since 1.0.0
     */
    public string $description = '';

    /**
     * Constructor.
     *
     * @param int|ItemL11nType $type        Item localization type
     * @param string           $description Description/content
     * @param string           $language    Language
     *
     * @since 1.0.0
     */
    public function __construct($type = 0, string $description = '', string $language = ISO639x1Enum::_EN)
    {
        $this->type        = $type;
        $this->description = $description;
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
     * Set item.
     *
     * @param int $item Item id
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setItem(int $item) : void
    {
        $this->item = $item;
    }

    /**
     * Get item
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getItem() : int
    {
        return $this->item;
    }

    /**
     * Set type.
     *
     * @param int|ItemL11nType $type Item type
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
     * Get type
     *
     * @return int|ItemL11nType
     *
     * @since 1.0.0
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get language
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function getLanguage() : string
    {
        return $this->language;
    }

    /**
     * Set language
     *
     * @param string $language Language
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setLanguage(string $language) : void
    {
        $this->language = $language;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id'             => $this->id,
            'description'    => $this->description,
            'item'           => $this->item,
            'language'       => $this->language,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
