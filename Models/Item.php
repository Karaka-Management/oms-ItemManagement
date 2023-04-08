<?php
/**
 * Karaka
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

use Modules\Editor\Models\EditorDoc;
use Modules\Media\Models\Media;
use Modules\Media\Models\NullMedia;
use phpOMS\Localization\Money;

/**
 * Item class.
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class Item implements \JsonSerializable
{
    /**
     * ID.
     *
     * @var int
     * @since 1.0.0
     */
    protected int $id = 0;

    /**
     * Item number/id
     *
     * @var string
     * @since 1.0.0
     */
    public string $number = '';

    public int $successor = 0;

    public ?int $parent = null;

    private int $status = ItemStatus::ACTIVE;

    public Money $salesPrice;

    public Money $purchasePrice;

    /**
     * Files.
     *
     * @var Media[]
     * @since 1.0.0
     */
    private array $files = [];

    /**
     * Files.
     *
     * @var EditorDoc[]
     * @since 1.0.0
     */
    private array $notes = [];

    /**
     * Localizations.
     *
     * @var ItemL11n[]
     * @since 1.0.0
     */
    private array $l11n = [];

    /**
     * Attributes.
     *
     * @var ItemAttribute[]
     * @since 1.0.0
     */
    private array $attributes = [];

    public ?int $partslist = null;

    public ?int $disposal = null;

    /**
     * Created at.
     *
     * @var \DateTimeImmutable
     * @since 1.0.0
     */
    public \DateTimeImmutable $createdAt;

    public string $info = '';

    /**
     * Unit
     *
     * @var null|int
     * @since 1.0.0
     */
    public ?int $unit = null;

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->createdAt     = new \DateTimeImmutable('now');
        $this->salesPrice    = new Money();
        $this->purchasePrice = new Money();
    }

    /**
     * Get id.
     *
     * @return int Model id
     *
     * @since 1.0.0
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Add item l11n
     *
     * @param ItemL11n $l11n Item localization
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function addL11n(ItemL11n $l11n) : void
    {
        foreach ($this->l11n as $l11n) {
            if ($l11n->type->title === $l11n->type->title) {
                return;
            }
        }

        $this->l11n[] = $l11n;
    }

    /**
     * Get l11n
     *
     * @param null|string $type Localization type
     *
     * @return ItemL11n
     *
     * @since 1.0.0
     */
    public function getL11n(string $type = null) : ItemL11n
    {
        foreach ($this->l11n as $l11n) {
            if ($l11n->type->title === $type) {
                return $l11n;
            }
        }

        return new NullItemL11n();
    }

    /**
     * Get localizations
     *
     * @return ItemL11n[]
     *
     * @since 1.0.0
     */
    public function getL11ns() : array
    {
        return $this->l11n;
    }

    /**
     * Get status.
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
     * Set status.
     *
     * @param int $status Status
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setStatus(int $status) : void
    {
        $this->status = $status;
    }

    /**
     * Add media to item
     *
     * @param Media $media Media
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function addFile(Media $media) : void
    {
        $this->files[] = $media;
    }

    /**
     * Add attribute to item
     *
     * @param ItemAttribute $attribute Note
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function addAttribute(ItemAttribute $attribute) : void
    {
        $this->attributes[] = $attribute;
    }

    /**
     * Get attributes
     *
     * @return ItemAttribute[]
     *
     * @since 1.0.0
     */
    public function getAttributes() : array
    {
        return $this->attributes;
    }

    /**
     * Has attribute value
     *
     * @param string $attrName  Attribute name
     * @param mixed  $attrValue Attribute value
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public function hasAttributeValue(string $attrName, mixed $attrValue) : bool
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->type->name === $attrName && $attribute->value->getValue() === $attrValue) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get attribute
     *
     * @param string $attrName Attribute name
     *
     * @return null|ItemAttribute
     *
     * @since 1.0.0
     */
    public function getAttribute(string $attrName) : ?ItemAttribute
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->type->name === $attrName) {
                return $attribute;
            }
        }

        return null;
    }

    /**
     * Add note to item
     *
     * @param EditorDoc $note Note
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function addNote(EditorDoc $note) : void
    {
        $this->notes[] = $note;
    }

    /**
     * Get notes
     *
     * @return EditorDoc[]
     *
     * @since 1.0.0
     */
    public function getNotes() : array
    {
        return $this->notes;
    }

    /**
     * Get files
     *
     * @return Media[]
     *
     * @since 1.0.0
     */
    public function getFiles() : array
    {
        return $this->files;
    }

    /**
     * Get media file by type
     *
     * @param int $type Media type
     *
     * @return Media
     *
     * @since 1.0.0
     */
    public function getFileByType(int $type) : Media
    {
        foreach ($this->files as $file) {
            if ($file->hasMediaTypeId($type)) {
                return $file;
            }
        }

        return new NullMedia();
    }

    /**
     * Get all media files by type name
     *
     * @param string $type Media type
     *
     * @return Media
     *
     * @since 1.0.0
     */
    public function getFileByTypeName(string $type) : Media
    {
        foreach ($this->files as $file) {
            if ($file->hasMediaTypeName($type)) {
                return $file;
            }
        }

        return new NullMedia();
    }

    /**
     * Get all media files by type name
     *
     * @param string $type Media type
     *
     * @return Media[]
     *
     * @since 1.0.0
     */
    public function getFilesByTypeName(string $type) : array
    {
        $files = [];
        foreach ($this->files as $file) {
            if ($file->hasMediaTypeName($type)) {
                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id'     => $this->id,
            'number' => $this->number,
            'status' => $this->status,
            'info'   => $this->info,
            'l11n'   => $this->l11n,
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
