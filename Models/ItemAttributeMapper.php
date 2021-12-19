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

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Item mapper class.
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class ItemAttributeMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'itemmgmt_item_attr_id'    => ['name' => 'itemmgmt_item_attr_id',    'type' => 'int', 'internal' => 'id'],
        'itemmgmt_item_attr_item'  => ['name' => 'itemmgmt_item_attr_item',  'type' => 'int', 'internal' => 'item'],
        'itemmgmt_item_attr_type'  => ['name' => 'itemmgmt_item_attr_type',  'type' => 'int', 'internal' => 'type'],
        'itemmgmt_item_attr_value' => ['name' => 'itemmgmt_item_attr_value', 'type' => 'int', 'internal' => 'value'],
    ];

    /**
     * Has one relation.
     *
     * @var array<string, array{mapper:string, external:string, by?:string, column?:string, conditional?:bool}>
     * @since 1.0.0
     */
    public const OWNS_ONE = [
        'type' => [
            'mapper'   => ItemAttributeTypeMapper::class,
            'external' => 'itemmgmt_item_attr_type',
        ],
        'value' => [
            'mapper'   => ItemAttributeValueMapper::class,
            'external' => 'itemmgmt_item_attr_value',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'itemmgmt_item_attr';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD ='itemmgmt_item_attr_id';
}
