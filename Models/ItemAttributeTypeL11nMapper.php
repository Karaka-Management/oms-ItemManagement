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
final class ItemAttributeTypeL11nMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'itemmgmt_attr_type_l11n_id'    => ['name' => 'itemmgmt_attr_type_l11n_id',    'type' => 'int',    'internal' => 'id'],
        'itemmgmt_attr_type_l11n_title' => ['name' => 'itemmgmt_attr_type_l11n_title', 'type' => 'string', 'internal' => 'title', 'autocomplete' => true],
        'itemmgmt_attr_type_l11n_type'  => ['name' => 'itemmgmt_attr_type_l11n_type',  'type' => 'int',    'internal' => 'type'],
        'itemmgmt_attr_type_l11n_lang'  => ['name' => 'itemmgmt_attr_type_l11n_lang',  'type' => 'string', 'internal' => 'language'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'itemmgmt_attr_type_l11n';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD ='itemmgmt_attr_type_l11n_id';
}
