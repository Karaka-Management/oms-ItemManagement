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
final class ItemAttributeValueL11nMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'itemmgmt_attr_value_l11n_id'     => ['name' => 'itemmgmt_attr_value_l11n_id',    'type' => 'int',    'internal' => 'id'],
        'itemmgmt_attr_value_l11n_title'  => ['name' => 'itemmgmt_attr_value_l11n_title', 'type' => 'string', 'internal' => 'title', 'autocomplete' => true],
        'itemmgmt_attr_value_l11n_value'  => ['name' => 'itemmgmt_attr_value_l11n_value',  'type' => 'int',    'internal' => 'value'],
        'itemmgmt_attr_value_l11n_lang'   => ['name' => 'itemmgmt_attr_value_l11n_lang',  'type' => 'string', 'internal' => 'language'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'itemmgmt_attr_value_l11n';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD ='itemmgmt_attr_value_l11n_id';
}
