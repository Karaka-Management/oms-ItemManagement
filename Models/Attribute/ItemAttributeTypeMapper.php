<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\ItemManagement\Models\Attribute
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\ItemManagement\Models\Attribute;

use Modules\Attribute\Models\AttributeType;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Item mapper class.
 *
 * @package Modules\ItemManagement\Models\Attribute
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of AttributeType
 * @extends DataMapperFactory<T>
 */
final class ItemAttributeTypeMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'itemmgmt_attr_type_id'         => ['name' => 'itemmgmt_attr_type_id',       'type' => 'int',    'internal' => 'id'],
        'itemmgmt_attr_type_name'       => ['name' => 'itemmgmt_attr_type_name',     'type' => 'string', 'internal' => 'name', 'autocomplete' => true],
        'itemmgmt_attr_type_datatype'   => ['name' => 'itemmgmt_attr_type_datatype',   'type' => 'int',    'internal' => 'datatype'],
        'itemmgmt_attr_type_fields'     => ['name' => 'itemmgmt_attr_type_fields',   'type' => 'int',    'internal' => 'fields'],
        'itemmgmt_attr_type_custom'     => ['name' => 'itemmgmt_attr_type_custom',   'type' => 'bool',   'internal' => 'custom'],
        'itemmgmt_attr_type_repeatable' => ['name' => 'itemmgmt_attr_type_repeatable',   'type' => 'bool',   'internal' => 'repeatable'],
        'itemmgmt_attr_type_internal'   => ['name' => 'itemmgmt_attr_type_internal',   'type' => 'bool',   'internal' => 'isInternal'],
        'itemmgmt_attr_type_pattern'    => ['name' => 'itemmgmt_attr_type_pattern',  'type' => 'string', 'internal' => 'validationPattern'],
        'itemmgmt_attr_type_required'   => ['name' => 'itemmgmt_attr_type_required', 'type' => 'bool',   'internal' => 'isRequired'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:class-string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'l11n' => [
            'mapper'   => ItemAttributeTypeL11nMapper::class,
            'table'    => 'itemmgmt_attr_type_l11n',
            'self'     => 'itemmgmt_attr_type_l11n_type',
            'column'   => 'content',
            'external' => null,
        ],
        'defaults' => [
            'mapper'   => ItemAttributeValueMapper::class,
            'table'    => 'itemmgmt_item_attr_default',
            'self'     => 'itemmgmt_item_attr_default_type',
            'external' => 'itemmgmt_item_attr_default_value',
        ],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var class-string<T>
     * @since 1.0.0
     */
    public const MODEL = AttributeType::class;

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'itemmgmt_attr_type';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'itemmgmt_attr_type_id';
}
