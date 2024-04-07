<?php
/**
 * Jingga
 *
 * PHP Version 8.2
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
use phpOMS\Localization\BaseStringL11nType;

/**
 * MaterialType mapper class.
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of BaseStringL11nType
 * @extends DataMapperFactory<T>
 */
final class MaterialTypeMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'itemmgmt_material_type_id'   => ['name' => 'itemmgmt_material_type_id',       'type' => 'int',    'internal' => 'id'],
        'itemmgmt_material_type_name' => ['name' => 'itemmgmt_material_type_name',     'type' => 'string', 'internal' => 'title', 'autocomplete' => true],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:class-string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'l11n' => [
            'mapper'   => MaterialTypeL11nMapper::class,
            'table'    => 'itemmgmt_material_type_l11n',
            'self'     => 'itemmgmt_material_type_l11n_type',
            'column'   => 'content',
            'external' => null,
        ],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var class-string<T>
     * @since 1.0.0
     */
    public const MODEL = BaseStringL11nType::class;

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'itemmgmt_material_type';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'itemmgmt_material_type_id';
}
