<?php
/**
 * Karaka
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
 */
final class ItemRelationMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'itemmgmt_item_relation_id'          => ['name' => 'itemmgmt_item_relation_id',          'type' => 'int',    'internal' => 'id'],
        'itemmgmt_item_relation_src'         => ['name' => 'itemmgmt_item_relation_src',        'type' => 'int',    'internal' => 'source'],
        'itemmgmt_item_relation_dst'         => ['name' => 'itemmgmt_item_relation_dst',        'type' => 'int',    'internal' => 'destination'],
        'itemmgmt_item_relation_type'        => ['name' => 'itemmgmt_item_relation_type',     'type' => 'int',    'internal' => 'type'],
    ];

    /**
     * Has one relation.
     *
     * @var array<string, array{mapper:class-string, external:string, by?:string, column?:string, conditional?:bool}>
     * @since 1.0.0
     */
    public const OWNS_ONE = [
        'type' => [
            'mapper'   => ItemRelationTypeMapper::class,
            'external' => 'itemmgmt_item_relation_type',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'itemmgmt_item_relation';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'itemmgmt_item_relation_id';
}
