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

use Modules\Media\Models\Media;
use Modules\Media\Models\NullMedia;

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
    private string $number = '';

    private $successor = 0;

    private int $type = 0;

    /**
     * Files.
     *
     * @var int[]|Media[]
     * @since 1.0.0
     */
    private array $files = [];

    /**
     * Localizations.
     *
     * @var int[]|ItemL11n[]
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

    private $partslist = null;

    private $purchase = [];

    private $disposal = null;

    /**
     * Created at.
     *
     * @var \DateTimeImmutable
     * @since 1.0.0
     */
    private \DateTimeImmutable $createdAt;

    private $info = '';

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('now');
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
     * Get created at date time
     *
     * @return \DateTimeImmutable
     *
     * @since 1.0.0
     */
    public function getCreatedAt() : \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Set the successor item
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setSuccessor(int $successor) : void
    {
        $this->successor = $successor;
    }

    /**
     * Get the successor item
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getSuccessor() : int
    {
        return $this->successor;
    }

    /**
     * Get the item number
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function getNumber() : string
    {
        return $this->number;
    }

    /**
     * Set the item number
     *
     * @param string $number Number
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setNumber(string $number) : void
    {
        $this->number = $number;
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
            if ($l11n->getType()->getTitle() === $type) {
                return $l11n;
            }
        }

        return new NullItemL11n();
    }

    /**
     * Add media to item
     *
     * @param int|Media $media Media
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function addFile($media) : void
    {
        $this->files[] = $media;
    }

    /**
     * Get media file by type
     *
     * @param string $type Media type
     *
     * @return Media
     *
     * @since 1.0.0
     */
    public function getFileByType(string $type) : Media
    {
        foreach ($this->files as $file) {
            if ($file->getType() === $type) {
                return $file;
            }
        }

        return new NullMedia();
    }

    /**
     * Get all media files by type
     *
     * @param string $type Media type
     *
     * @return Media[]
     *
     * @since 1.0.0
     */
    public function getFilesByType(string $type) : array
    {
        $files = [];
        foreach ($this->files as $file) {
            if ($file->getType() === $type) {
                $files[] = $file;
            }
        }

        return $files;
    }
}
