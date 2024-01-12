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

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Container mapper class.
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of Item
 * @extends DataMapperFactory<T>
 */
final class ContainerMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'itemmgmt_item_container_id'            => ['name' => 'itemmgmt_item_container_id',            'type' => 'int',          'internal' => 'id'],
        'itemmgmt_item_container_name'            => ['name' => 'itemmgmt_item_container_name',            'type' => 'string',       'internal' => 'name', 'autocomplete' => true],
        'itemmgmt_item_container_quantity'        => ['name' => 'itemmgmt_item_container_quantity',        'type' => 'int',          'internal' => 'quantity'],
        'itemmgmt_item_container_weight'        => ['name' => 'itemmgmt_item_container_weight',        'type' => 'int',          'internal' => 'weight'],
        'itemmgmt_item_container_width'        => ['name' => 'itemmgmt_item_container_width',        'type' => 'int',          'internal' => 'width'],
        'itemmgmt_item_container_height'        => ['name' => 'itemmgmt_item_container_height',        'type' => 'int',          'internal' => 'height'],
        'itemmgmt_item_container_length'        => ['name' => 'itemmgmt_item_container_length',        'type' => 'int',          'internal' => 'length'],
        'itemmgmt_item_container_volume'        => ['name' => 'itemmgmt_item_container_volume',        'type' => 'int',          'internal' => 'volume'],
        'itemmgmt_item_container_item'        => ['name' => 'itemmgmt_item_container_item',        'type' => 'int',          'internal' => 'item'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'itemmgmt_item_container';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'itemmgmt_item_container_id';
}
