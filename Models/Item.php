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

use phpOMS\Localization\BaseStringL11n;
use phpOMS\Localization\NullBaseStringL11n;
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

    /**
     * Successor value.
     *
     * @var int
     * @since 1.0.0
     */
    public int $successor = 0;

    /**
     * Parent value.
     *
     * @var int|null
     * @since 1.0.0
     */
    public ?int $parent = null;

    /**
     * Status value.
     *
     * @var int
     * @since 1.0.0
     */
    public int $status = ItemStatus::ACTIVE;

    public int $stockIdentifier = StockIdentifierType::NONE;

    /**
     * Sales price value.
     *
     * @var FloatInt
     * @since 1.0.0
     */
    public FloatInt $salesPrice;

    /**
     * Purchase price value.
     *
     * @var FloatInt
     * @since 1.0.0
     */
    public FloatInt $purchasePrice;

    /**
     * Localizations.
     *
     * @var BaseStringL11n[]
     * @since 1.0.0
     */
    public array $l11n = [];

    /**
     * Parts list.
     *
     * @var int|null
     * @since 1.0.0
     */
    public ?int $partslist = null;

    /**
     * Disposal.
     *
     * @var int|null
     * @since 1.0.0
     */
    public ?int $disposal = null;

    /**
     * Created at.
     *
     * @var \DateTimeImmutable
     * @since 1.0.0
     */
    public \DateTimeImmutable $createdAt;

    /**
     * Info.
     *
     * @var string
     * @since 1.0.0
     */
    public string $info = '';

    /**
     * Unit
     *
     * @var null|int
     * @since 1.0.0
     */
    public ?int $unit = null;

    public array $container = [];

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
     * @param BaseStringL11n $l11n Item localization
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function addL11n(BaseStringL11n $l11n) : void
    {
        foreach ($this->l11n as $l11n) {
            if ($l11n->type?->title === $l11n->type?->title) {
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
     * @return BaseStringL11n
     *
     * @since 1.0.0
     */
    public function getL11n(string $type = null) : BaseStringL11n
    {
        foreach ($this->l11n as $l11n) {
            if ($l11n->type?->title === $type) {
                return $l11n;
            }
        }

        return new NullBaseStringL11n();
    }

    /**
     * Get localizations
     *
     * @return BaseStringL11n[]
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
    use \Modules\Editor\Models\EditorDocListTrait;
    use \Modules\Attribute\Models\AttributeHolderTrait;
}
