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
 * Item mapper class.
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of Material
 * @extends DataMapperFactory<T>
 */
final class MaterialMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'itemmgmt_material_id'          => ['name' => 'itemmgmt_material_id',       'type' => 'int',    'internal' => 'id'],
        'itemmgmt_material_category'    => ['name' => 'itemmgmt_material_category',     'type' => 'int', 'internal' => 'category'],
        'itemmgmt_material_subcategory' => ['name' => 'itemmgmt_material_subcategory',     'type' => 'int', 'internal' => 'subcategory'],
        'itemmgmt_material_type'        => ['name' => 'itemmgmt_material_type',     'type' => 'int', 'internal' => 'type'],
        'itemmgmt_material_unit'        => ['name' => 'itemmgmt_material_unit',     'type' => 'int', 'internal' => 'unit'],
        'itemmgmt_material_item'        => ['name' => 'itemmgmt_material_item',     'type' => 'int', 'internal' => 'item'],
    ];

    /**
     * Has one relation.
     *
     * @var array<string, array{mapper:class-string, external:string, by?:string, column?:string, conditional?:bool}>
     * @since 1.0.0
     */
    public const BELONGS_TO = [
        'type' => [
            'mapper'   => MaterialTypeMapper::class,
            'external' => 'itemmgmt_material_type',
        ],
        'item' => [
            'mapper'   => ItemMapper::class,
            'external' => 'itemmgmt_material_item',
        ],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var class-string<T>
     * @since 1.0.0
     */
    public const MODEL = Material::class;

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'itemmgmt_material';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'itemmgmt_material_id';
}
