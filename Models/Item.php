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
use phpOMS\Stdlib\Base\FloatInt;

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
    public int $id = 0;

    /**
     * Item number/id
     *
     * @var string
     * @since 1.0.0
     */
    public string $number = '';

    public int $successor = 0;

    public ?int $parent = null;

    public int $status = ItemStatus::ACTIVE;

    public FloatInt $salesPrice;

    public FloatInt $purchasePrice;

    /**
     * Notes.
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
        $this->salesPrice    = new FloatInt();
        $this->purchasePrice = new FloatInt();
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

    use \Modules\Media\Models\MediaListTrait;
    use \Modules\Attribute\Models\AttributeHolderTrait;
}
