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

use phpOMS\DataStorage\Database\DataMapperAbstract;

/**
 * Item mapper class.
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class ItemAttributeTypeMapper extends DataMapperAbstract
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    protected static array $columns = [
        'itemmgmt_attr_type_id'       => ['name' => 'itemmgmt_attr_type_id',     'type' => 'int',    'internal' => 'id'],
        'itemmgmt_attr_type_name'     => ['name' => 'itemmgmt_attr_type_name',   'type' => 'string', 'internal' => 'name', 'autocomplete' => true],
        'itemmgmt_attr_type_fields'   => ['name' => 'itemmgmt_attr_type_fields', 'type' => 'int',    'internal' => 'fields'],
        'itemmgmt_attr_type_custom'   => ['name' => 'itemmgmt_attr_type_custom', 'type' => 'bool', 'internal' => 'custom'],
        'itemmgmt_attr_type_pattern'  => ['name' => 'itemmgmt_attr_type_pattern', 'type' => 'bool', 'internal' => 'validationPattern'],
        'itemmgmt_attr_type_required' => ['name' => 'itemmgmt_attr_type_required', 'type' => 'bool', 'internal' => 'isRequired'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    protected static array $hasMany = [
        'l11n' => [
            'mapper'            => ItemAttributeTypeL11nMapper::class,
            'table'             => 'itemmgmt_attr_type_l11n',
            'self'              => 'itemmgmt_attr_type_l11n_type',
            'column'            => 'title',
            'conditional'       => true,
            'external'          => null,
        ],
        'defaults' => [
            'mapper'            => ItemAttributeValueMapper::class,
            'table'             => 'itemmgmt_item_attr_default',
            'self'              => 'itemmgmt_item_attr_default_type',
            'external'          => 'itemmgmt_item_attr_default_value',
            'conditional'       => false,
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $table = 'itemmgmt_attr_type';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $primaryField = 'itemmgmt_attr_type_id';
}
