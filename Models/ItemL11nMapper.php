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
final class ItemL11nMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'itemmgmt_item_l11n_id'          => ['name' => 'itemmgmt_item_l11n_id',          'type' => 'int',    'internal' => 'id'],
        'itemmgmt_item_l11n_description' => ['name' => 'itemmgmt_item_l11n_description', 'type' => 'string', 'internal' => 'description', 'autocomplete' => true],
        'itemmgmt_item_l11n_item'        => ['name' => 'itemmgmt_item_l11n_item',        'type' => 'int',    'internal' => 'item'],
        'itemmgmt_item_l11n_lang'        => ['name' => 'itemmgmt_item_l11n_lang',        'type' => 'string', 'internal' => 'language'],
        'itemmgmt_item_l11n_typeref'     => ['name' => 'itemmgmt_item_l11n_typeref',     'type' => 'int',    'internal' => 'type'],
    ];

    /**
     * Has one relation.
     *
     * @var array<string, array{mapper:class-string, external:string, by?:string, column?:string, conditional?:bool}>
     * @since 1.0.0
     */
    public const OWNS_ONE = [
        'type' => [
            'mapper'   => ItemL11nTypeMapper::class,
            'external' => 'itemmgmt_item_l11n_typeref',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'itemmgmt_item_l11n';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD ='itemmgmt_item_l11n_id';
}
