<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\ItemManagement\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\ItemManagement\Models;

/**
 * Container class.
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @todo Fully implement different containers for items (Gebinde)
 *      https://github.com/Karaka-Management/oms-ItemManagement/issues/17
 *
 * @todo Item Containers can be Supplier and Client specific.
 *      Currently they are only handled on item level not in conjunction with clients/suppliers.
 *      https://github.com/Karaka-Management/oms-ItemManagement/issues/18
 *
 * @todo Item Containers have an effect on the package materials
 *      https://github.com/Karaka-Management/oms-ItemManagement/issues/19
 */
class Container implements \JsonSerializable
{
    /**
     * ID.
     *
     * @var int
     * @since 1.0.0
     */
    public int $id = 0;

    public string $name = '';

    public string $unit = 'pcs';

    public int $quantity = 0;

    public int $quantityDecimals = 0;

    public int $weight = 0;

    public int $width = 0;

    public int $height = 0;

    public int $length = 0;

    public int $volume = 0;

    public int $item = 0;

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id' => $this->id,
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
