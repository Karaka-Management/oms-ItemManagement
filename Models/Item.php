<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\ItemManagement\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\ItemManagement\Models;

use Modules\Editor\Models\EditorDoc;
use Modules\Media\Models\Media;
use Modules\Media\Models\NullMedia;
use phpOMS\Localization\Money;

/**
 * Account class.
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
class Item
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
     * @var int[]|ItemAttribute[]
     * @since 1.0.0
     */
    private array $attributes = [];

    private ?int $partslist = null;

    private array $purchase = [];

    private ?int $disposal = null;

    /**
     * Created at.
     *
     * @var \DateTimeImmutable
     * @since 1.0.0
     */
    public \DateTimeImmutable $createdAt;

    public string $info = '';

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
        $this->l11n[] = $l11n;
    }

    /**
     * Get l11n
     *
     * @param string $type Localization type
     *
     * @return ItemL11n
     *
     * @since 1.0.0
     */
    public function getL11n(string $type) : ItemL11n
    {
        foreach ($this->l11n as $l11n) {
            if ($l11n->type->title === $type) {
                return $l11n;
            }
        }

        return new NullItemL11n();
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
     * @return int[]|ItemAttribute[]
     *
     * @since 1.0.0
     */
    public function getAttributes() : array
    {
        return $this->attributes;
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
     * @param null|int $type Media type
     *
     * @return Media
     *
     * @since 1.0.0
     */
    public function getFileByType(int $type = null) : Media
    {
        foreach ($this->files as $file) {
            if ($file->type === $type) {
                return $file;
            }
        }

        return new NullMedia();
    }

    /**
     * Get all media files by type
     *
     * @param null|int $type Media type
     *
     * @return Media[]
     *
     * @since 1.0.0
     */
    public function getFilesByType(int $type = null) : array
    {
        $files = [];
        foreach ($this->files as $file) {
            if ($file->type === $type) {
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
