<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\ItemManagement\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\ItemManagement\Models;

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Item mapper class.
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class ItemAttributeValueMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'itemmgmt_attr_value_id'            => ['name' => 'itemmgmt_attr_value_id',       'type' => 'int',      'internal' => 'id'],
        'itemmgmt_attr_value_default'       => ['name' => 'itemmgmt_attr_value_default',  'type' => 'bool',     'internal' => 'isDefault'],
        'itemmgmt_attr_value_valuetype'     => ['name' => 'itemmgmt_attr_value_valuetype',     'type' => 'int',      'internal' => 'type'],
        'itemmgmt_attr_value_valueStr'      => ['name' => 'itemmgmt_attr_value_valueStr', 'type' => 'string',   'internal' => 'valueStr'],
        'itemmgmt_attr_value_valueInt'      => ['name' => 'itemmgmt_attr_value_valueInt', 'type' => 'int',      'internal' => 'valueInt'],
        'itemmgmt_attr_value_valueDec'      => ['name' => 'itemmgmt_attr_value_valueDec', 'type' => 'float',    'internal' => 'valueDec'],
        'itemmgmt_attr_value_valueDat'      => ['name' => 'itemmgmt_attr_value_valueDat', 'type' => 'DateTime', 'internal' => 'valueDat'],
        'itemmgmt_attr_value_unit'          => ['name' => 'itemmgmt_attr_value_unit', 'type' => 'string', 'internal' => 'unit'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'l11n' => [
            'mapper'   => ItemAttributeValueL11nMapper::class,
            'table'    => 'itemmgmt_attr_value_l11n',
            'self'     => 'itemmgmt_attr_value_l11n_type',
            'column'   => 'title',
            'external' => null,
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'itemmgmt_attr_value';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD ='itemmgmt_attr_value_id';
}
