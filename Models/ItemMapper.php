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

use Modules\Editor\Models\EditorDocMapper;
use Modules\Media\Models\MediaMapper;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Item mapper class.
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of Item
 * @extends DataMapperFactory<T>
 */
final class ItemMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'itemmgmt_item_id'            => ['name' => 'itemmgmt_item_id',            'type' => 'int',          'internal' => 'id'],
        'itemmgmt_item_no'            => ['name' => 'itemmgmt_item_no',            'type' => 'string',       'internal' => 'number', 'autocomplete' => true],
        'itemmgmt_item_status'        => ['name' => 'itemmgmt_item_status',        'type' => 'int',          'internal' => 'status'],
        'itemmgmt_item_info'          => ['name' => 'itemmgmt_item_info',          'type' => 'string',       'internal' => 'info'],
        'itemmgmt_item_salesprice'    => ['name' => 'itemmgmt_item_salesprice',    'type' => 'Serializable', 'internal' => 'salesPrice'],
        'itemmgmt_item_purchaseprice' => ['name' => 'itemmgmt_item_purchaseprice', 'type' => 'Serializable', 'internal' => 'purchasePrice'],
        'itemmgmt_item_parent'        => ['name' => 'itemmgmt_item_parent', 'type' => 'int', 'internal' => 'parent'],
        'itemmgmt_item_unit'          => ['name' => 'itemmgmt_item_unit', 'type' => 'int', 'internal' => 'unit'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'itemmgmt_item';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'itemmgmt_item_id';

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:class-string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'files' => [
            'mapper'   => MediaMapper::class,            /* mapper of the related object */
            'table'    => 'itemmgmt_item_media',         /* table of the related object, null if no relation table is used (many->1) */
            'external' => 'itemmgmt_item_media_media',
            'self'     => 'itemmgmt_item_media_item',
        ],
        'notes' => [
            'mapper'   => EditorDocMapper::class,       /* mapper of the related object */
            'table'    => 'itemmgmt_item_note',         /* table of the related object, null if no relation table is used (many->1) */
            'external' => 'itemmgmt_item_note_doc',
            'self'     => 'itemmgmt_item_note_item',
        ],
        'l11n' => [
            'mapper'   => ItemL11nMapper::class,
            'table'    => 'itemmgmt_item_l11n',
            'self'     => 'itemmgmt_item_l11n_item',
            'external' => null,
        ],
        'attributes' => [
            'mapper'   => ItemAttributeMapper::class,
            'table'    => 'itemmgmt_item_attr',
            'self'     => 'itemmgmt_item_attr_item',
            'external' => null,
        ],
    ];
}
